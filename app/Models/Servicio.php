<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicio';

    protected $fillable = [
        'title',
        'description_1',
        'image',
        'title_s',
        'description_s',
        'image_s',
    ];

    public function downloads()
    {
        return $this->hasMany(ServicioDownload::class)->orderBy('orden');
    }
}
