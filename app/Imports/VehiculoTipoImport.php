<?php

namespace App\Imports;

use App\Models\VehiculoTipo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VehiculoTipoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new VehiculoTipo([
            'id'             => $row['id'],
            'descripcion_es' => $row['descripcion_es'],
            'descripcion_en' => $row['descripcion_en'],
            'estado'         => $row['estado'],
        ]);
        
    }
}