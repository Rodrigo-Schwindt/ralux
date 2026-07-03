<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\ProductoTipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Producto;
use App\Models\Carrito;
use App\Models\CarritoConfig;
use App\Support\ProductSearch;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.zone')]
class ProductosZona extends Component
{
    use WithPagination;

    #[Url(keep: true)]
    public $tipo_id = null;

    #[Url(keep: true)]
    public $marca_id = null;

    #[Url(keep: true)]
    public $modelo_id = null;

    #[Url(keep: true)]
    public $busqueda = '';

    public array $cantidades = [];

    #[Url(keep: true)]
    public $perPage = 24;

    public $mostrarModalOfertas = false;
    public $productosEnOferta   = [];
    public $productoActualModal = 0;
    public $modelosOptions      = [];
    public ?int $productoDetalleId = null;
    public ?string $modalImagenActual = null;
    public ?string $modalImagenActualTipo = null;

    public function calcularDescuentos($precio, $producto)
    {
        $precio  = (float)($precio ?? 0);
        $cliente = Auth::guard('cliente')->user();

        $dC1       = (float)($cliente?->descuento        ?? 0);
        $dC2       = (float)($cliente?->descuento2       ?? 0);
        $dC3       = (float)($cliente?->descuento3       ?? 0);
        $dTipo     = (float)($producto->tipo?->descuento ?? 0);
        $dProducto = (float)($producto->descuento        ?? 0);

        $partes = array_values(array_filter([$dC1, $dC2, $dC3, $dTipo, $dProducto]));

        // Cascading final price
        $precioFinal = $precio;
        foreach ($partes as $d) {
            $precioFinal *= (1 - $d / 100);
        }

        return [
            'precio_original' => $precio,
            'partes'          => $partes,
            'precio_final'    => $precioFinal,
        ];
    }

    public function mount()
    {
        if (!Auth::guard('cliente')->check()) {
            session()->flash('openLoginModal', true);
            return redirect()->route('home');
        }

        if (session()->has('toast')) {
            $toast = session('toast');
            $this->dispatch('producto-agregado', [
                'message' => $toast['message'],
                'type'    => $toast['type'],
            ]);
        }
    }

    public function cerrarModalOfertas()
    {
        $this->mostrarModalOfertas = false;
    }

    public function siguienteProductoModal()
    {
        if ($this->productoActualModal < count($this->productosEnOferta) - 1) {
            $this->productoActualModal++;
        } else {
            $this->productoActualModal = 0;
        }
    }

    public function anteriorProductoModal()
    {
        if ($this->productoActualModal > 0) {
            $this->productoActualModal--;
        }
    }

    public function agregarAlCarritoDesdeModal($productoId)
    {
        $this->agregarAlCarrito($productoId);
    }

    public function abrirDetalleProducto(int $productoId): void
    {
        $this->productoDetalleId = $productoId;
        $this->modalImagenActual = null;
        $this->modalImagenActualTipo = null;
    }

    public function cerrarDetalleProducto(): void
    {
        $this->productoDetalleId = null;
        $this->modalImagenActual = null;
        $this->modalImagenActualTipo = null;
    }

    public function cambiarImagenDetalle(string $ruta, string $tipo = 'galeria'): void
    {
        $this->modalImagenActual = $ruta;
        $this->modalImagenActualTipo = $tipo;
    }

    public function agregarAlCarrito($productoId)
    {
        $producto = Producto::find($productoId);

        if (!$producto) {
            $this->dispatch('producto-agregado', ['message' => 'Producto no encontrado', 'type' => 'error']);
            return;
        }

        $cantidad  = $this->normalizarCantidad($this->cantidades[$productoId] ?? 1);
        $this->cantidades[$productoId] = $cantidad;
        $clienteId = Auth::guard('cliente')->id();

        $itemCarrito = Carrito::where('cliente_id', $clienteId)
            ->where('producto_id', $productoId)
            ->first();

        if ($itemCarrito) {
            $itemCarrito->cantidad       += $cantidad;
            $itemCarrito->precio_unitario = $producto->precio ?? 0;
            $itemCarrito->save();
            $mensaje = "¡{$producto->codigo_ralux} actualizado en el carrito!";
        } else {
            Carrito::create([
                'cliente_id'        => $clienteId,
                'producto_id'       => $productoId,
                'cantidad'          => $cantidad,
                'precio_unitario'   => $producto->precio ?? 0,
                'descuento_unitario' => 0,
            ]);
            $mensaje = "¡{$producto->codigo_ralux} agregado al carrito!";
        }

        $this->dispatch('producto-agregado', ['message' => $mensaje]);
        $this->dispatch('carrito-actualizado');
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
        $this->resetPage();
    }

