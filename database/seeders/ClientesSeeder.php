<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ClienteImport;

class ClientesSeeder extends Seeder
{
    public function run(): void
    {
        Excel::import(new ClienteImport, database_path('seeders/files/clientes.xlsx'));
    }
}
