<?php

namespace App\Livewire\Vistas\Productos;

use Livewire\Component;
use App\Models\Producto;
use App\Models\ProductoTipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Support\ProductSearch;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class ProductoDetalle extends Component
{
    public int $productoId;
    public ?string $imagenActual     = null;
    public ?string $imagenActualTipo = null;

    public function mount(int $id)
    {
        $producto = Producto::with('imagenes')
            ->where('visible', true)
            ->findOrFail($id);

        $this->productoId = $producto->id;

        $principal = $producto->imagenes->firstWhere('principal', true)
            ?? $producto->imagenes->where('tipo', '!=', 'video')->first()
            ?? $producto->imagenes->first();

        $this->imagenActual = $principal?->ruta
            ?? $producto->imagen_diagrama
            ?? $producto->diagrama_orientativo;

        $this->imagenActualTipo = $principal?->tipo
            ?? ($producto->imagen_diagrama ? 'diagrama' : ($producto->diagrama_orientativo ? 'diagrama' : 'galeria'));
    }

    public function cambiarImagen(string $ruta, string $tipo = 'galeria'): void
    {
        $this->imagenActual     = $ruta;
        $this->imagenActualTipo = $tipo;
    }

    public function render()
    {
        $producto = Producto::with(['tipo', 'imagenes', 'marcas', 'modelos.marca', 'codigosOM.marca'])
            ->findOrFail($this->productoId);

        $relacionados = Producto::where('visible', true)
            ->where('producto_tipo_id', $producto->producto_tipo_id)
            ->where('id', '!=', $producto->id)
            ->with(['tipo', 'imagenPrincipal', 'marcas'])
            ->inRandomOrder()
            ->take(5)
            ->get();

        $tipos = ProductoTipo::where('visible', true)
            ->orderBy('descripcion_es')
            ->get();

        $marcas = Marca::where('visible', true)
            ->whereHas('productos', fn($q) => $q->where('visible', true))
            ->orderBy('descripcion_es')
            ->get();

        $allModelos = Modelo::where('visible', true)
            ->orderBy('descripcion_es')
            ->get(['id', 'descripcion_es', 'marca_id'])
            ->filter(fn($m) => filled(trim($m->descripcion_es, '- ')))
            ->map(function ($m) {
                $m->marca_ids = array_map('strval', ProductSearch::equivalentMarcaIds($m->marca_id));
                return $m;
            })
            ->values();

        return view('livewire.vistas.productos.producto-detalle', [
            'producto'    => $producto,
            'relacionados' => $relacionados,
            'tipos'       => $tipos,
            'marcas'      => $marcas,
            'allModelos'  => $allModelos,
        ]);
    }
}
