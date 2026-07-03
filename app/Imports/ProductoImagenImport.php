<?php

namespace App\Imports;

use App\Models\ProductoImagen;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Producto;

class ProductoImagenImport implements ToModel, WithHeadingRow
{
public function model(array $row)
{
    if (empty($row['id_producto']) || empty($row['nombre_imagen'])) {
        return null;
    }

    $productoId = (int) $row['id_producto'];

    if (!Producto::find($productoId)) {
        return null;
    }

    $ruta = 'productos/imagenes/' . trim($row['nombre_imagen']);
    $hasPrincipal = ProductoImagen::where('producto_id', $productoId)
        ->where('principal', true)
        ->exists();

    $maxOrden = ProductoImagen::where('producto_id', $productoId)->max('orden');
    $orden = is_null($maxOrden) ? 0 : ((int) $maxOrden + 1);

    $imagen = ProductoImagen::firstOrNew([
        'producto_id' => $productoId,
        'ruta' => $ruta,
    ]);

    if (!$imagen->exists) {
        $imagen->orden = $orden;
        $imagen->principal = !$hasPrincipal;
    } elseif (!$hasPrincipal) {
        // Si ya existía pero ninguna imagen era principal, esta pasa a principal.
        $imagen->principal = true;
    }

    $imagen->tipo = 'galeria';
    $imagen->save();

    return null;
}
}
