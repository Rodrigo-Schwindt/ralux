<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use App\Models\CarritoConfig;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.zone')]
class DetallePedido extends Component
{
    public $pedidoId;
    public $pedido;

    public function mount($id)
    {
        $this->pedidoId = $id;
        $this->pedido = Pedido::with(['cliente', 'items.producto'])
            ->where('id', $id)
            ->where('cliente_id', Auth::guard('cliente')->id())
            ->firstOrFail();
    }

    public function descargar()
    {
        if (!$this->pedido->descarga_habilitada) {
            $this->dispatch('show-toast', message: 'La descarga aún no está habilitada para este pedido', type: 'error');
            return;
        }

        return redirect()->route('cliente.pedidos.descargar', $this->pedido->id);
    }

    public function render()
    {
        $partesCliente = array_values(array_filter([
            (float)($this->pedido->porcentaje_descuento_c1 ?? 0),
            (float)($this->pedido->porcentaje_descuento_c2 ?? 0),
            (float)($this->pedido->porcentaje_descuento_c3 ?? 0),
        ]));

        return view('livewire.zona.detalle-pedido', [
            'partesCliente'          => $partesCliente,
            'totalDescuentoCliente'  => (float)($this->pedido->descuento_cliente ?? 0),
            'totalDescuentoTipo'     => (float)($this->pedido->descuento_tipo ?? 0),
            'totalDescuentoProducto' => (float)($this->pedido->descuento_producto ?? 0),
            'descuentoPorPago'       => (float)($this->pedido->descuento_pago ?? 0),
            'porcentajePago'         => (float)($this->pedido->porcentaje_descuento_pago ?? 0),
            'config'                 => CarritoConfig::first(),
        ]);
    }
}
