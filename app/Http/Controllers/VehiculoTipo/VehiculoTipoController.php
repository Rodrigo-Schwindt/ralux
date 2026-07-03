<?php

namespace App\Http\Controllers\VehiculoTipo;

use App\Models\VehiculoTipo;
use App\Http\Controllers\Controller;

class VehiculoTipoController extends Controller
{
    public function index()
    {
        $page          = request('page', 1);
        $search        = request('search', '');
        $sortField     = request('sortField', 'orden');
        $sortDirection = request('sortDirection', 'asc');

        $vehiculoTipos = VehiculoTipo::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcion_es', 'like', "%{$search}%")
                  ->orWhere('descripcion_en', 'like', "%{$search}%")
                  ->orWhere('orden', 'like', "%{$search}%");
            });
        })
        ->when($sortField === 'orden', function ($q) use ($sortDirection) {
            $q->orderByRaw('orden IS NULL ASC, orden ' . ($sortDirection === 'desc' ? 'DESC' : 'ASC'));
        }, fn($q) => $q->orderBy($sortField, $sortDirection))
        ->paginate(10, ['*'], 'page', $page);

        if (request()->ajax()) {
            return response()->json([
                'html'       => view('livewire.vehiculo-tipo.partials.table', compact('vehiculoTipos'))->render(),
                'pagination' => view('livewire.vehiculo-tipo.partials.pagination', compact('vehiculoTipos'))->render(),
            ]);
        }

        return view('livewire.vehiculo-tipo.index', compact('vehiculoTipos'));
    }

    public function destroy($id)
    {
        $vehiculoTipo = VehiculoTipo::findOrFail($id);
        $vehiculoTipo->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de vehículo eliminado correctamente',
            ]);
        }

        return redirect()->route('vehiculo-tipo.index')
            ->with('success', 'Tipo de vehículo eliminado correctamente');
    }
}
