<?php

namespace App\Http\Controllers\CodigoOM;

use App\Models\CodigoOM;
use App\Models\Producto;
use App\Models\Marca;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CodigoOMEdit extends Controller
{
    public function edit($id)
    {
        $codigoOM  = CodigoOM::with(['producto', 'marca'])->findOrFail($id);
        $productos = Producto::orderBy('codigo_ralux')->get(['id', 'codigo_ralux', 'descripcion_es']);
        $marcas    = Marca::orderBy('descripcion_es')->get(['id', 'descripcion_es']);

        return view('livewire.codigo-om.edit', compact('codigoOM', 'productos', 'marcas'));
    }

    public function update(Request $request, $id)
    {
        $codigoOM = CodigoOM::findOrFail($id);

        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'marca_id'    => 'nullable|exists:marcas,id',
            'codigo'      => 'required|string|max:100',
        ]);

        $codigoOM->update($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Código OM actualizado correctamente',
            'redirect' => route('codigo-om.index'),
        ]);
    }
}
