<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equivalencia extends Model
{
    protected $table = 'equivalencias';

    protected $fillable = [
        'producto_id',
        'codigo',
        'distribuidor',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
