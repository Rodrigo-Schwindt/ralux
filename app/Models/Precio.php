<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    protected $fillable = ['title', 'archivo', 'tipo', 'publicado', 'generado_at'];

    protected $casts = [
        'publicado' => 'boolean',
        'generado_at' => 'datetime',
    ];
}
