<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Actualiza SOLO la columna `precio` de la tabla productos a partir de un Excel
 * con bloques de columnas ART / PRECIO (izquierda el codigo, derecha el precio).
 *
 * Uso:
 *   php artisan db:seed --class=ActualizarPreciosSeeder                      (aplica)
 *   PRECIOS_DRY_RUN=1 php artisan db:seed --class=ActualizarPreciosSeeder    (simula, no escribe)
 *
 * Variables de entorno opcionales:
 *   PRECIOS_ARCHIVO      ruta del xlsx (default: database/seeders/files/LISTA 268 17 07 2026.xlsx)
 *   PRECIOS_HOJA         nombre de la hoja (default: LISTA 268)
 *   PRECIOS_DRY_RUN      1 = no escribe nada, solo informa
 *   PRECIOS_COL_ART      letra de la columna de codigos, si el Excel no trae encabezado ART
 *   PRECIOS_COL_PRECIO   letra de la columna de precios (ej: PRECIOS_COL_ART=B PRECIOS_COL_PRECIO=C)
 *
 * El match de codigos es tolerante a inconsistencias: ignora mayusculas/minusculas,
 * guiones, espacios y anotaciones entre parentesis del codigo cargado en la base
 * (ej: el Excel trae "FB402" y la base tiene "fb402 (ex f-8538)").
 */
class ActualizarPreciosSeeder extends Seeder
{
    private const ARCHIVO_DEFAULT = 'LISTA 268 17 07 2026.xlsx';
    private const HOJA_DEFAULT    = 'LISTA 268';

