<?php

namespace App\Http\Controllers\Modelos;

use App\Models\Modelo;
use App\Models\Marca;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ModeloEdit extends Controller
{
    public function edit($id)
    {
        $modelo = Modelo::findOrFail($id);
        $marcas = Marca::with('vehiculoTipo')->orderBy('descripcion_es')->get();

        return view('livewire.modelos.edit', compact('modelo', 'marcas'));
    }

    public function update(Request $request, $id)
    {
        $modelo = Modelo::findOrFail($id);

        $validated = $request->validate([
            'descripcion_es' => 'required|string|max:255',
            'descripcion_en' => 'nullable|string|max:255',
            'marca_id'       => 'required|exists:marcas,id',
            'orden'          => ['nullable', 'string', 'max:10', 'regex:/^[A-Za-z]+$/'],
        ]);

        $validated['orden']   = filled($validated['orden'] ?? null) ? strtoupper($validated['orden']) : null;
        $validated['visible'] = $request->boolean('visible');

        $modelo->update($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Modelo actualizado correctamente',
            'redirect' => route('modelos.index'),
        ]);
    }
}
