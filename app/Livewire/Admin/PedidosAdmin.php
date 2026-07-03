<?php

namespace App\Livewire\Admin;

use App\Models\Pedido;
use Livewire\Component;
use Livewire\WithPagination;

class PedidosAdmin extends Component
{
    use WithPagination;

    public string $buscar = '';
    public string $filtroEntregado = 'todos';
    public string $filtroFormaPago = 'todos';
    public string $filtroFechaDesde = '';
    public string $filtroFechaHasta = '';
    public string $vista = 'lista';
    public ?int $pedidoSeleccionadoId = null;

    public function updatingBuscar(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEntregado(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroFormaPago(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroFechaDesde(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroFechaHasta(): void
    {
        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->buscar = '';
        $this->filtroEntregado = 'todos';
        $this->filtroFormaPago = 'todos';
        $this->filtroFechaDesde = '';
        $this->filtroFechaHasta = '';
        $this->resetPage();
    }

    public function verDetalle(int $id): void
    {
        $this->pedidoSeleccionadoId = $id;
        $this->vista = 'detalle';
    }

    public function volverLista(): void
    {
        $this->vista = 'lista';
        $this->pedidoSeleccionadoId = null;
    }

    public function actualizarFechaEntrega(int $id, string $fecha): void
    {
        Pedido::findOrFail($id)->update(['fecha_entrega' => $fecha ?: null]);
        session()->flash('success', 'Fecha de entrega actualizada.');
    }

    public function toggleEntregado(int $id): void
    {
        $pedido = Pedido::findOrFail($id);

        if ($pedido->entregado) {
            $pedido->update(['entregado' => false, 'fecha_entregado' => null]);
        } else {
            $pedido->marcarComoEntregado();
        }

        if ($this->pedidoSeleccionadoId === $id) {
            // Refresca detalle
        }
    }

    public function render()
    {
        $query = Pedido::with(['cliente', 'items']);

        if ($this->buscar !== '') {
            $buscar = $this->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_pedido', 'like', '%' . $buscar . '%')
                  ->orWhereHas('cliente', fn($q2) => $q2
                      ->where('nombre', 'like', '%' . $buscar . '%')
                      ->orWhere('email', 'like', '%' . $buscar . '%')
                  );
            });
        }

        if ($this->filtroEntregado !== 'todos') {
            $query->where('entregado', $this->filtroEntregado === 'entregados');
        }

        if ($this->filtroFormaPago !== 'todos') {
            $query->where('forma_pago', $this->filtroFormaPago);
        }

        if ($this->filtroFechaDesde !== '') {
            $query->whereDate('fecha_compra', '>=', $this->filtroFechaDesde);
        }

        if ($this->filtroFechaHasta !== '') {
            $query->whereDate('fecha_compra', '<=', $this->filtroFechaHasta);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(20);

        $pedidoSeleccionado = $this->pedidoSeleccionadoId
            ? Pedido::with(['cliente', 'items'])->find($this->pedidoSeleccionadoId)
            : null;

        return view('livewire.admin.pedidos-admin', compact('pedidos', 'pedidoSeleccionado'))
            ->layout('layouts.admin');
    }
}
