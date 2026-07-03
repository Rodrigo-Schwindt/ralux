<?php

namespace App\Http\Controllers\ProductoTipo;

use App\Models\ProductoTipo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductoTipoEdit extends Controller
{
    public function edit($id)
    {
        $productoTipo = ProductoTipo::findOrFail($id);

        return view('livewire.producto-tipo.edit', compact('productoTipo'));
    }

    public function update(Request $request, $id)
    {
        $productoTipo = ProductoTipo::findOrFail($id);

        $validated = $request->validate([
            'descripcion_es' => 'required|string|max:255',
            'descripcion_en' => 'nullable|string|max:255',
            'orden'          => ['nullable', 'string', 'max:10', 'regex:/^[A-Za-z]+$/'],
            'descuento'      => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['orden']   = filled($validated['orden'] ?? null) ? strtoupper($validated['orden']) : null;
        $validated['descuento'] = isset($validated['descuento']) && $validated['descuento'] !== null && $validated['descuento'] !== ''
            ? $validated['descuento']
            : 0;
        $validated['visible'] = $request->boolean('visible');

        $productoTipo->update($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Tipo de producto actualizado correctamente',
            'redirect' => route('producto-tipo.index'),
        ]);
    }
}
