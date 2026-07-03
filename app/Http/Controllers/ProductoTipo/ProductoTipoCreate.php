<?php

namespace App\Http\Controllers\ProductoTipo;

use App\Models\ProductoTipo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductoTipoCreate extends Controller
{
    public function create()
    {
        return view('livewire.producto-tipo.create');
    }

    public function store(Request $request)
    {
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
        $validated['estado']  = true;
        $validated['visible'] = $request->boolean('visible');

        ProductoTipo::create($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Tipo de producto creado correctamente',
            'redirect' => route('producto-tipo.index'),
        ]);
    }
}
