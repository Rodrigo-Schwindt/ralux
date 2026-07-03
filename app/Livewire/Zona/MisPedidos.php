<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pedido;
use App\Models\Carrito;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.zone')]
class MisPedidos extends Component
{
    use WithPagination;

    public int $perPage = 10;

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function recomprar($pedidoId)
    {
        $clienteId = Auth::guard('cliente')->id();

        $pedido = Pedido::with('items.producto')
            ->where('id', $pedidoId)
            ->where('cliente_id', $clienteId)
            ->first();

        if (!$pedido) {
            return null;
        }

        foreach ($pedido->items as $item) {
            if (!$item->producto) {
                continue;
            }

            $existing = Carrito::where('cliente_id', $clienteId)
                ->where('producto_id', $item->producto_id)
                ->first();

            if ($existing) {
                $existing->cantidad += $item->cantidad;
                $existing->precio_unitario = $item->producto->precio ?? $item->precio_unitario;
                $existing->save();
            } else {
                Carrito::create([
                    'cliente_id'         => $clienteId,
                    'producto_id'        => $item->producto_id,
                    'cantidad'           => $item->cantidad,
                    'precio_unitario'    => $item->producto->precio ?? $item->precio_unitario,
                    'descuento_unitario' => 0,
                ]);
            }
        }

        return redirect()->route('cliente.carrito');
    }

    public function render()
    {
        $pedidos = Pedido::where('cliente_id', Auth::guard('cliente')->id())
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.zona.mis-pedidos', [
            'pedidos' => $pedidos,
        ]);
    }
}