<?php

namespace App\Console\Commands;

use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportarImagenesProductos extends Command
{
    protected $signature = 'productos:importar-imagenes
                            {--fotos-path=public/producto/fotos : Ruta relativa al proyecto de la carpeta fotos/}
                            {--dry-run : Solo muestra lo que haría sin hacer cambios ni copiar archivos}';

    protected $description = 'Importa imágenes desde fotos/productos/{id}/ y fotos/diagramas/{id}/ a storage y base de datos';

    private array $extensiones = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function handle(): int
    {
        $fotosPath = base_path($this->option('fotos-path'));
        $dryRun = $this->option('dry-run');

        if (!is_dir($fotosPath)) {
            $this->error("No se encontró la carpeta: {$fotosPath}");
            $this->line("Asegurate de que la carpeta exista en: {$fotosPath}");
            return 1;
        }

        if ($dryRun) {
            $this->warn('MODO DRY-RUN: No se realizarán cambios. Solo se muestra lo que se haría.');
            $this->newLine();
        }

        $stats = [
            'imagenes_copiadas'      => 0,
            'imagenes_registradas'   => 0,
            'diagramas_copiados'     => 0,
            'diagramas_registrados'  => 0,
            'sin_producto_en_db'     => [],
            'sin_archivo_en_carpeta' => [],
        ];

        // 1. Limpiar registros existentes
        if (!$dryRun) {
            $this->info('Limpiando registros existentes...');
            ProductoImagen::truncate();
            Producto::query()->update(['imagen_diagrama' => null]);
        }

        // 2. Procesar imágenes de galería
        $productosPath = $fotosPath . '/productos';
        if (is_dir($productosPath)) {
            $this->info('Procesando imágenes de productos...');
            $this->procesarGaleria($productosPath, $stats, $dryRun);
        } else {
            $this->warn("Carpeta no encontrada: {$productosPath}");
        }

        // 3. Procesar diagramas
        $diagramasPath = $fotosPath . '/diagramas';
        if (is_dir($diagramasPath)) {
            $this->info('Procesando diagramas...');
            $this->procesarDiagramas($diagramasPath, $stats, $dryRun);
        } else {
            $this->warn("Carpeta no encontrada: {$diagramasPath}");
        }

        // 4. Resumen final
        $this->newLine();
        $this->info('========== RESUMEN ==========');
        $this->line("Imágenes de galería copiadas:    {$stats['imagenes_copiadas']}");
        $this->line("Imágenes de galería registradas: {$stats['imagenes_registradas']}");
        $this->line("Diagramas copiados:              {$stats['diagramas_copiados']}");
        $this->line("Diagramas registrados:           {$stats['diagramas_registrados']}");

        $sinProducto = array_unique($stats['sin_producto_en_db']);
        if (count($sinProducto) > 0) {
            $this->newLine();
            $this->warn('Carpetas con ID que no existe en la BD (' . count($sinProducto) . '):');
            $this->line(implode(', ', array_slice($sinProducto, 0, 30)));
            if (count($sinProducto) > 30) {
                $this->line('... y ' . (count($sinProducto) - 30) . ' más');
            }
        }

        if (count($stats['sin_archivo_en_carpeta']) > 0) {
            $this->newLine();
            $this->warn('Carpetas sin imagen dentro (' . count($stats['sin_archivo_en_carpeta']) . '):');
            $this->line(implode(', ', array_slice($stats['sin_archivo_en_carpeta'], 0, 30)));
        }

        $this->newLine();
        if ($dryRun) {
            $this->warn('DRY-RUN completado. Ejecutá sin --dry-run para aplicar los cambios.');
        } else {
            $this->info('Importación completada.');
        }

        return 0;
    }

    private function procesarGaleria(string $path, array &$stats, bool $dryRun): void
    {
        foreach ($this->listarCarpetas($path) as $carpetaId) {
            $productoId = (int) $carpetaId;
            if ($productoId <= 0) continue;

            $producto = Producto::find($productoId);
            if (!$producto) {
                $stats['sin_producto_en_db'][] = $productoId;
                continue;
            }

            $carpetaPath = $path . '/' . $carpetaId;
            $archivo = $this->buscarImagen($carpetaPath, $carpetaId);

            if (!$archivo) {
                $stats['sin_archivo_en_carpeta'][] = $productoId;
                continue;
            }

            $ext     = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
            $destino = "productos/imagenes/{$productoId}.{$ext}";

            if (!$dryRun) {
                Storage::disk('public')->put($destino, file_get_contents($archivo));
                $stats['imagenes_copiadas']++;

                ProductoImagen::create([
                    'producto_id' => $productoId,
                    'ruta'        => $destino,
                    'alt'         => $producto->descripcion_es ?? $producto->codigo_ralux,
                    'orden'       => 0,
                    'principal'   => true,
                    'tipo'        => 'galeria',
                ]);
                $stats['imagenes_registradas']++;
            } else {
                $this->line("  [DRY] producto {$productoId}: " . basename($archivo) . " → {$destino}");
                $stats['imagenes_copiadas']++;
                $stats['imagenes_registradas']++;
            }
        }
    }

    private function procesarDiagramas(string $path, array &$stats, bool $dryRun): void
    {
        foreach ($this->listarCarpetas($path) as $carpetaId) {
            $productoId = (int) $carpetaId;
            if ($productoId <= 0) continue;

            $producto = Producto::find($productoId);
            if (!$producto) {
                $stats['sin_producto_en_db'][] = $productoId;
                continue;
            }

            $carpetaPath = $path . '/' . $carpetaId;
            $archivo = $this->buscarImagen($carpetaPath, $carpetaId);

            if (!$archivo) {
                $stats['sin_archivo_en_carpeta'][] = 'diagrama-' . $productoId;
                continue;
            }

            $ext     = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
            $destino = "productos/diagramas/{$productoId}.{$ext}";

            if (!$dryRun) {
                Storage::disk('public')->put($destino, file_get_contents($archivo));
                $stats['diagramas_copiados']++;

                $producto->update(['imagen_diagrama' => $destino]);
                $stats['diagramas_registrados']++;
            } else {
                $this->line("  [DRY] diagrama {$productoId}: " . basename($archivo) . " → {$destino}");
                $stats['diagramas_copiados']++;
                $stats['diagramas_registrados']++;
            }
        }
    }

    /**
     * Busca una imagen dentro de una carpeta.
     * Primero intenta {id}.{ext}, luego cualquier imagen válida (ignorando subcarpetas).
     */
    private function buscarImagen(string $carpetaPath, string $carpetaId): ?string
    {
        // 1. Buscar con nombre igual al id
        foreach ($this->extensiones as $ext) {
            $candidato = $carpetaPath . '/' . $carpetaId . '.' . $ext;
            if (file_exists($candidato)) {
                return $candidato;
            }
        }

        // 2. Buscar cualquier imagen en la carpeta (ignorar subcarpetas como thumbnail/)
        foreach (scandir($carpetaPath) as $file) {
            if ($file === '.' || $file === '..') continue;
            if (is_dir($carpetaPath . '/' . $file)) continue;

            $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($fileExt, $this->extensiones)) {
                return $carpetaPath . '/' . $file;
            }
        }

        return null;
    }

    /**
     * Lista los nombres de subcarpetas directas de un directorio.
     */
    private function listarCarpetas(string $path): array
    {
        $carpetas = [];
        foreach (scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            if (is_dir($path . '/' . $entry)) {
                $carpetas[] = $entry;
            }
        }
        return $carpetas;
    }
}
