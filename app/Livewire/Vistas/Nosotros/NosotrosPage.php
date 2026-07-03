<?php

namespace App\Livewire\Vistas\Nosotros;

use Livewire\Component;
use App\Models\Nosotros;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class NosotrosPage extends Component
{
    public $nosotros;


    public function mount()
    {
        $this->nosotros = Nosotros::first();
  
    }

    public function render()
    {
        return view('livewire.vistas.nosotros.nosotros', [
            'nosotros' => $this->nosotros,
        
        ]);
    }
}