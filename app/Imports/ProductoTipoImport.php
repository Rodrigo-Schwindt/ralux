<?php

namespace App\Imports;

use App\Models\ProductoTipo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductoTipoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['id'])) {
            return null;
        }

        return new ProductoTipo([
            'id'             => $row['id'],
            'descripcion_es' => $row['descripcion_es'],
            'descripcion_en' => $row['descripcion_en'],
            'estado'         => $row['estado'] ?? 1,
        ]);
    }
}