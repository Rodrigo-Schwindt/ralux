<?php

namespace App\Http\Controllers\ProductoTipo;

use App\Models\ProductoTipo;
use App\Http\Controllers\Controller;

class ProductoTipoController extends Controller
{
    public function index()
    {
        $page          = request('page', 1);
        $search        = request('search', '');
        $sortField     = request('sortField', 'orden');
        $sortDirection = request('sortDirection', 'asc');

        $productoTipos = ProductoTipo::when($search, function ($query) use ($search) {
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
                'html'       => view('livewire.producto-tipo.partials.table', compact('productoTipos'))->render(),
                'pagination' => view('livewire.producto-tipo.partials.pagination', compact('productoTipos'))->render(),
            ]);
        }

        return view('livewire.producto-tipo.index', compact('productoTipos'));
    }

    public function destroy($id)
    {
        $productoTipo = ProductoTipo::findOrFail($id);
        $productoTipo->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de producto eliminado correctamente',
            ]);
        }

        return redirect()->route('producto-tipo.index')
            ->with('success', 'Tipo de producto eliminado correctamente');
    }
}
