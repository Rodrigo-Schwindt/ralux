<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use App\Models\Carrito;
use App\Models\CarritoConfig;
use App\Models\Producto;
use App\Support\CarritoIva;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.zone')]
class CarritoZona extends Component
{
    public $formaPago = 'contado';
    public ?int $productoDetalleId = null;
    public ?string $modalImagenActual = null;
    public ?string $modalImagenActualTipo = null;

    protected $listeners = ['carrito-actualizado' => '$refresh'];

    public function mount()
    {
        if (!Auth::guard('cliente')->check()) {
            session()->flash('openLoginModal', true);
            return redirect()->route('home');
        }
    }

    #[On('carrito-actualizado')]
    public function actualizarCarrito()
    {
    }

    public function incrementar($carritoId)
    {
        $item = Carrito::find($carritoId);
        if ($item && $item->cliente_id === Auth::guard('cliente')->id()) {
            $item->cantidad++;
            $item->save();
        }
    }

    public function decrementar($carritoId)
    {
        $item = Carrito::find($carritoId);
        if ($item && $item->cliente_id === Auth::guard('cliente')->id() && $item->cantidad > 1) {
            $item->cantidad--;
            $item->save();
        }
    }

    public function eliminar($carritoId)
    {
        $item = Carrito::find($carritoId);
        if ($item && $item->cliente_id === Auth::guard('cliente')->id()) {
            $codigo = $item->producto->code ?? 'Producto';
            $item->delete();
            
            $this->dispatch('producto-agregado', [
                'message' => "¡{$codigo} eliminado del carrito!"
            ]);
        }
    }

    public function abrirDetalleProducto(int $productoId): void
    {
        $producto = Producto::with('imagenes')
            ->where('visible', true)
            ->find($productoId);

        if (!$producto) {
            return;
        }

        $principal = $producto->imagenes->firstWhere('principal', true)
            ?? $producto->imagenes->where('tipo', '!=', 'video')->first()
            ?? $producto->imagenes->first();

        $this->productoDetalleId = $producto->id;
        $this->modalImagenActual = $principal?->ruta
            ?? $producto->imagen_diagrama
            ?? $producto->diagrama_orientativo;

        $this->modalImagenActualTipo = $principal?->tipo
            ?? (($producto->imagen_diagrama || $producto->diagrama_orientativo) ? 'diagrama' : 'galeria');
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

        // Cascade step by step
        $p0 = $precio;
        $p1 = $p0 * (1 - $dC1 / 100);
        $p2 = $p1 * (1 - $dC2 / 100);
        $p3 = $p2 * (1 - $dC3 / 100);
        $p4 = $p3 * (1 - $dTipo / 100);
        $p5 = $p4 * (1 - $dProducto / 100);

        return [
            'precio_original' => $precio,
            'partes'          => $partes,
            'precio_final'    => $p5,
            'monto_c1'        => $p0 - $p1,
            'monto_c2'        => $p1 - $p2,
            'monto_c3'        => $p2 - $p3,
            'monto_tipo'      => $p3 - $p4,
            'monto_producto'  => $p4 - $p5,
            'monto_cliente'   => $p0 - $p3,
            'd_c1'            => $dC1,
            'd_c2'            => $dC2,
            'd_c3'            => $dC3,
            'd_tipo'          => $dTipo,
            'd_producto'      => $dProducto,
        ];
    }

