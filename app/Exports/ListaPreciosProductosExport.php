<?php

namespace App\Exports;

use App\Models\Producto;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ListaPreciosProductosExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithStyles, WithEvents, WithTitle, WithCustomValueBinder
{
    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'A' && $cell->getRow() > 1) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function title(): string
    {
        return 'Lista de precios';
    }

    public function collection(): Collection
    {
        return Producto::with(['tipo', 'marcas', 'modelos.marca'])
            ->where('visible', true)
            ->orderByRaw("CASE WHEN (orden IS NULL OR orden = '') THEN 1 ELSE 0 END ASC")
            ->orderBy('orden')
            ->orderBy('codigo_ralux')
            ->get()
            ->map(function (Producto $producto) {
                $precioLista = (float) ($producto->precio ?? 0);

                return [
                    $producto->codigo_ralux,
                    $this->normalizarTexto($producto->descripcion_es),
                    $precioLista ?: null,
                ];
            });
    }

    private function normalizarTexto(?string $value): string
    {
        $text = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);
        $text = str_replace("\xc2\xa0", ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text ?? '');
    }

    public function headings(): array
    {
        return [
            'Codigo',
            'Descripcion',
            'Precio lista',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'AD0369']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:C{$highestRow}");

                $sheet->getStyle("A1:C{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB'],
                        ],
                    ],
                ]);

                if ($highestRow >= 2) {
                    $sheet->getStyle("A2:C{$highestRow}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_TOP)
                        ->setWrapText(true);

                    $sheet->getStyle("A2:A{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    $sheet->getStyle("A2:A{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_TEXT);

                    $sheet->getStyle("C2:C{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('"$" #,##0.00');
                }

                foreach ([
                    'A' => 15,
                    'B' => 32,
                    'C' => 14,
                ] as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                $sheet->getRowDimension(1)->setRowHeight(24);
            },
        ];
    }
}
