<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    protected $table = 'catalogo';

    protected $fillable = [
        'title',
        'subtitle',
        'descripcion',
        'image_1',
        'image_2',
        'orden',
        'visible',
        'pdf',
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];
}