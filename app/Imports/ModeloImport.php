<?php

namespace App\Imports;

use App\Models\Modelo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ModeloImport implements ToModel, WithHeadingRow
{
public function model(array $row)
{
    if (empty($row['id'])) {
        return null;
    }

    return new Modelo([
        'id'             => $row['id'],
        'marca_id'       => $row['id_vehiculo_marca'],
        'descripcion_es' => $row['descripcion_es'],
        'descripcion_en' => $row['descripcion_en'],
        'estado'         => $row['estado'] ?? 1,
    ]);
}
}