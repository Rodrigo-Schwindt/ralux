<?php

namespace App\Http\Controllers\Equivalencias;

use App\Models\Equivalencia;
use App\Models\Producto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EquivalenciaCreate extends Controller
{
    public function create()
    {
        $productos = Producto::orderBy('codigo_ralux')->get(['id', 'codigo_ralux', 'descripcion_es']);

        return view('livewire.equivalencias.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'producto_id'  => 'required|exists:productos,id',
            'codigo'       => 'required|string|max:100',
            'distribuidor' => 'nullable|string|max:150',
        ]);

        Equivalencia::create($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Equivalencia creada correctamente',
            'redirect' => route('equivalencias.index'),
        ]);
    }
}
