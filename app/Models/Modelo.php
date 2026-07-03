<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $table = 'modelos';

    protected $fillable = [
        'id', 'descripcion_es', 'descripcion_en', 'estado',
        'destacado', 'visible', 'orden', 'marca_id', 'imagen_diagrama',
    ];

    protected $casts = [
        'estado'    => 'boolean',
        'destacado' => 'boolean',
        'visible'   => 'boolean',
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'modelo_producto');
    }
}