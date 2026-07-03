<?php

namespace App\Http\Controllers\Modelos;

use App\Models\Modelo;
use App\Http\Controllers\Controller;

class ModeloController extends Controller
{
    public function index()
    {
        $page          = request('page', 1);
        $search        = request('search', '');
        $sortField     = request('sortField', 'orden');
        $sortDirection = request('sortDirection', 'asc');

        $modelos = Modelo::with('marca')
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
                'html'       => view('livewire.modelos.partials.table', compact('modelos'))->render(),
                'pagination' => view('livewire.modelos.partials.pagination', compact('modelos'))->render(),
            ]);
        }

        return view('livewire.modelos.index', compact('modelos'));
    }

    public function destroy($id)
    {
        $modelo = Modelo::findOrFail($id);
        $modelo->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Modelo eliminado correctamente',
            ]);
        }

        return redirect()->route('modelos.index')
            ->with('success', 'Modelo eliminado correctamente');
    }
}
