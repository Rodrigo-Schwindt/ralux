<?php

namespace App\Http\Controllers\Equivalencias;

use App\Models\Equivalencia;
use App\Models\Producto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EquivalenciaEdit extends Controller
{
    public function edit($id)
    {
        $equivalencia = Equivalencia::with('producto')->findOrFail($id);
        $productos    = Producto::orderBy('codigo_ralux')->get(['id', 'codigo_ralux', 'descripcion_es']);

        return view('livewire.equivalencias.edit', compact('equivalencia', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $equivalencia = Equivalencia::findOrFail($id);

        $validated = $request->validate([
            'producto_id'  => 'required|exists:productos,id',
            'codigo'       => 'required|string|max:100',
            'distribuidor' => 'nullable|string|max:150',
        ]);

        $equivalencia->update($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Equivalencia actualizada correctamente',
            'redirect' => route('equivalencias.index'),
        ]);
    }
}
