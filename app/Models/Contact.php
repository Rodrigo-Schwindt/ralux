<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contact';

    protected $fillable = [
        'image_banner',
        'direction_adm',
        'direction_sale',
        'phone_amd',
        'phone_sale',
        'maps_adm',
        'maps_sale',
        'frame_adm',
        'mail_adm',
        'mail_comercial',
        'mail_tecnico',
        'mail_compras',
        'mail_logistica',
        'wssp',
        'facebook',
        'insta',
        'linkedin',
        'youtube',
        'icono_1',
        'icono_2',
        'icono_3'
    ];
}