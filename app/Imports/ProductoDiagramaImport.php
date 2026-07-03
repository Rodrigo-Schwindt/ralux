<?php

namespace App\Imports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductoDiagramaImport implements ToModel, WithHeadingRow
{
public function model(array $row)
{
    if (empty($row['id'])) {
        return null;
    }

    $diagrama = $row['nombre_imagen_pinout'] ?? $row['nombre_imagen_esquema_electrico'] ?? null;

    if (empty($diagrama)) {
        return null;
    }

    Producto::where('id', $row['id_producto'])->update([
        'imagen_diagrama' => 'productos/diagramas/' . $diagrama,
    ]);

    return null;
}
}