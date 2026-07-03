<?php

namespace App\Livewire\Vistas\Home;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Sliders;
use App\Models\ProductoTipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Support\ProductSearch;

#[Layout('layouts.public2')]
class Inicio extends Component
{
    public function render()
    {
        $sliders = Sliders::orderBy('orden')->get();

        $tipos = ProductoTipo::where('visible', true)
            ->whereHas('productos', fn($q) => $q->where('visible', true))
            ->orderBy('descripcion_es')
            ->get();

        $marcas = Marca::where('visible', true)
            ->whereHas('productos', fn($q) => $q->where('visible', true))
            ->orderBy('descripcion_es')
            ->get();

        // All models as flat array with marca_id — loaded once, used client-side
        $todosModelos = Modelo::where('visible', true)
            ->whereNotNull('descripcion_es')
            ->where('descripcion_es', '!=', '')
            ->whereHas('productos', fn($q) => $q->where('visible', true))
            ->orderBy('descripcion_es')
            ->get()
            ->filter(fn($m) => filled(trim($m->descripcion_es, '- ')))
            ->map(fn($m) => [
                'value'    => (string) $m->id,
                'label'    => trim($m->descripcion_es),
                'marca_id' => (string) $m->marca_id,
                'marca_ids' => array_map('strval', ProductSearch::equivalentMarcaIds($m->marca_id)),
            ])
            ->values()
            ->toArray();

        return view('livewire.vistas.home.inicio', [
            'sliders'       => $sliders,
            'tipos'         => $tipos,
            'marcas'        => $marcas,
            'todosModelos'  => $todosModelos,
        ]);
    }
}
