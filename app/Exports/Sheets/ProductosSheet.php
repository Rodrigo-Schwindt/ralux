<?php

namespace App\Exports\Sheets;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductosSheet implements FromCollection, WithHeadings, WithEvents, WithStyles, WithTitle
{
    use RegistersEventListeners;

    public function __construct(private bool $withData = true) {}

    public function title(): string
    {
        return 'Productos';
    }

    public function collection()
    {
        if (!$this->withData) {
            // Return one example row (grayed out) to show format
            return collect([[
                0,                          // eliminar
                'EJEMPLO-001',              // id
                'COD-EJEMPLO',              // codigo_ralux
                1,                          // producto_tipo_id
                'Descripción en español',   // descripcion_es
                13208,                      // precio
                0,                          // descuento
                12,                         // voltaje
                100,                        // amperaje
                2,                          // terminales
                0,                          // soporte
                'Material',                 // caract_1
                'Acero',                    // valor_1
                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
                0,                          // destacado
                1,                          // visible
                1,                          // orden
                '1,2',                      // vehiculo_tipos_ids
                '1,3',                      // marcas_ids
                '2,5',                      // modelos_ids
                'ABC123|1,XYZ456|',         // codigos_om
            ]]);
        }

        return Producto::with(['tipo', 'marcas', 'modelos', 'vehiculoTipos', 'codigosOM'])
            ->orderBy('orden')
            ->get()
            ->map(fn($p) => [
                0,
                $p->id,
                $p->codigo_ralux,
                $p->producto_tipo_id,
                $p->descripcion_es,
                $p->precio,
                $p->descuento,
                $p->voltaje,
                $p->amperaje,
                $p->terminales,
                $p->soporte ? 1 : 0,
                $p->caract_1,  $p->valor_1,
                $p->caract_2,  $p->valor_2,
                $p->caract_3,  $p->valor_3,
                $p->caract_4,  $p->valor_4,
                $p->caract_5,  $p->valor_5,
                $p->caract_6,  $p->valor_6,
                $p->caract_7,  $p->valor_7,
                $p->caract_8,  $p->valor_8,
                $p->caract_9,  $p->valor_9,
                $p->caract_10, $p->valor_10,
                $p->destacado ? 1 : 0,
                $p->visible   ? 1 : 0,
                $p->orden,
                $p->vehiculoTipos->pluck('id')->implode(','),
                $p->marcas->pluck('id')->implode(','),
                $p->modelos->pluck('id')->implode(','),
                $p->codigosOM->map(fn($c) => $c->codigo . '|' . ($c->marca_id ?? ''))->implode(','),
            ]);
    }

    public function headings(): array
    {
        return [
            'eliminar', 'id', 'codigo_ralux', 'producto_tipo_id',
            'descripcion_es', 'precio', 'descuento',
            'voltaje', 'amperaje', 'terminales', 'soporte',
            'caract_1', 'valor_1', 'caract_2', 'valor_2',
            'caract_3', 'valor_3', 'caract_4', 'valor_4',
            'caract_5', 'valor_5', 'caract_6', 'valor_6',
            'caract_7', 'valor_7', 'caract_8', 'valor_8',
            'caract_9', 'valor_9', 'caract_10', 'valor_10',
            'destacado', 'visible', 'orden',
            'vehiculo_tipos_ids', 'marcas_ids', 'modelos_ids', 'codigos_om',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row: dark blue background, white bold text
        $sheet->getStyle('1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
        ]);

        // 'eliminar' column header: red (column A)
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B91C1C']],
        ]);

        if (!$this->withData) {
            // Example row: light gray italic to distinguish from real data
            $sheet->getStyle('A2:AL2')->applyFromArray([
                'font'  => ['italic' => true, 'color' => ['rgb' => '9CA3AF']],
                'fill'  => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
            ]);
            // Add comment to A1 about eliminar column
            $sheet->getComment('A1')->getText()->createTextRun(
                'Poner 1 para eliminar ese producto al importar. Dejar en 0 para crear/actualizar.'
            );
        }

        return [];
    }

    public static function afterSheet(AfterSheet $event): void
    {
        $sheet = $event->sheet->getDelegate();

        // Freeze header row
        $sheet->freezePane('A2');

        // Auto-size all columns (A to AL = 38 columns)
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (['AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set minimum widths for relationship columns
        foreach (['AI','AJ','AK','AL'] as $col) {
            $sheet->getColumnDimension($col)->setWidth(25);
        }

        // Excel stores number masks with invariant separators and displays them by locale.
        // In Spanish/Argentine Excel this shows as 13.208,11 while keeping a numeric value.
        $sheet->getStyle('F:F')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(20);
    }
}
