<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ModeloProductoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['id'])) {
            return null;
        }

        DB::table('modelo_producto')->insertOrIgnore([
            'producto_id' => $row['id_producto'],
            'modelo_id'   => $row['id_modelo'],
        ]);

        return null;
    }
}