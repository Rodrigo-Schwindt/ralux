<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo_ralux', 'descripcion_es', 'descripcion_en',
        'voltaje', 'amperaje', 'terminales', 'soporte',
        'caract_1', 'valor_1', 'caract_2', 'valor_2',
        'caract_3', 'valor_3', 'caract_4', 'valor_4',
        'caract_5', 'valor_5', 'caract_6', 'valor_6',
        'caract_7', 'valor_7', 'caract_8', 'valor_8',
        'caract_9', 'valor_9', 'caract_10', 'valor_10',
        'precio', 'descuento', 'periodo_desde', 'periodo_hasta',
        'safe_url', 'pdf', 'sonido', 'video',
        'estado', 'destacado', 'visible', 'orden',
        'producto_tipo_id', 'imagen_diagrama', 'diagrama_orientativo',
    ];

    protected $casts = [
        'soporte'       => 'boolean',
        'estado'        => 'boolean',
        'destacado'     => 'boolean',
        'visible'       => 'boolean',
        'voltaje'       => 'string',
        'amperaje'      => 'string',
        'terminales'    => 'string',
        'precio'        => 'decimal:2',
        'descuento'     => 'decimal:2',
        'imagen_diagrama' => 'string',
        'diagrama_orientativo' => 'string',
        'periodo_desde' => 'date',
        'periodo_hasta' => 'date',
    ];

    public function tipo()
    {
        return $this->belongsTo(ProductoTipo::class, 'producto_tipo_id');
    }

    public function marcas()
    {
        return $this->belongsToMany(Marca::class, 'marca_producto');
    }

    public function modelos()
    {
        return $this->belongsToMany(Modelo::class, 'modelo_producto');
    }

    public function vehiculoTipos()
    {
        return $this->belongsToMany(VehiculoTipo::class, 'vehiculo_tipo_producto');
    }
    public function imagenes()
{
    return $this->hasMany(ProductoImagen::class)->orderBy('orden');
}

public function imagenPrincipal()
{
    return $this->hasOne(ProductoImagen::class)->where('principal', true);
}

public function equivalencias()
{
    return $this->hasMany(Equivalencia::class)->orderBy('distribuidor');
}

public function codigosOM()
{
    return $this->hasMany(CodigoOM::class);
}
}
