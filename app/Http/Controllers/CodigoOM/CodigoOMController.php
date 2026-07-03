<?php

namespace App\Http\Controllers\CodigoOM;

use App\Models\CodigoOM;
use App\Http\Controllers\Controller;

class CodigoOMController extends Controller
{
    public function index()
    {
        $page          = request('page', 1);
        $search        = request('search', '');
        $sortField     = request('sortField', 'codigo');
        $sortDirection = request('sortDirection', 'asc');

        $codigos = CodigoOM::with(['producto', 'marca'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                      ->orWhereHas('marca', function ($mq) use ($search) {
                          $mq->where('descripcion_es', 'like', "%{$search}%");
                      })
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
                'html'       => view('livewire.codigo-om.partials.table', compact('codigos'))->render(),
                'pagination' => view('livewire.codigo-om.partials.pagination', compact('codigos'))->render(),
                'total'      => $codigos->total(),
            ]);
        }

        return view('livewire.codigo-om.index', compact('codigos'));
    }

    public function destroy($id)
    {
        $codigo = CodigoOM::findOrFail($id);
        $codigo->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Código OM eliminado correctamente',
            ]);
        }

        return redirect()->route('codigo-om.index')
            ->with('success', 'Código OM eliminado correctamente');
    }
}
