<?php

namespace App\Imports;

use App\Models\CodigoOM;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductoAdminImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithCalculatedFormulas, WithMultipleSheets
{
    private int $creados = 0;
    private int $actualizados = 0;
    private int $eliminados = 0;
    private int $omitidos = 0;

    // Datos precargados para evitar varias queries por fila (codigo normalizado => Producto)
    private ?array $productos = null;
    private array $pivots = [];
    private array $codigosOm = [];

    private const PIVOTS = [
        'vehiculo_tipos_ids' => ['tabla' => 'vehiculo_tipo_producto', 'columna' => 'vehiculo_tipo_id', 'relacion' => 'vehiculoTipos'],
        'marcas_ids' => ['tabla' => 'marca_producto', 'columna' => 'marca_id', 'relacion' => 'marcas'],
        'modelos_ids' => ['tabla' => 'modelo_producto', 'columna' => 'modelo_id', 'relacion' => 'modelos'],
    ];

    public function __construct(private bool $soloActualizar = false)
    {
    }

    // Solo se procesa la primera hoja (Productos); las hojas "Ref - ..." son de consulta
    public function sheets(): array
    {
        return [0 => $this];
    }

    private function claveCodigo($codigo): string
    {
        return mb_strtolower(trim((string) $codigo));
    }

    private function precargar(): void
    {
        $this->productos = [];
        foreach (Producto::all() as $producto) {
            $this->productos[$this->claveCodigo($producto->codigo_ralux)] = $producto;
        }

        foreach (self::PIVOTS as $campo => $pivot) {
            $this->pivots[$campo] = [];
            foreach (DB::table($pivot['tabla'])->get(['producto_id', $pivot['columna']]) as $fila) {
                $this->pivots[$campo][$fila->producto_id][] = (int) $fila->{$pivot['columna']};
            }
        }

        foreach (CodigoOM::all(['producto_id', 'marca_id', 'codigo']) as $codigoOm) {
            $this->codigosOm[$codigoOm->producto_id][] = $codigoOm->codigo . '|' . ($codigoOm->marca_id ?? '');
        }
    }

    private function mismosValores(array $a, array $b): bool
    {
        $a = array_values(array_unique($a));
        $b = array_values(array_unique($b));
        sort($a);
        sort($b);

        return $a === $b;
    }

    private function limpiarNumero($valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        if (!is_string($valor) && is_numeric($valor)) {
            return (string) $valor;
        }

        $texto = trim((string) $valor);
        if ($texto === '') {
            return null;
        }

        $texto = preg_replace('/\s+/', '', $texto);
        $texto = preg_replace('/[^0-9,.-]/', '', $texto);
        $negativo = str_starts_with($texto, '-');
        $texto = str_replace('-', '', $texto);

        if ($texto === '') {
            return null;
        }

        $ultimaComa = strrpos($texto, ',');
        $ultimoPunto = strrpos($texto, '.');

        if ($ultimaComa !== false && $ultimoPunto !== false) {
            if ($ultimaComa > $ultimoPunto) {
                $texto = str_replace('.', '', $texto);
                $texto = str_replace(',', '.', $texto);
            } else {
                $texto = str_replace(',', '', $texto);
            }
        } elseif ($ultimaComa !== false) {
            $partes = explode(',', $texto);
            $decimales = strlen(end($partes));

            if (count($partes) > 2 || $decimales === 3) {
                $texto = str_replace(',', '', $texto);
            } else {
                $texto = str_replace(',', '.', $texto);
            }
        } elseif ($ultimoPunto !== false) {
            $partes = explode('.', $texto);
            $decimales = strlen(end($partes));

            if (count($partes) > 2) {
                $esMiles = collect(array_slice($partes, 1))
                    ->every(fn ($parte) => strlen($parte) === 3);

                if ($esMiles) {
                    $texto = str_replace('.', '', $texto);
                } else {
                    $decimal = array_pop($partes);
                    $texto = implode('', $partes) . '.' . $decimal;
                }
            } elseif ($decimales === 3) {
                $texto = str_replace('.', '', $texto);
            } else {
                $decimal = array_pop($partes);
                $texto = implode('', $partes) . '.' . $decimal;
            }
        }

        if ($negativo) {
            $texto = '-' . $texto;
        }

        return is_numeric($texto) ? $texto : null;
    }

    private function limpiarFecha($valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        $str = (string) $valor;
        if (str_starts_with($str, '-') || str_starts_with($str, '0000')) {
            return null;
        }

        return $str;
    }

    private function valorPresente(array $row, string $campo): bool
    {
        return isset($row[$campo]) && $row[$campo] !== null && $row[$campo] !== '';
    }

    public function getResumen(): array
    {
        return [
            'creados' => $this->creados,
            'actualizados' => $this->actualizados,
            'eliminados' => $this->eliminados,
            'omitidos' => $this->omitidos,
            'modo' => $this->soloActualizar ? 'solo_actualizar' : 'crear_actualizar',
        ];
    }

    public function getMensajeResumen(): string
    {
        $partes = [];

        if ($this->soloActualizar) {
            $partes[] = "Actualizados: {$this->actualizados}";

            if ($this->omitidos > 0) {
                $partes[] = "Omitidos por no existir: {$this->omitidos}";
            }
        } else {
            $partes[] = "Creados: {$this->creados}";
            $partes[] = "Actualizados: {$this->actualizados}";
        }

        if ($this->eliminados > 0) {
            $partes[] = "Eliminados: {$this->eliminados}";
        }

        return 'Importacion completada correctamente. ' . implode(' | ', $partes) . '.';
    }

    private function eliminarProducto(Producto $producto): void
    {
        foreach (['pdf', 'sonido', 'video', 'imagen_diagrama', 'diagrama_orientativo'] as $campo) {
            if ($producto->$campo && Storage::disk('public')->exists($producto->$campo)) {
                Storage::disk('public')->delete($producto->$campo);
            }
        }

        foreach ($producto->imagenes as $imagen) {
            if (Storage::disk('public')->exists($imagen->ruta)) {
                Storage::disk('public')->delete($imagen->ruta);
            }
        }

        $producto->delete();
        unset($this->productos[$this->claveCodigo($producto->codigo_ralux)]);
        $this->eliminados++;
    }

    public function model(array $row)
    {
        $codigo = $row['codigo_ralux'] ?? null;
        if (empty($codigo)) {
            return null;
        }

        if ($this->productos === null) {
            $this->precargar();
        }

        $productoExistente = $this->productos[$this->claveCodigo($codigo)] ?? null;

        if (isset($row['eliminar']) && (int) $row['eliminar'] === 1) {
            if ($productoExistente) {
                $this->eliminarProducto($productoExistente);
            }

            return null;
        }

        if ($this->soloActualizar && !$productoExistente) {
            $this->omitidos++;
            return null;
        }

        $data = [];

        $camposTexto = [
            'descripcion_es', 'descripcion_en', 'safe_url', 'pdf',
            'sonido', 'video', 'imagen_diagrama', 'diagrama_orientativo',
            'caract_1', 'valor_1', 'caract_2', 'valor_2',
            'caract_3', 'valor_3', 'caract_4', 'valor_4',
            'caract_5', 'valor_5', 'caract_6', 'valor_6',
            'caract_7', 'valor_7', 'caract_8', 'valor_8',
            'caract_9', 'valor_9', 'caract_10', 'valor_10',
        ];

        foreach ($camposTexto as $campo) {
            if ($this->valorPresente($row, $campo)) {
                $data[$campo] = $row[$campo];
            }
        }

        $camposNumericos = ['precio', 'descuento', 'voltaje', 'amperaje', 'terminales'];
        foreach ($camposNumericos as $campo) {
            if ($this->valorPresente($row, $campo)) {
                $data[$campo] = $this->limpiarNumero($row[$campo]);
            }
        }

        $camposBooleanos = ['soporte', 'estado', 'destacado', 'visible'];
        foreach ($camposBooleanos as $campo) {
            if ($this->valorPresente($row, $campo)) {
                $data[$campo] = (int) $row[$campo];
            }
        }

        if ($this->valorPresente($row, 'orden')) {
            $data['orden'] = (int) $row['orden'];
        }

        if ($this->valorPresente($row, 'producto_tipo_id')) {
            $data['producto_tipo_id'] = (int) $row['producto_tipo_id'];
        }

        if ($this->valorPresente($row, 'id')) {
            $data['id'] = (int) $row['id'];
        }

        $fechaDesde = $this->limpiarFecha($row['periodo_desde'] ?? null);
        $fechaHasta = $this->limpiarFecha($row['periodo_hasta'] ?? null);

        if ($fechaDesde !== null) {
            $data['periodo_desde'] = $fechaDesde;
        }

        if ($fechaHasta !== null) {
            $data['periodo_hasta'] = $fechaHasta;
        }

        if ($productoExistente) {
            $producto = $productoExistente->fill($data);
            if ($producto->isDirty()) {
                $producto->save();
            }
            $this->actualizados++;
        } else {
            $producto = Producto::create(['codigo_ralux' => $codigo] + $data);
            $this->productos[$this->claveCodigo($codigo)] = $producto;
            $this->creados++;
        }

        foreach (self::PIVOTS as $campo => $pivot) {
            if (!$this->valorPresente($row, $campo)) {
                continue;
            }

            $ids = array_values(array_filter(array_map('intval', explode(',', (string) $row[$campo]))));
            if ($this->mismosValores($ids, $this->pivots[$campo][$producto->id] ?? [])) {
                continue;
            }

            $producto->{$pivot['relacion']}()->sync($ids);
            $this->pivots[$campo][$producto->id] = $ids;
        }

        if ($this->valorPresente($row, 'codigos_om')) {
            $nuevos = [];
            $pares = explode(',', (string) $row['codigos_om']);

            foreach ($pares as $par) {
                $par = trim($par);
                if ($par === '') {
                    continue;
                }

                $partes = explode('|', $par, 2);
                $codigoOm = trim($partes[0]);
                $marcaId = isset($partes[1]) && $partes[1] !== '' ? (int) $partes[1] : null;
                $clave = $codigoOm . '|' . ($marcaId ?? '');

                if ($codigoOm === '' || isset($nuevos[$clave])) {
                    continue;
                }

                $nuevos[$clave] = ['codigo' => $codigoOm, 'marca_id' => $marcaId];
            }

            if (!$this->mismosValores(array_keys($nuevos), $this->codigosOm[$producto->id] ?? [])) {
                $producto->codigosOM()->delete();

                foreach ($nuevos as $nuevo) {
                    CodigoOM::create([
                        'producto_id' => $producto->id,
                        'marca_id' => $nuevo['marca_id'],
                        'codigo' => $nuevo['codigo'],
                    ]);
                }

                $this->codigosOm[$producto->id] = array_keys($nuevos);
            }
        }

        return null;
    }
}
