<?php

namespace App\Imports;

use App\Models\Marca;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class MarcaImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
public function model(array $row)
{
    if (empty($row['id'])) {
        return null;
    }

    return new Marca([
        'id'             => $row['id'],
        'descripcion_es' => $row['descripcion_es'],
        'descripcion_en' => $row['descripcion_en'],
        'estado'         => $row['estado'] ?? 1,
    ]);
}
}