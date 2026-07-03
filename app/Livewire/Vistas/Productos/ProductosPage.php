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
class ProductosPage extends Component
{
    public $tipo_id        = null;
    public $marca_id       = null;
    public $modelo_id      = null;
    public $busqueda       = '';
    public $perPage        = 15;
    public $modelosOptions = [];

    protected $queryString = [
        'tipo_id'   => ['except' => null],
        'marca_id'  => ['except' => null],
        'modelo_id' => ['except' => null],
        'busqueda'  => ['except' => ''],
    ];

    public function updatedTipoId()
    {
        $this->modelo_id = null;
        $this->perPage   = 15;
    }

    public function updatedMarcaId()
    {
        // Solo limpiar modelo si no pertenece a la marca seleccionada
        if ($this->modelo_id) {
            $modelo = Modelo::find($this->modelo_id);
            $marcaIds = ProductSearch::equivalentMarcaIds($this->marca_id);
            if (!$modelo || !in_array((int) $modelo->marca_id, $marcaIds, true)) {
                $this->modelo_id = null;
            }
        }
        $this->perPage = 15;
    }

    public function updatedModeloId()
    {
        $this->perPage = 15;
        if ($this->modelo_id) {
            $modelo = Modelo::find($this->modelo_id);
            if ($modelo && $modelo->marca_id) {
                $this->marca_id = $modelo->marca_id;
            }
        }
    }

    public function buscar()
    {
        $this->perPage = 15;
    }

    public function limpiarFiltros()
    {
        $this->tipo_id   = null;
        $this->marca_id  = null;
        $this->modelo_id = null;
        $this->busqueda  = '';
        $this->perPage   = 15;
    }

    public function cargarMas()
    {
        $this->perPage += 15;
    }

    public function render()
    {
        $tipos = ProductoTipo::where('visible', true)
            ->whereHas('productos', fn($q) => $q->where('visible', true))
            ->orderBy('descripcion_es')
            ->get();

        $marcas = Marca::where('visible', true)
            ->whereHas('productos', fn($q) => $q->where('visible', true))
            ->orderBy('descripcion_es')
            ->get();

        $modelos = Modelo::where('visible', true)
            ->when($this->marca_id, fn($q) => $q->whereIn('marca_id', ProductSearch::equivalentMarcaIds($this->marca_id)))
            ->orderBy('descripcion_es')
            ->get();

        $this->modelosOptions = $modelos
            ->filter(fn($m) => filled(trim($m->descripcion_es, '- ')))
            ->map(fn($m) => ['value' => (string) $m->id, 'label' => trim($m->descripcion_es)])
            ->values()
            ->toArray();

        $query = Producto::query()
            ->where('visible', true)
            ->with(['tipo', 'imagenPrincipal', 'marcas']);

        ProductSearch::applyTipoFilter($query, $this->tipo_id);
        ProductSearch::applyMarcaFilter($query, $this->marca_id);
        ProductSearch::applyModeloFilter($query, $this->modelo_id);
        ProductSearch::applyTextSearch($query, $this->busqueda);
        ProductSearch::applyRelevanceOrder($query, $this->busqueda);

        $total    = $query->count();
        $productos = $query->take($this->perPage)->get();
        $hayMas   = $total > $this->perPage;

        return view('livewire.vistas.productos.productos', [
            'productos' => $productos,
            'tipos'     => $tipos,
            'marcas'    => $marcas,
            'modelos'   => $modelos,
            'hayMas'    => $hayMas,
            'total'     => $total,
        ]);
    }
}
