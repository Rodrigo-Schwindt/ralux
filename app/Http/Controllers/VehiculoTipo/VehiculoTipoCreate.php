<?php

namespace App\Http\Controllers\VehiculoTipo;

use App\Models\VehiculoTipo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VehiculoTipoCreate extends Controller
{
    public function create()
    {
        return view('livewire.vehiculo-tipo.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion_es' => 'required|string|max:255',
            'descripcion_en' => 'nullable|string|max:255',
            'orden'          => 'nullable|integer|min:0',
        ]);

        $validated['estado']  = true;
        $validated['visible'] = $request->boolean('visible');

        VehiculoTipo::create($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Tipo de vehículo creado correctamente',
            'redirect' => route('vehiculo-tipo.index'),
        ]);
    }
}
