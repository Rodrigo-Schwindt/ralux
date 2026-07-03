<?php

namespace App\Http\Controllers\Marcas;

use App\Models\Marca;
use App\Http\Controllers\Controller;

class MarcaController extends Controller
{
    public function index()
    {
        $page          = request('page', 1);
        $search        = request('search', '');
        $sortField     = request('sortField', 'orden');
        $sortDirection = request('sortDirection', 'asc');

        $marcas = Marca::with('vehiculoTipo')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('descripcion_es', 'like', "%{$search}%")
                      ->orWhere('descripcion_en', 'like', "%{$search}%")
                      ->orWhere('orden', 'like', "%{$search}%");
                });
            })
            ->when($sortField === 'orden', function ($q) use ($sortDirection) {
                $q->orderByRaw('(orden IS NULL OR orden = "") ASC, orden ' . ($sortDirection === 'desc' ? 'DESC' : 'ASC'));
            }, fn($q) => $q->orderBy($sortField, $sortDirection))
            ->paginate(10, ['*'], 'page', $page);

        if (request()->ajax()) {
            return response()->json([
                'html'        => view('livewire.marcas.partials.table', compact('marcas'))->render(),
                'pagination'  => view('livewire.marcas.partials.pagination', compact('marcas'))->render(),
                'currentPage' => $marcas->currentPage(),
                'lastPage'    => $marcas->lastPage(),
                'firstItem'   => $marcas->firstItem() ?? 0,
                'lastItem'    => $marcas->lastItem() ?? 0,
                'total'       => $marcas->total(),
            ]);
        }

        return view('livewire.marcas.index', compact('marcas'));
    }

    public function destroy($id)
    {
        $marca = Marca::findOrFail($id);
        $marca->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Marca eliminada correctamente',
            ]);
        }

        return redirect()->route('marcas.index')
            ->with('success', 'Marca eliminada correctamente');
    }
}