    public function run(): void
    {
        $archivo = env('PRECIOS_ARCHIVO') ?: database_path('seeders/files/' . self::ARCHIVO_DEFAULT);
        $hoja    = env('PRECIOS_HOJA') ?: self::HOJA_DEFAULT;
        $dryRun  = (bool) env('PRECIOS_DRY_RUN', false);

        if (!is_file($archivo)) {
            $this->command?->error("No se encontro el archivo: {$archivo}");
            return;
        }

        $this->command?->info("Archivo: {$archivo}");
        $this->command?->info("Hoja:    {$hoja}");
        if ($dryRun) {
            $this->command?->warn('MODO SIMULACION (PRECIOS_DRY_RUN=1): no se va a escribir nada en la base.');
        }

        $listaPrecios = $this->leerExcel($archivo, $hoja);

        if (empty($listaPrecios)) {
            $this->command?->error('No se pudo leer ningun par ART/PRECIO de la hoja.');
            return;
        }

        $this->command?->info('Codigos leidos del Excel: ' . count($listaPrecios));

        $indices = $this->indexarProductos();

        $actualizados = [];   // codigo excel => [codigo base, precio viejo, precio nuevo]
        $sinCambio    = 0;
        $sinMatch     = [];   // codigo excel => precio
        $ambiguos     = [];   // codigo excel => [codigos base candidatos]
        $conflictos   = [];   // producto id => [codigos excel]
        $asignados    = [];   // producto id => codigo excel (para detectar conflictos)

        DB::beginTransaction();

        try {
            foreach ($listaPrecios as $codigoExcel => $precio) {
                $candidatos = $this->buscarProductos($codigoExcel, $indices);

                if ($candidatos === null) {
                    $sinMatch[$codigoExcel] = $precio;
                    continue;
                }

                if ($candidatos === false) {
                    $ambiguos[$codigoExcel] = $indices['ambiguos'][$this->normalizar($codigoExcel)] ?? [];
                    continue;
                }

                foreach ($candidatos as $producto) {
                    if (isset($asignados[$producto->id]) && $asignados[$producto->id] !== $codigoExcel) {
                        $conflictos[$producto->codigo_ralux][] = $asignados[$producto->id];
                        $conflictos[$producto->codigo_ralux][] = $codigoExcel;
                        continue;
                    }

                    $asignados[$producto->id] = $codigoExcel;
                    $precioViejo = $producto->precio === null ? null : (float) $producto->precio;

                    if ($precioViejo !== null && abs($precioViejo - $precio) < 0.005) {
                        $sinCambio++;
                        continue;
                    }

                    if (!$dryRun) {
                        DB::table('productos')
                            ->where('id', $producto->id)
                            ->update(['precio' => $precio, 'updated_at' => now()]);
                    }

                    $actualizados[] = [$codigoExcel, $producto->codigo_ralux, $precioViejo, $precio];
                }
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command?->error('Se revirtio todo por un error: ' . $e->getMessage());
            throw $e;
        }

        $this->reportar([
            'actualizados' => $actualizados,
            'sinCambio'    => $sinCambio,
            'sinMatch'     => $sinMatch,
            'ambiguos'     => $ambiguos,
            'conflictos'   => $conflictos,
            'asignados'    => $asignados,
        ], $indices, $dryRun);
    }

    /**
     * Lee la hoja y devuelve [codigo => precio]. Detecta la fila de encabezados
     * ART/PRECIO y arma los pares de columnas a partir de ahi, asi soporta las
     * 5 tandas de columnas del listado sin hardcodearlas.
     */
    private function leerExcel(string $archivo, string $hoja): array
    {
        $reader = IOFactory::createReaderForFile($archivo);
        $reader->setReadDataOnly(true);
        $planilla = $reader->load($archivo);

        $sheet = $planilla->getSheetByName($hoja) ?? $planilla->getSheet(0);
        $filas = $sheet->toArray(null, true, false, false);

        $pares = $this->detectarColumnas($filas);
        $precios = [];

        foreach ($filas as $fila) {
            foreach ($pares as $colArt => $colPrecio) {
                $codigo = $fila[$colArt] ?? null;
                $precio = $fila[$colPrecio] ?? null;

                if ($codigo === null || trim((string) $codigo) === '') {
                    continue;
                }

                $precio = $this->limpiarPrecio($precio);

                // Sin precio numerico no es una fila de producto: son los titulos,
                // encabezados y notas al pie del listado.
                if ($precio === null) {
                    continue;
                }

                $precios[trim((string) $codigo)] = $precio;
            }
        }

        return $precios;
    }

    /**
     * Devuelve [columnaART => columnaPRECIO] leyendo la fila de encabezados.
     * Se puede forzar con PRECIOS_COL_ART / PRECIOS_COL_PRECIO (letras de Excel,
     * ej: PRECIOS_COL_ART=B PRECIOS_COL_PRECIO=C).
     * Si no encuentra encabezados, asume el patron de pares (0,1), (2,3), (4,5)...
     */
    private function detectarColumnas(array $filas): array
    {
        $colArt = env('PRECIOS_COL_ART');
        $colPrecio = env('PRECIOS_COL_PRECIO');

        if ($colArt && $colPrecio) {
            return [$this->letraAIndice($colArt) => $this->letraAIndice($colPrecio)];
        }

        foreach ($filas as $fila) {
            $pares = [];

            foreach ($fila as $col => $celda) {
                if (strtoupper(trim((string) $celda)) !== 'ART') {
                    continue;
                }

                $siguiente = strtoupper(trim((string) ($fila[$col + 1] ?? '')));

                if ($siguiente === 'PRECIO') {
                    $pares[$col] = $col + 1;
                }
            }

            if (!empty($pares)) {
                return $pares;
            }
        }

        $pares = [];
        $ancho = count($filas[0] ?? []);

        for ($col = 0; $col + 1 < $ancho; $col += 2) {
            $pares[$col] = $col + 1;
        }

        return $pares;
    }

    /** "A" => 0, "B" => 1, "C" => 2... Acepta tambien el numero directo. */
    private function letraAIndice(string $letra): int
    {
        $letra = strtoupper(trim($letra));

        if (is_numeric($letra)) {
            return (int) $letra;
        }

        $indice = 0;

        foreach (str_split($letra) as $caracter) {
            $indice = $indice * 26 + (ord($caracter) - 64);
        }

        return $indice - 1;
    }

    private function limpiarPrecio($valor): ?float
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        if (is_numeric($valor)) {
            return round((float) $valor, 2);
        }

        $texto = preg_replace('/[^0-9,.-]/', '', (string) $valor);

        if ($texto === '' || $texto === null) {
            return null;
        }

        // Formato local 1.234,56 -> 1234.56
        if (str_contains($texto, ',')) {
            $texto = str_replace('.', '', $texto);
            $texto = str_replace(',', '.', $texto);
        }

        return is_numeric($texto) ? round((float) $texto, 2) : null;
    }

