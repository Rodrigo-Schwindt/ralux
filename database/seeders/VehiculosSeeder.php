<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VehiculoTipoImport;
use App\Imports\MarcaImport;
use App\Imports\ModeloImport;
use App\Imports\ProductoTipoImport;
use App\Imports\ProductoImport;
use App\Imports\ProductoImagenImport;
use App\Imports\ProductoDiagramaImport;
use App\Imports\VehiculoTipoProductoImport;
use App\Imports\MarcaProductoImport;
use App\Imports\ModeloProductoImport;

class VehiculosSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('codigo_om')->truncate();
        DB::table('modelo_producto')->truncate();
        DB::table('marca_producto')->truncate();
        DB::table('vehiculo_tipo_producto')->truncate();
        DB::table('producto_imagenes')->truncate();
        DB::table('modelos')->truncate();
        DB::table('marcas')->truncate();
        DB::table('vehiculo_tipo')->truncate();
        DB::table('productos')->truncate();
        DB::table('productos_tipo')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Excel::import(new ProductoTipoImport, database_path('seeders/files/productos_tipo.xlsx'));
        Excel::import(new ProductoImport, database_path('seeders/files/productos.xlsx'));
        Excel::import(new VehiculoTipoImport, database_path('seeders/files/tipos.xlsx'));
        Excel::import(new MarcaImport, database_path('seeders/files/vehiculo_marca.xlsx'));
        Excel::import(new ModeloImport, database_path('seeders/files/vehiculo_modelos.xlsx'));
        Excel::import(new ProductoImagenImport, database_path('seeders/files/producto_imagenes.xlsx'));
        Excel::import(new ProductoDiagramaImport, database_path('seeders/files/producto_diagramas.xlsx'));
        Excel::import(new VehiculoTipoProductoImport, database_path('seeders/files/vehiculo_tipo_producto.xlsx'));
        Excel::import(new MarcaProductoImport, database_path('seeders/files/marca_producto.xlsx'));
        Excel::import(new ModeloProductoImport, database_path('seeders/files/modelo_producto.xlsx'));
    }
}