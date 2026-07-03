<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarritoConfig;
use Illuminate\Http\Request;

class CarritoConfigController extends Controller
{
    public function index()
    {
        $config = CarritoConfig::first() ?? new CarritoConfig();
        return view('livewire.admin.carrito-config', compact('config'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'informacion'   => 'nullable|string',
            'escribenos'    => 'nullable|string|max:500',
            'contado'       => 'nullable|numeric|min:0|max:100',
            'transferencia' => 'nullable|numeric|min:0|max:100',
            'corriente'     => 'nullable|numeric|min:0|max:100',
            'iva'           => 'nullable|numeric|min:0|max:100',
            'iva_especial'  => 'nullable|numeric|min:0|max:100',
            'iva_prefijos_especiales' => 'nullable|string|max:255',
        ]);

        $prefijosEspeciales = collect(preg_split('/[\s,;|]+/', (string) $request->iva_prefijos_especiales, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($prefijo) => mb_strtoupper(trim($prefijo)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $config = CarritoConfig::first() ?? new CarritoConfig();
        $config->informacion   = $request->informacion;
        $config->escribenos    = $request->escribenos;
        $config->contado       = $request->contado ?? 0;
        $config->transferencia = $request->transferencia ?? 0;
        $config->corriente     = $request->corriente ?? 0;
        $config->iva           = $request->iva ?? 21;
        $config->iva_especial  = $request->iva_especial ?? 10.5;
        $config->iva_prefijos_especiales = $prefijosEspeciales ?: ['F', 'M'];
        $config->save();

        return redirect()->route('admin.carrito-config')->with('success', 'Configuración guardada exitosamente');
    }
}