    /**
     * Arma los indices de busqueda de productos en 3 niveles de tolerancia.
     */
    private function indexarProductos(): array
    {
        $exacto = [];
        $normalizado = [];
        $baseCodigo = [];
        $todos = [];

        Producto::select('id', 'codigo_ralux', 'precio', 'estado', 'visible')
            ->whereNotNull('codigo_ralux')
            ->orderBy('id')
            ->chunk(500, function ($productos) use (&$exacto, &$normalizado, &$baseCodigo, &$todos) {
                foreach ($productos as $producto) {
                    $todos[$producto->id] = $producto;
                    $exacto[trim($producto->codigo_ralux)][] = $producto;

                    $norm = $this->normalizar($producto->codigo_ralux);
                    if ($norm === '') {
                        continue;
                    }
                    $normalizado[$norm][] = $producto;

                    $base = $this->base($producto->codigo_ralux);
                    if ($base !== '' && $base !== $norm) {
                        $baseCodigo[$base][] = $producto;
                    }
                }
            });

        // Un mismo codigo base puede apuntar a varios productos. Si solo difieren en
        // lo que va entre parentesis son filas duplicadas del mismo articulo (ej:
        // "fna321401 (ex f8981)" y "fna321401 (ex f8981000)") y se actualizan todas.
        // Si difieren en el codigo mismo (ej: "160" vs "160/ 60a") queda ambiguo y no
        // se toca ninguna.
        $ambiguos = [];

        foreach ($baseCodigo as $clave => $productos) {
            $sinAnotacion = array_unique(array_map(fn ($p) => $this->sinAnotaciones($p->codigo_ralux), $productos));

            if (count($sinAnotacion) > 1) {
                $ambiguos[$clave] = array_map(fn ($p) => $p->codigo_ralux, $productos);
            }
        }

        return [
            'exacto'      => $exacto,
            'normalizado' => $normalizado,
            'base'        => $baseCodigo,
            'ambiguos'    => $ambiguos,
            'todos'       => $todos,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection|array|null|false
     *         array de productos si hay match, null si no hay, false si es ambiguo
     */
    private function buscarProductos(string $codigoExcel, array $indices)
    {
        $codigo = trim($codigoExcel);

        if (isset($indices['exacto'][$codigo])) {
            return $indices['exacto'][$codigo];
        }

        $norm = $this->normalizar($codigo);

        if ($norm === '') {
            return null;
        }

        if (isset($indices['normalizado'][$norm])) {
            return $indices['normalizado'][$norm];
        }

        if (isset($indices['ambiguos'][$norm])) {
            return false;
        }

        if (isset($indices['base'][$norm])) {
            return $indices['base'][$norm];
        }

        return null;
    }

    /** Mayusculas y solo alfanumerico: "b-8408" y "B 8408" caen en "B8408". */
    private function normalizar($codigo): string
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper(trim((string) $codigo)));
    }

    /** Saca lo que va entre parentesis: "fb402 (ex f-8538)" -> "FB402". */
    private function sinAnotaciones($codigo): string
    {
        return $this->normalizar(preg_replace('/\([^)]*+\)/', ' ', (string) $codigo));
    }

    /** Saca anotaciones: "fb402 (ex f-8538)" -> "FB402", "B-9100 REEMPLAZABLE..." -> "B9100". */
    private function base($codigo): string
    {
        $limpio = preg_replace('/\([^)]*+\)/', ' ', (string) $codigo);
        $primero = preg_split('/\s+/', trim($limpio))[0] ?? '';

        return $this->normalizar($primero);
    }

