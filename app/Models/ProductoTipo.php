<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoTipo extends Model
{
    protected $table = 'productos_tipo';

    protected $fillable = [
        'descripcion_es', 'descripcion_en', 'estado',
        'destacado', 'visible', 'orden', 'descuento',
    ];

    protected $casts = [
        'estado'    => 'boolean',
        'destacado' => 'boolean',
        'visible'   => 'boolean',
        'descuento' => 'decimal:2',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'producto_tipo_id');
    }
    public function imagenes()
{
    return $this->hasMany(ProductoImagen::class)->orderBy('orden');
}

public function imagenPrincipal()
{
    return $this->hasOne(ProductoImagen::class)->where('principal', true);
}
}