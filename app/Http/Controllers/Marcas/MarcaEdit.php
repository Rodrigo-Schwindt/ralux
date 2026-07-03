<?php

namespace App\Http\Controllers\Marcas;

use App\Models\Marca;
use App\Models\VehiculoTipo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MarcaEdit extends Controller
{
    public function edit($id)
    {
        $marca         = Marca::findOrFail($id);
        $vehiculoTipos = VehiculoTipo::orderBy('orden')->get();

        return view('livewire.marcas.edit', compact('marca', 'vehiculoTipos'));
    }

    public function update(Request $request, $id)
    {
        $marca = Marca::findOrFail($id);

        $validated = $request->validate([
            'descripcion_es'   => 'required|string|max:255',
            'descripcion_en'   => 'nullable|string|max:255',
            'vehiculo_tipo_id' => 'required|exists:vehiculo_tipo,id',
            'orden' => ['nullable', 'string', 'max:10', 'regex:/^[A-Za-z]+$/'],
        ]);

        $validated['orden']   = filled($validated['orden'] ?? null) ? strtoupper($validated['orden']) : null;
        $validated['visible'] = $request->boolean('visible');

        $marca->update($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Marca actualizada correctamente',
            'redirect' => route('marcas.index'),
        ]);
    }
}