    /**
     * El reverso del reporte: productos de la base que NO recibieron precio de la lista.
     * Solo detalla los que estan activos, visibles y ya tenian un precio cargado, que son
     * los que quedan mostrando un precio viejo en la web. El resto se cuenta nomas.
     */
    private function reportarProductosSinPrecio(array $todos, array $asignados): void
    {
        $sinPrecio = array_filter($todos, fn ($p) => !isset($asignados[$p->id]));

        if (empty($sinPrecio)) {
            return;
        }

        $desactualizados = array_filter(
            $sinPrecio,
            fn ($p) => $p->precio !== null && (float) $p->precio > 0 && $p->estado && $p->visible
        );

        $this->command?->newLine();
        $this->command?->info('Productos de la base sin precio en la lista: ' . count($sinPrecio));
        $this->command?->info('  de esos, activos+visibles con precio viejo: ' . count($desactualizados));

        if (empty($desactualizados)) {
            return;
        }

        $this->command?->newLine();
        $this->command?->warn('--- Quedan publicados con el precio anterior (revisar a mano) ---');

        usort($desactualizados, fn ($a, $b) => strcasecmp($a->codigo_ralux, $b->codigo_ralux));

        foreach ($desactualizados as $producto) {
            $this->command?->line(sprintf(
                '  %-32s $%s',
                $producto->codigo_ralux,
                number_format((float) $producto->precio, 2, ',', '.')
            ));
        }
    }

    /**
     * @param array{actualizados:array, sinCambio:int, sinMatch:array, ambiguos:array,
     *              conflictos:array, asignados:array} $r
     */
    private function reportar(array $r, array $indices, bool $dryRun): void
    {
        [$actualizados, $sinCambio, $sinMatch, $ambiguos, $conflictos, $asignados] =
            [$r['actualizados'], $r['sinCambio'], $r['sinMatch'], $r['ambiguos'], $r['conflictos'], $r['asignados']];

        $this->command?->newLine();
        $this->command?->info('================ RESUMEN ================');
        $this->command?->info(($dryRun ? 'Se actualizarian: ' : 'Actualizados:     ') . count($actualizados));
        $this->command?->info('Ya estaban al dia: ' . $sinCambio);
        $this->command?->info('Sin match en la base: ' . count($sinMatch));
        $this->command?->info('Ambiguos (no tocados): ' . count($ambiguos));
        $this->command?->info('Conflictos (2 codigos al mismo producto): ' . count($conflictos));

        $this->reportarProductosSinPrecio($indices['todos'], $asignados);

        if (!empty($sinMatch)) {
            $this->command?->newLine();
            $this->command?->warn('--- Codigos del Excel que NO existen en productos.codigo_ralux ---');
            foreach ($sinMatch as $codigo => $precio) {
                $this->command?->line(sprintf('  %-20s $%s', $codigo, number_format($precio, 2, ',', '.')));
            }
        }

        if (!empty($ambiguos)) {
            $this->command?->newLine();
            $this->command?->warn('--- Codigos ambiguos (revisar a mano) ---');
            foreach ($ambiguos as $codigo => $candidatos) {
                $this->command?->line("  {$codigo} => " . implode(' | ', $candidatos));
            }
        }

        if (!empty($conflictos)) {
            $this->command?->newLine();
            $this->command?->warn('--- Conflictos: un producto recibio precio de mas de un codigo ---');
            foreach ($conflictos as $codigoBase => $codigosExcel) {
                $this->command?->line("  {$codigoBase} <= " . implode(' | ', array_unique($codigosExcel)));
            }
        }

        $duplicados = array_filter($indices['normalizado'], fn ($p) => count($p) > 1);

        if (!empty($duplicados)) {
            $this->command?->newLine();
            $this->command?->warn('--- Productos duplicados en la base (se actualizaron todos) ---');
            foreach ($duplicados as $productos) {
                $this->command?->line('  ' . implode(' | ', array_map(fn ($p) => $p->codigo_ralux, $productos)));
            }
        }

        $this->command?->newLine();
        $this->command?->info('=========================================');
    }
}
