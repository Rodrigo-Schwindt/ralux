<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehiculoTipo extends Model
{
    protected $table = 'vehiculo_tipo';

    protected $fillable = [
        'id', 'descripcion_es', 'descripcion_en', 'estado',
        'destacado', 'visible', 'orden',
    ];

    protected $casts = [
        'estado'    => 'boolean',
        'destacado' => 'boolean',
        'visible'   => 'boolean',
    ];

    public function marcas()
    {
        return $this->hasMany(Marca::class, 'vehiculo_tipo_id');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'vehiculo_tipo_producto');
    }
}