<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use App\Models\Precio;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

#[Layout('layouts.zone')]
class Precios extends Component
{
    public function descargar($id)
    {
        $precio = Precio::where('publicado', true)->findOrFail($id);
    
        return Storage::disk('public')->download($precio->archivo);
    }
    
    public function render()
    {
        $precios = Precio::where('publicado', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.zona.precios', [
            'precios' => $precios,
        ]);
    }
}
