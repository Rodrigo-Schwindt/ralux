<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MarcaProductoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['id'])) {
            return null;
        }

        DB::table('marca_producto')->insertOrIgnore([
            'producto_id' => $row['id_producto'],
            'marca_id'    => $row['id_vehiculo_marca'],
        ]);

        return null;
    }
}