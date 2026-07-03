<?php

namespace App\Exports;

use App\Models\Marca;
use App\Models\Modelo;
use App\Models\ProductoTipo;
use App\Models\VehiculoTipo;
use App\Exports\Sheets\ProductosSheet;
use App\Exports\Sheets\ReferenciaSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductoExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductosSheet(withData: true),
            $this->vehiculoTiposSheet(),
            $this->marcasSheet(),
            $this->modelosSheet(),
            $this->productoTiposSheet(),
        ];
    }

    private function vehiculoTiposSheet(): ReferenciaSheet
    {
        $data = VehiculoTipo::orderBy('orden')->get()
            ->map(fn($v) => [$v->id, $v->descripcion_es]);

        return new ReferenciaSheet('Ref - Vehiculo Tipos', ['id', 'descripcion_es'], $data);
    }

    private function marcasSheet(): ReferenciaSheet
    {
        $data = Marca::with('vehiculoTipo')->orderBy('descripcion_es')->get()
            ->map(fn($m) => [
                $m->id,
                $m->descripcion_es,
                $m->vehiculo_tipo_id,
                $m->vehiculoTipo?->descripcion_es,
            ]);

        return new ReferenciaSheet('Ref - Marcas', ['id', 'descripcion_es', 'vehiculo_tipo_id', 'vehiculo_tipo'], $data);
    }

    private function modelosSheet(): ReferenciaSheet
    {
        $data = Modelo::with('marca')->orderBy('descripcion_es')->get()
            ->map(fn($m) => [
                $m->id,
                $m->descripcion_es,
                $m->marca_id,
                $m->marca?->descripcion_es,
            ]);

        return new ReferenciaSheet('Ref - Modelos', ['id', 'descripcion_es', 'marca_id', 'marca'], $data);
    }

    private function productoTiposSheet(): ReferenciaSheet
    {
        $data = ProductoTipo::orderBy('orden')->get()
            ->map(fn($t) => [$t->id, $t->descripcion_es]);

        return new ReferenciaSheet('Ref - Producto Tipos', ['id', 'descripcion_es'], $data);
    }
}
