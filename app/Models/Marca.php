<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marcas';

    protected $fillable = [
       'id', 'descripcion_es', 'descripcion_en', 'estado',
        'destacado', 'visible', 'orden', 'vehiculo_tipo_id',
    ];

    protected $casts = [
        'estado'    => 'boolean',
        'destacado' => 'boolean',
        'visible'   => 'boolean',
    ];

    public function vehiculoTipo()
    {
        return $this->belongsTo(VehiculoTipo::class, 'vehiculo_tipo_id');
    }

    public function modelos()
    {
        return $this->hasMany(Modelo::class, 'marca_id');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'marca_producto');
    }
}