    public function updatedTipoId()
    {
        $this->modelo_id = null;
        $this->resetPage();
    }

    public function updatedModeloId()
    {
        $this->resetPage();
        if ($this->modelo_id) {
            $modelo = Modelo::find($this->modelo_id);
            if ($modelo && $modelo->marca_id) {
                $this->marca_id = $modelo->marca_id;
            }
        }
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function buscar()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset(['busqueda', 'tipo_id', 'marca_id', 'modelo_id']);
        $this->resetPage();
    }

    public function incrementar($productoId)
    {
        $cantidad = $this->normalizarCantidad($this->cantidades[$productoId] ?? 1);
        $this->cantidades[$productoId] = min(999, $cantidad + 1);
    }

    public function decrementar($productoId)
    {
        $cantidad = $this->normalizarCantidad($this->cantidades[$productoId] ?? 1);
        $this->cantidades[$productoId] = max(1, $cantidad - 1);
    }

    public function updatedCantidades($value, $productoId)
    {
        $this->cantidades[$productoId] = $this->normalizarCantidad($value);
    }

    private function normalizarCantidad($cantidad): int
    {
        $cantidad = (int) preg_replace('/\D/', '', (string) $cantidad);

        return min(999, max(1, $cantidad));
    }

    public function render()
    {
        $tipos = ProductoTipo::where('visible', true)
            ->whereHas('productos', fn($q) => $q->where('visible', true))
            ->orderBy('descripcion_es')
            ->get();

        $marcas = Marca::where('visible', true)
            ->whereNotNull('descripcion_es')
            ->where('descripcion_es', '!=', '')
            ->whereHas('productos', fn($q) => $q->where('visible', true))
            ->orderBy('descripcion_es')
            ->get()
            ->filter(fn($m) => filled(trim($m->descripcion_es)));

        $modelos = Modelo::where('visible', true)
            ->when($this->marca_id, fn($q) => $q->whereIn('marca_id', ProductSearch::equivalentMarcaIds($this->marca_id)))
            ->when($this->tipo_id, fn($q) => $q->whereHas('productos', function ($p) {
                $p->where('visible', true)->where('producto_tipo_id', $this->tipo_id);
            }))
            ->orderBy('descripcion_es')
            ->get();

        $this->modelosOptions = $modelos
            ->filter(fn($m) => filled(trim($m->descripcion_es, '- ')))
            ->map(fn($m) => ['value' => (string) $m->id, 'label' => trim($m->descripcion_es)])
            ->values()
            ->toArray();

        $productos = $this->filtrarProductos();
        $productoDetalle = $this->productoDetalleId
            ? Producto::with(['tipo', 'imagenes', 'marcas', 'modelos.marca', 'codigosOM.marca'])
                ->where('visible', true)
                ->find($this->productoDetalleId)
            : null;

        if ($productoDetalle && !$this->modalImagenActual) {
            $principal = $productoDetalle->imagenes->firstWhere('principal', true)
                ?? $productoDetalle->imagenes->where('tipo', '!=', 'video')->first()
                ?? $productoDetalle->imagenes->first();

            $this->modalImagenActual = $principal?->ruta
                ?? $productoDetalle->imagen_diagrama
                ?? $productoDetalle->diagrama_orientativo;

            $this->modalImagenActualTipo = $principal?->tipo
                ?? (($productoDetalle->imagen_diagrama || $productoDetalle->diagrama_orientativo) ? 'diagrama' : 'galeria');
        }

        if ($productos) {
            foreach ($productos as $producto) {
                if (!isset($this->cantidades[$producto->id])) {
                    $this->cantidades[$producto->id] = 1;
                }
            }
        }

        return view('livewire.zona.productos-zona', [
            'tipos'    => $tipos,
            'marcas'   => $marcas,
            'modelos'  => $modelos,
            'productos' => $productos,
            'productoDetalle' => $productoDetalle,
            'config' => CarritoConfig::first(),
        ]);
    }

    protected function filtrarProductos()
    {
        $query = Producto::query()
            ->with(['marcas', 'modelos', 'imagenPrincipal', 'tipo'])
            ->where('visible', true);

        ProductSearch::applyTipoFilter($query, $this->tipo_id);
        ProductSearch::applyMarcaFilter($query, $this->marca_id);
        ProductSearch::applyModeloFilter($query, $this->modelo_id);
        ProductSearch::applyTextSearch($query, $this->busqueda);
        ProductSearch::applyRelevanceOrder($query, $this->busqueda);

        return $query->paginate($this->perPage);
    }
}
