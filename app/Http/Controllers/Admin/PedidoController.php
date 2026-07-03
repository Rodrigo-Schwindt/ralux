<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PedidoExport;
use App\Http\Controllers\Controller;
use App\Mail\PedidoEntregadoMail;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['cliente', 'items']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_pedido', 'like', '%'.$buscar.'%')
                    ->orWhereHas('cliente', function ($q2) use ($buscar) {
                        $q2->where('nombre', 'like', '%'.$buscar.'%')
                            ->orWhere('email', 'like', '%'.$buscar.'%');
                    });
            });
        }

        if ($request->filled('estado') && $request->estado !== 'todos') {
            if ($request->estado === 'cancelados') {
                $query->where('cancelado', true);
            } elseif ($request->estado === 'entregados') {
                $query->where('cancelado', false)->where('entregado', true);
            } elseif ($request->estado === 'pendientes') {
                $query->where('cancelado', false)->where('entregado', false);
            }
        }

        if ($request->filled('forma_pago') && $request->forma_pago !== 'todos') {
            $query->where('forma_pago', $request->forma_pago);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_compra', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_compra', '<=', $request->fecha_hasta);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function show($id)
    {
        $pedido = Pedido::with(['cliente', 'items'])->findOrFail($id);

        return view('admin.pedidos.show', compact('pedido'));
    }

    public function toggleEntregado(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        if ($pedido->entregado) {
            $pedido->update(['entregado' => false, 'fecha_entregado' => null]);
        } else {
            $pedido->marcarComoEntregado();
            $this->notifyPedidoEntregado($pedido->fresh(['cliente', 'items']));
        }

        if ($request->wantsJson()) {
            $fresh = $pedido->fresh();

            return response()->json([
                'entregado' => $fresh->entregado,
                'fecha_entregado' => $fresh->fecha_entregado?->format('d/m/Y'),
                'fecha_entrega' => $fresh->fecha_entrega?->format('Y-m-d'),
            ]);
        }

        return back()->with('success', 'Estado actualizado.');
    }

    public function toggleCancelado(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->update(['cancelado' => ! $pedido->cancelado]);

        if ($request->wantsJson()) {
            return response()->json(['cancelado' => $pedido->fresh()->cancelado]);
        }

        return back()->with('success', 'Estado actualizado.');
    }

    public function updateFechaEntrega(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->update(['fecha_entrega' => $request->fecha_entrega ?: null]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Fecha de entrega actualizada.');
    }

    public function destroy(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        $this->deletePedido($pedido);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Pedido eliminado correctamente.');
    }

    public function destroyMultiple(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:pedidos,id',
        ]);

        $pedidos = Pedido::whereIn('id', $validated['ids'])->get();

        DB::transaction(function () use ($pedidos) {
            foreach ($pedidos as $pedido) {
                $this->deletePedido($pedido);
            }
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'deleted' => $pedidos->count(),
            ]);
        }

        return back()->with('success', $pedidos->count().' pedido(s) eliminado(s) correctamente.');
    }

    public function exportarTodo()
    {
        $filename = 'pedidos_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new PedidoExport, $filename);
    }

    public function exportarFiltrado(Request $request)
    {
        $filters = $request->only(['buscar', 'estado', 'forma_pago', 'fecha_desde', 'fecha_hasta']);
        $filename = 'pedidos_filtrado_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new PedidoExport($filters), $filename);
    }

    private function deletePedido(Pedido $pedido): void
    {
        if ($pedido->archivo_path) {
            Storage::disk('public')->delete($pedido->archivo_path);
        }

        $pedido->delete();
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
