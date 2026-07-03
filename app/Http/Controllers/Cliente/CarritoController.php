<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Mail\PedidoMail;
use App\Models\Carrito;
use App\Models\CarritoConfig;
use App\Models\Contact;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Support\CarritoIva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CarritoController extends Controller
{
    private function calcularDescuentos($precio, $producto, $cliente)
    {
        $precio  = (float)($precio ?? 0);

        $dC1       = (float)($cliente?->descuento        ?? 0);
        $dC2       = (float)($cliente?->descuento2       ?? 0);
        $dC3       = (float)($cliente?->descuento3       ?? 0);
        $dTipo     = (float)($producto->tipo?->descuento ?? 0);
        $dProducto = (float)($producto->descuento        ?? 0);

        $p0 = $precio;
        $p1 = $p0 * (1 - $dC1 / 100);
        $p2 = $p1 * (1 - $dC2 / 100);
        $p3 = $p2 * (1 - $dC3 / 100);
        $p4 = $p3 * (1 - $dTipo / 100);
        $p5 = $p4 * (1 - $dProducto / 100);

        return [
            'precio_original' => $precio,
            'precio_final'    => $p5,
            'descuento_unit'  => $p0 - $p5,
            'monto_c1'        => $p0 - $p1,
            'monto_c2'        => $p1 - $p2,
            'monto_c3'        => $p2 - $p3,
            'monto_tipo'      => $p3 - $p4,
            'monto_producto'  => $p4 - $p5,
            'd_c1'            => $dC1,
            'd_c2'            => $dC2,
            'd_c3'            => $dC3,
        ];
    }

    public function realizarPedido(Request $request)
    {
        $request->validate([
            'forma_pago' => 'required|in:contado,transferencia,cuenta_corriente',
            'archivo'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $clienteId = Auth::guard('cliente')->id();

        $items = Carrito::with('producto.tipo')
            ->where('cliente_id', $clienteId)
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Tu carrito está vacío.');
        }

        $config  = CarritoConfig::first();
        $cliente = Auth::guard('cliente')->user();
        $dC1 = (float)($cliente?->descuento  ?? 0);
        $dC2 = (float)($cliente?->descuento2 ?? 0);
        $dC3 = (float)($cliente?->descuento3 ?? 0);

        // Calcular totales (incluye descuentos de cliente, tipo y producto)
        $subtotalSinDescuento = 0;
        $totalDescuentoC1 = 0;
        $totalDescuentoC2 = 0;
        $totalDescuentoC3 = 0;
        $totalDescuentoTipo = 0;
        $totalDescuentoProducto = 0;
        foreach ($items as $item) {
            $subtotalSinDescuento += $item->precio_unitario * $item->cantidad;
            $desc = $this->calcularDescuentos($item->precio_unitario, $item->producto, $cliente);
            $totalDescuentoC1 += $desc['monto_c1'] * $item->cantidad;
            $totalDescuentoC2 += $desc['monto_c2'] * $item->cantidad;
            $totalDescuentoC3 += $desc['monto_c3'] * $item->cantidad;
            $totalDescuentoTipo += $desc['monto_tipo'] * $item->cantidad;
            $totalDescuentoProducto += $desc['monto_producto'] * $item->cantidad;
        }
        $totalDescuentoCliente = $totalDescuentoC1 + $totalDescuentoC2 + $totalDescuentoC3;
        $totalDescuentosItems = $totalDescuentoCliente + $totalDescuentoTipo + $totalDescuentoProducto;
        $subtotalConDescuentosItems = max(0, $subtotalSinDescuento - $totalDescuentosItems);

        $descuentoPorPago = 0;
        $tasa = 0;
        if ($config) {
            $tasa = match ($request->forma_pago) {
                'contado'          => $config->contado,
                'transferencia'    => $config->transferencia,
                'cuenta_corriente' => $config->corriente,
                default            => 0,
            };
            $descuentoPorPago = $subtotalConDescuentosItems * ($tasa / 100);
        }

        $subtotal        = $subtotalConDescuentosItems - $descuentoPorPago;
        if ($subtotal < 0) {
            $subtotal = 0;
        }

        $ivaDetalle = [];
        foreach ($items as $item) {
            $desc = $this->calcularDescuentos($item->precio_unitario, $item->producto, $cliente);
            $baseItem = max(0, ($desc['precio_final'] * $item->cantidad) * (1 - $tasa / 100));
            $ivaItem = CarritoIva::ivaProducto($item->producto, $config);

            $ivaDetalle = CarritoIva::agregarLinea($ivaDetalle, $ivaItem, $baseItem);
        }

        $ivaDetalle = CarritoIva::detalleOrdenado($ivaDetalle);
        $iva = $ivaDetalle->sum('iva');
        $porcentajeIva = $subtotal > 0 ? ($iva / $subtotal) * 100 : CarritoIva::ivaGeneral($config);
        $total = $subtotal + $iva;

        // Manejo del archivo adjunto
        $archivoPath   = null;
        $archivoNombre = null;
        $archivoMime   = null;
        if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
            $archivoMime   = $request->file('archivo')->getMimeType();
            $archivoPath   = $request->file('archivo')->store('pedidos', 'public');
            $archivoNombre = $request->file('archivo')->getClientOriginalName();
        }

        // Crear el pedido
        $pedido = Pedido::create([
            'numero_pedido'          => Pedido::generarNumeroPedido(),
            'cliente_id'             => $clienteId,
            'forma_pago'             => $request->forma_pago,
            'mensaje'                => $request->mensaje,
            'archivo_path'           => $archivoPath,
            'archivo_nombre'         => $archivoNombre,
            'subtotal_sin_descuento' => $subtotalSinDescuento,
            'descuentos'             => $totalDescuentosItems + $descuentoPorPago,
            'porcentaje_descuento'   => $subtotalSinDescuento > 0 ? (($totalDescuentosItems + $descuentoPorPago) / $subtotalSinDescuento) * 100 : 0,
            'descuento_cliente'      => $totalDescuentoCliente,
            'descuento_tipo'         => $totalDescuentoTipo,
            'descuento_producto'     => $totalDescuentoProducto,
            'descuento_pago'         => $descuentoPorPago,
            'porcentaje_descuento_c1' => $dC1,
            'porcentaje_descuento_c2' => $dC2,
            'porcentaje_descuento_c3' => $dC3,
            'porcentaje_descuento_pago' => $tasa,
            'subtotal'               => $subtotal,
            'porcentaje_iva'         => $porcentajeIva,
            'iva'                    => $iva,
            'iva_detalle'            => $ivaDetalle->all(),
            'total'                  => $total,
            'fecha_compra'           => now()->toDateString(),
            'fecha_entrega'          => null,
            'entregado'              => false,
        ]);

        // Crear los items del pedido
        foreach ($items as $item) {
            $desc = $this->calcularDescuentos($item->precio_unitario, $item->producto, $cliente);
            PedidoItem::create([
                'pedido_id'       => $pedido->id,
                'producto_id'     => $item->producto_id,
                'codigo_producto' => $item->producto->codigo_ralux ?? '',
                'nombre_producto' => $item->producto->descripcion_es ?? '',
                'cantidad'        => $item->cantidad,
                'precio_unitario' => $item->precio_unitario,
                'descuento_unitario' => $desc['descuento_unit'],
                'descuento_cliente'  => ($desc['monto_c1'] + $desc['monto_c2'] + $desc['monto_c3']),
                'descuento_tipo'     => $desc['monto_tipo'],
                'descuento_producto' => $desc['monto_producto'],
                'subtotal'        => $desc['precio_final'] * $item->cantidad,
            ]);
        }

        $this->enviarMailsPedido(
            $pedido->fresh(['cliente', 'items']),
            $this->buildPedidoMailData($pedido, $cliente, $items, [
                'archivo_path' => $archivoPath ? Storage::disk('public')->path($archivoPath) : null,
                'archivo_nombre' => $archivoNombre,
                'archivo_mime' => $archivoMime,
            ])
        );

        // Vaciar el carrito
        Carrito::where('cliente_id', $clienteId)->delete();

        return redirect()
            ->route('cliente.pedidos')
            ->with('toast', [
                'message' => "Pedido #{$pedido->numero_pedido} realizado con éxito.",
                'type'    => 'success',
            ]);
    }

    private function buildPedidoMailData(Pedido $pedido, $cliente, $items, array $archivo): array
    {
        return [
            'pedido' => [
                'numero' => $pedido->numero_pedido,
                'fecha' => $pedido->fecha_compra?->format('d/m/Y') ?? now()->format('d/m/Y'),
            ],
            'cliente' => [
                'nombre' => $cliente->nombre,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
                'domicilio' => $cliente->domicilio,
                'localidad' => $cliente->localidad,
                'provincia' => $cliente->provincia,
            ],
            'forma_pago' => $pedido->forma_pago,
            'productos' => $items->map(function ($item) use ($cliente) {
                $desc = $this->calcularDescuentos($item->precio_unitario, $item->producto, $cliente);

                return [
                    'codigo' => $item->producto->codigo_ralux ?? '',
                    'nombre' => $item->producto->descripcion_es ?? '',
                    'precio_unitario' => $item->precio_unitario,
                    'precio_con_descuento' => $desc['precio_final'],
                    'cantidad' => $item->cantidad,
                    'subtotal' => $desc['precio_final'] * $item->cantidad,
                ];
            })->all(),
            'resumen' => [
                'subtotal_sin_descuento' => $pedido->subtotal_sin_descuento,
                'descuentos' => $pedido->descuentos,
                'porcentaje_descuento' => $pedido->porcentaje_descuento,
                'subtotal' => $pedido->subtotal,
                'porcentaje_iva' => $pedido->porcentaje_iva,
                'iva_detalle' => $pedido->iva_detalle ?? [],
                'iva' => $pedido->iva,
                'total' => $pedido->total,
            ],
            'mensaje' => $pedido->mensaje,
            'archivo_path' => $archivo['archivo_path'],
            'archivo_nombre' => $archivo['archivo_nombre'],
            'archivo_mime' => $archivo['archivo_mime'],
        ];
    }

    private function enviarMailsPedido(Pedido $pedido, array $mailData): void
    {
        $mailAdm = Contact::query()->value('mail_adm');

        $destinatarios = [];

        if ($mailAdm) {
            $destinatarios[] = [
                'email' => $mailAdm,
                'subject' => 'Nuevo Pedido - '.$mailData['cliente']['nombre'].' - #'.$pedido->numero_pedido,
                'titulo' => 'Nuevo Pedido Recibido',
            ];
        }

        if ($pedido->cliente?->email) {
            $destinatarios[] = [
                'email' => $pedido->cliente->email,
                'subject' => 'Pedido recibido - #'.$pedido->numero_pedido,
                'titulo' => 'Recibimos tu pedido',
            ];
        }

        foreach ($destinatarios as $destinatario) {
            try {
                Mail::to($destinatario['email'])->send(new PedidoMail(array_merge($mailData, [
                    'subject' => $destinatario['subject'],
                    'titulo' => $destinatario['titulo'],
                ])));
            } catch (\Throwable $e) {
                Log::error('Error al enviar email de pedido realizado: '.$e->getMessage(), [
                    'pedido_id' => $pedido->id,
                    'email' => $destinatario['email'],
                ]);
            }
        }
    }
}
