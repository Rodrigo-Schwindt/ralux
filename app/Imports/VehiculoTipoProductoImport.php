<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VehiculoTipoProductoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['id'])) {
            return null;
        }

        DB::table('vehiculo_tipo_producto')->insertOrIgnore([
            'producto_id'     => $row['id_producto'],
            'vehiculo_tipo_id' => $row['id_vehiculo_tipo'],
        ]);

        return null;
    }
}