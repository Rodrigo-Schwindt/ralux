<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CodigoOMImport;

class CodigoOMSeeder extends Seeder
{
    public function run(): void
    {
        Excel::import(new CodigoOMImport, database_path('seeders/files/codigo_om.xlsx'));
    }
}
