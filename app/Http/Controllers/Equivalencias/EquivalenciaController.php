<?php

namespace App\Http\Controllers\Equivalencias;

use App\Models\Equivalencia;
use App\Http\Controllers\Controller;

class EquivalenciaController extends Controller
{
    public function index()
    {
        $page          = request('page', 1);
        $search        = request('search', '');
        $sortField     = request('sortField', 'distribuidor');
        $sortDirection = request('sortDirection', 'asc');

        $equivalencias = Equivalencia::with('producto')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                      ->orWhere('distribuidor', 'like', "%{$search}%")
                      ->orWhereHas('producto', function ($pq) use ($search) {
                          $pq->where('codigo_ralux', 'like', "%{$search}%")
                             ->orWhere('descripcion_es', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(15, ['*'], 'page', $page);

        if (request()->ajax()) {
            return response()->json([
                'html'       => view('livewire.equivalencias.partials.table', compact('equivalencias'))->render(),
                'pagination' => view('livewire.equivalencias.partials.pagination', compact('equivalencias'))->render(),
                'total'      => $equivalencias->total(),
            ]);
        }

        return view('livewire.equivalencias.index', compact('equivalencias'));
    }

    public function destroy($id)
    {
        $equivalencia = Equivalencia::findOrFail($id);
        $equivalencia->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Equivalencia eliminada correctamente',
            ]);
        }

        return redirect()->route('equivalencias.index')
            ->with('success', 'Equivalencia eliminada correctamente');
    }
}
