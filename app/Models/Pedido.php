<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_pedido',
        'cliente_id',
        'forma_pago',
        'mensaje',
        'archivo_path',
        'archivo_nombre',
        'subtotal_sin_descuento',
        'descuentos',
        'porcentaje_descuento',
        'descuento_cliente',
        'descuento_tipo',
        'descuento_producto',
        'descuento_pago',
        'porcentaje_descuento_c1',
        'porcentaje_descuento_c2',
        'porcentaje_descuento_c3',
        'porcentaje_descuento_pago',
        'subtotal',
        'porcentaje_iva',
        'iva',
        'iva_detalle',
        'total',
        'fecha_compra',
        'fecha_entrega',
        'entregado',
        'fecha_entregado',
        'cancelado',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_entrega' => 'date',
        'fecha_entregado' => 'date',
        'entregado'  => 'boolean',
        'cancelado'  => 'boolean',
        'subtotal_sin_descuento' => 'decimal:2',
        'descuentos' => 'decimal:2',
        'porcentaje_descuento' => 'decimal:2',
        'descuento_cliente' => 'decimal:2',
        'descuento_tipo' => 'decimal:2',
        'descuento_producto' => 'decimal:2',
        'descuento_pago' => 'decimal:2',
        'porcentaje_descuento_c1' => 'decimal:2',
        'porcentaje_descuento_c2' => 'decimal:2',
        'porcentaje_descuento_c3' => 'decimal:2',
        'porcentaje_descuento_pago' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'porcentaje_iva' => 'decimal:2',
        'iva' => 'decimal:2',
        'iva_detalle' => 'array',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public static function generarNumeroPedido()
    {
        $ultimoPedido = static::latest('id')->first();
        $numero = $ultimoPedido ? intval(substr($ultimoPedido->numero_pedido, 0)) + 1 : 1;
        return str_pad($numero, 8, '0', STR_PAD_LEFT);
    }

    public function marcarComoEntregado()
    {
        $this->update([
            'entregado'       => true,
            'fecha_entregado' => now(),
            'fecha_entrega'   => $this->fecha_entrega ?? today(),
        ]);
    }
}
