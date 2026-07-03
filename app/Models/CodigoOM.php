<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoOM extends Model
{
    public $timestamps = false;

    protected $table = 'codigo_om';

    protected $fillable = ['producto_id', 'marca_id', 'codigo'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
}
