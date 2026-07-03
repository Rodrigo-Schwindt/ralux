<?php

namespace App\Http\Controllers\Marcas;

use App\Models\Marca;
use App\Models\VehiculoTipo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MarcaCreate extends Controller
{
    public function create()
    {
        $vehiculoTipos = VehiculoTipo::orderBy('orden')->get();

        return view('livewire.marcas.create', compact('vehiculoTipos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion_es'   => 'required|string|max:255',
            'descripcion_en'   => 'nullable|string|max:255',
            'vehiculo_tipo_id' => 'required|exists:vehiculo_tipo,id',
            'orden' => ['nullable', 'string', 'max:10', 'regex:/^[A-Za-z]+$/'],
        ]);

        $validated['orden']   = filled($validated['orden'] ?? null) ? strtoupper($validated['orden']) : null;
        $validated['estado']  = true;
        $validated['visible'] = $request->boolean('visible');

        Marca::create($validated);

        return response()->json([
            'success'  => true,
            'message'  => 'Marca creada correctamente',
            'redirect' => route('marcas.index'),
        ]);
    }
}