    public function render()
    {
        $config = CarritoConfig::first();

        $cliente = Auth::guard('cliente')->user();
        $partesCliente = array_values(array_filter([
            (float)($cliente?->descuento  ?? 0),
            (float)($cliente?->descuento2 ?? 0),
            (float)($cliente?->descuento3 ?? 0),
        ]));

        $items = Carrito::with(['producto.imagenPrincipal', 'producto.tipo', 'producto.marcas', 'producto.modelos'])
            ->where('cliente_id', Auth::guard('cliente')->id())
            ->get();
        $productoDetalle = $this->productoDetalleId
            ? Producto::with(['tipo', 'imagenes', 'marcas', 'modelos.marca', 'codigosOM.marca'])
                ->where('visible', true)
                ->find($this->productoDetalleId)
            : null;

        $subtotalSinDescuento   = 0;
        $totalDescuentoC1       = 0;
        $totalDescuentoC2       = 0;
        $totalDescuentoC3       = 0;
        $totalDescuentoTipo     = 0;
        $totalDescuentoProducto = 0;

        foreach ($items as $item) {
            $precioOriginal = $item->precio_unitario;
            $desc = $this->calcularDescuentos($precioOriginal, $item->producto);

            $subtotalSinDescuento   += $precioOriginal        * $item->cantidad;
            $totalDescuentoC1       += $desc['monto_c1']      * $item->cantidad;
            $totalDescuentoC2       += $desc['monto_c2']      * $item->cantidad;
            $totalDescuentoC3       += $desc['monto_c3']      * $item->cantidad;
            $totalDescuentoTipo     += $desc['monto_tipo']    * $item->cantidad;
            $totalDescuentoProducto += $desc['monto_producto'] * $item->cantidad;
        }

        $totalDescuentoCliente = $totalDescuentoC1 + $totalDescuentoC2 + $totalDescuentoC3;
        $totalDescuentosItems = $totalDescuentoCliente + $totalDescuentoTipo + $totalDescuentoProducto;
        $subtotalConDescuentosItems = max(0, $subtotalSinDescuento - $totalDescuentosItems);

        $descuentoPorPago = 0;
        $porcentajeDescuentoPago = 0;
        if ($config) {
            if ($this->formaPago === 'contado') {
                $porcentajeDescuentoPago = (float) $config->contado;
            } elseif ($this->formaPago === 'transferencia') {
                $porcentajeDescuentoPago = (float) $config->transferencia;
            } elseif ($this->formaPago === 'cuenta_corriente') {
                $porcentajeDescuentoPago = (float) $config->corriente;
            }

            $descuentoPorPago = $subtotalConDescuentosItems * ($porcentajeDescuentoPago / 100);
        }

        $totalDescuentos = $totalDescuentosItems + $descuentoPorPago;

        $subtotalFinal = $subtotalConDescuentosItems - $descuentoPorPago;

        if ($subtotalFinal < 0) {
            $subtotalFinal = 0;
        }

        $ivaDetalle = [];
        foreach ($items as $item) {
            $desc = $this->calcularDescuentos($item->precio_unitario, $item->producto);
            $baseItem = max(0, ($desc['precio_final'] * $item->cantidad) * (1 - $porcentajeDescuentoPago / 100));
            $ivaItem = CarritoIva::ivaProducto($item->producto, $config);

            $ivaDetalle = CarritoIva::agregarLinea($ivaDetalle, $ivaItem, $baseItem);
        }

        $ivaDetalle = CarritoIva::detalleOrdenado($ivaDetalle);
        $iva = $ivaDetalle->sum('iva');
        $totalFinal = $subtotalFinal + $iva;

        $dC1 = (float)($cliente?->descuento  ?? 0);
        $dC2 = (float)($cliente?->descuento2 ?? 0);
        $dC3 = (float)($cliente?->descuento3 ?? 0);

        return view('livewire.zona.carrito-zona', [
            'config'                   => $config,
            'items'                    => $items,
            'partesCliente'            => $partesCliente,
            'subtotalSinDescuento'     => $subtotalSinDescuento,
            'totalDescuentoC1'         => $totalDescuentoC1,
            'totalDescuentoC2'         => $totalDescuentoC2,
            'totalDescuentoC3'         => $totalDescuentoC3,
            'totalDescuentoCliente'    => $totalDescuentoCliente,
            'totalDescuentoTipo'       => $totalDescuentoTipo,
            'totalDescuentoProducto'   => $totalDescuentoProducto,
            'dC1'                      => $dC1,
            'dC2'                      => $dC2,
            'dC3'                      => $dC3,
            'descuentos'               => $totalDescuentos,
            'descuentoPorPago'         => $descuentoPorPago,
            'subtotalConDescuentoPago' => $subtotalFinal,
            'iva'                      => $iva,
            'ivaDetalle'               => $ivaDetalle,
            'total'                    => $totalFinal,
            'productoDetalle'          => $productoDetalle,
        ]);
    }
}
