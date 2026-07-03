<?php

namespace App\Http\Controllers\CodigoOM;

use App\Models\CodigoOM;
use App\Models\Producto;
use App\Models\Marca;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CodigoOMCreate extends Controller
{
    public function create()
    {
        $productos = Producto::orderBy('codigo_ralux')->get(['id', 'codigo_ralux', 'descripcion_es']);
        $marcas    = Marca::orderBy('descripcion_es')->get(['id', 'descripcion_es']);

        return view('livewire.codigo-om.create', compact('productos', 'marcas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'marca_id'    => 'nullable|exists:marcas,id',
            'codigo'      => 'required|string|max:100',
        ]);

        CodigoOM::firstOrCreate($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Código OM creado correctamente',
            'redirect' => route('codigo-om.index'),
        ]);
    }
}
