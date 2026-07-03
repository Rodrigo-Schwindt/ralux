<?php

namespace App\Livewire\Vistas\Servicios;

use Livewire\Component;
use App\Models\Servicio;
use Livewire\Attributes\Layout;

#[Layout('layouts.public', ['fullHeight' => 'min-h-[600px]'])]
class Servicios extends Component
{
    public $servicio;

    public function mount()
    {
        $this->servicio = Servicio::with('downloads')->first();
    }

    public function render()
    {
        return view('livewire.vistas.servicios.servicio', [
            'servicio' => $this->servicio,
        ]);
    }
}
