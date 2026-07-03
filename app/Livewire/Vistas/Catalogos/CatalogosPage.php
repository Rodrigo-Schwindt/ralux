<?php

namespace App\Livewire\Vistas\Catalogos;

use App\Models\Catalogo;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class CatalogosPage extends Component
{

    public function render()
    {
        $catalogos = Catalogo::where('visible', 1)
            ->orderBy('orden', 'asc')
            ->get();

        return view('livewire.vistas.catalogos.catalogos-page', [
            'catalogos' => $catalogos,
         
        ]);
    }
}