<?php

namespace App\Imports;

use App\Models\CodigoOM;
use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CodigoOMImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['id_producto']) || empty($row['codigo'])) {
            return null;
        }

        if (!Producto::where('id', $row['id_producto'])->exists()) {
            return null;
        }

        CodigoOM::firstOrCreate([
            'producto_id' => $row['id_producto'],
            'marca_id'    => $row['id_vehiculo_marca'] ?? null,
            'codigo'      => trim($row['codigo']),
        ]);

        return null;
    }
}
