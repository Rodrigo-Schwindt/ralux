<?php

namespace App\Exports;

use App\Models\PedidoItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PedidoExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = PedidoItem::with(['pedido.cliente'])
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->select('pedido_items.*')
            ->orderBy('pedidos.created_at', 'desc');

        if (!empty($this->filters['buscar'])) {
            $buscar = $this->filters['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('pedidos.numero_pedido', 'like', '%' . $buscar . '%')
                  ->orWhereHas('pedido.cliente', function ($q2) use ($buscar) {
                      $q2->where('nombre', 'like', '%' . $buscar . '%')
                         ->orWhere('email', 'like', '%' . $buscar . '%');
                  });
            });
        }

        if (!empty($this->filters['estado']) && $this->filters['estado'] !== 'todos') {
            if ($this->filters['estado'] === 'cancelados') {
                $query->where('pedidos.cancelado', true);
            } elseif ($this->filters['estado'] === 'entregados') {
                $query->where('pedidos.cancelado', false)->where('pedidos.entregado', true);
            } elseif ($this->filters['estado'] === 'pendientes') {
                $query->where('pedidos.cancelado', false)->where('pedidos.entregado', false);
            }
        }

        if (!empty($this->filters['forma_pago']) && $this->filters['forma_pago'] !== 'todos') {
            $query->where('pedidos.forma_pago', $this->filters['forma_pago']);
        }

        if (!empty($this->filters['fecha_desde'])) {
            $query->whereDate('pedidos.fecha_compra', '>=', $this->filters['fecha_desde']);
        }

        if (!empty($this->filters['fecha_hasta'])) {
            $query->whereDate('pedidos.fecha_compra', '<=', $this->filters['fecha_hasta']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Cliente',
            'Nº Pedido',
            'Código de pieza',
            'Cantidad',
            'Precio unitario',
            'Fecha del pedido',
            'OC',
            'Descuento aplicado',
        ];
    }

    public function map($item): array
    {
        $pedido = $item->pedido;
        return [
            $pedido->cliente->nombre ?? '',
            $pedido->numero_pedido,
            $item->codigo_producto,
            $item->cantidad,
            $item->precio_unitario,
            $pedido->fecha_compra?->format('d/m/Y') ?? '',
            route('admin.pedidos.factura', $pedido->id),
            $pedido->descuentos,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
            ],
        ];
    }
}
