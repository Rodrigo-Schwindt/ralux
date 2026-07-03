<?php

namespace App\Imports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class ClienteImport implements ToModel, WithHeadingRow
{
    private int $counter = 0;

    public function model(array $row)
    {
        if (empty($row['mostrar_nombre'])) {
            return null;
        }

        $this->counter++;
        $email = 'cliente' . str_pad($this->counter, 2, '0', STR_PAD_LEFT) . '@gmail.com';

        $cliente = Cliente::firstOrCreate(
            ['nombre' => $row['mostrar_nombre']],
            [
                'email'    => $email,
                'usuario'  => $email,
                'password' => Hash::make('12345678'),
                'activo'   => true,
            ]
        );

        $cliente->update([
            'descuento'  => !empty($row['descuento_1']) ? (float) $row['descuento_1'] : 0,
            'descuento2' => !empty($row['descuento_2']) ? (float) $row['descuento_2'] : 0,
            'descuento3' => !empty($row['descuento_3']) ? (float) $row['descuento_3'] : 0,
        ]);

        return null;
    }
}
