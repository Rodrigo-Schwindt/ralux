<?php

namespace App\Imports;

use App\Models\CodigoOM;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductoAdminImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithCalculatedFormulas
{
    private int $creados = 0;
    private int $actualizados = 0;
    private int $eliminados = 0;
    private int $omitidos = 0;

    public function __construct(private bool $soloActualizar = false)
    {
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
        $this->eliminados++;
    }

    public function model(array $row)
    {
        $codigo = $row['codigo_ralux'] ?? null;
        if (empty($codigo)) {
            return null;
        }

        $productoExistente = Producto::with('imagenes')
            ->where('codigo_ralux', $codigo)
            ->first();

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

        $producto = Producto::updateOrCreate(
            ['codigo_ralux' => $codigo],
            $data
        );

        if ($productoExistente) {
            $this->actualizados++;
        } else {
            $this->creados++;
        }

        if ($this->valorPresente($row, 'vehiculo_tipos_ids')) {
            $ids = array_filter(array_map('intval', explode(',', (string) $row['vehiculo_tipos_ids'])));
            $producto->vehiculoTipos()->sync($ids);
        }

        if ($this->valorPresente($row, 'marcas_ids')) {
            $ids = array_filter(array_map('intval', explode(',', (string) $row['marcas_ids'])));
            $producto->marcas()->sync($ids);
        }

        if ($this->valorPresente($row, 'modelos_ids')) {
            $ids = array_filter(array_map('intval', explode(',', (string) $row['modelos_ids'])));
            $producto->modelos()->sync($ids);
        }

        if ($this->valorPresente($row, 'codigos_om')) {
            $producto->codigosOM()->delete();

            $vistos = [];
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

                if ($codigoOm === '' || isset($vistos[$clave])) {
                    continue;
                }

                $vistos[$clave] = true;

                CodigoOM::create([
                    'producto_id' => $producto->id,
                    'marca_id' => $marcaId,
                    'codigo' => $codigoOm,
                ]);
            }
        }

        return null;
    }
}
