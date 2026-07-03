<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioDownload extends Model
{
    protected $table = 'servicio_downloads';

    protected $fillable = [
        'servicio_id',
        'title',
        'description',
        'image',
        'file',
        'orden',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }
}
