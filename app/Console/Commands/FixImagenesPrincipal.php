<?php

namespace App\Console\Commands;

use App\Models\Producto;
use Illuminate\Console\Command;

class FixImagenesPrincipal extends Command
{
    protected $signature = 'productos:fix-imagen-principal';
    protected $description = 'Marca la primera imagen (por orden) como principal en productos que no tienen ninguna imagen principal';

    public function handle()
    {
        $productos = Producto::with('imagenes')->get();
        $fixed = 0;

        foreach ($productos as $producto) {
            if ($producto->imagenes->isEmpty()) {
                continue;
            }

            $tienePrincipal = $producto->imagenes->where('principal', true)->isNotEmpty();

            if (!$tienePrincipal) {
                $primera = $producto->imagenes->sortBy('orden')->first();
                $primera->update(['principal' => true]);
                $fixed++;
                $this->line("Producto ID {$producto->id} ({$producto->codigo_ralux}): imagen ID {$primera->id} marcada como principal.");
            }
        }

        $this->info("Listo. {$fixed} producto(s) corregido(s).");
    }
}
