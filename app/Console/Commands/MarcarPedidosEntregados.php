<?php

namespace App\Console\Commands;

use App\Mail\PedidoEntregadoMail;
use App\Models\Pedido;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MarcarPedidosEntregados extends Command
{
    protected $signature = 'pedidos:marcar-entregados';

    protected $description = 'Marca como entregados los pedidos cuya fecha de entrega ya llegó';

    public function handle(): int
    {
        $pedidos = Pedido::query()
            ->where('entregado', false)
            ->where('cancelado', false)
            ->whereNotNull('fecha_entrega')
            ->whereDate('fecha_entrega', '<=', today())
            ->get();

        foreach ($pedidos as $pedido) {
            $pedido->update([
                'entregado' => true,
                'fecha_entregado' => $pedido->fecha_entrega,
            ]);

            $this->notifyPedidoEntregado($pedido->fresh(['cliente', 'items']));
        }

        $this->info("Se marcaron {$pedidos->count()} pedido(s) como entregados.");

        return self::SUCCESS;
    }

    private function notifyPedidoEntregado(Pedido $pedido): void
    {
        if (! $pedido->cliente?->email) {
            return;
        }

        try {
            Mail::to($pedido->cliente->email)->send(new PedidoEntregadoMail($pedido));
        } catch (\Throwable $e) {
            Log::error('Error al enviar email de pedido entregado: '.$e->getMessage(), [
                'pedido_id' => $pedido->id,
                'email' => $pedido->cliente->email,
            ]);
        }
    }
}
