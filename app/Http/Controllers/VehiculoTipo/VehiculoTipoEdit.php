<?php

namespace App\Http\Controllers\VehiculoTipo;

use App\Models\VehiculoTipo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VehiculoTipoEdit extends Controller
{
    public function edit($id)
    {
        $vehiculoTipo = VehiculoTipo::findOrFail($id);

        return view('livewire.vehiculo-tipo.edit', compact('vehiculoTipo'));
    }

    public function update(Request $request, $id)
    {
        $vehiculoTipo = VehiculoTipo::findOrFail($id);

        $validated = $request->validate([
            'descripcion_es' => 'required|string|max:255',
            'descripcion_en' => 'nullable|string|max:255',
            'orden'          => 'nullable|integer|min:0',
        ]);

        $validated['visible'] = $request->boolean('visible');

        $vehiculoTipo->update($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Tipo de vehículo actualizado correctamente',
            'redirect' => route('vehiculo-tipo.index'),
        ]);
    }
}
