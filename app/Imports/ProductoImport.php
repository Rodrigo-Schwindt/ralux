<?php

namespace App\Imports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductoImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    private function limpiarNumero($valor)
    {
        if ($valor === null || $valor === '') return null;
        if (!is_string($valor) && is_numeric($valor)) return (string) $valor;

        $texto = trim((string) $valor);
        if ($texto === '') return null;

        $texto = preg_replace('/\s+/', '', $texto);
        $texto = preg_replace('/[^0-9,.-]/', '', $texto);
        $negativo = str_starts_with($texto, '-');
        $texto = str_replace('-', '', $texto);

        if ($texto === '') return null;

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
    private function limpiarFecha($valor)
{
    if (empty($valor)) return null;
    if (str_starts_with($valor, '-') || str_starts_with($valor, '0000')) return null;
    return $valor;
}

    public function model(array $row)
{
    if (empty($row['id'])) {
        return null;
    }

    Producto::updateOrCreate(
        ['codigo_ralux' => $row['codigo_ralux']],
        [
            'id'               => $row['id'],
            'producto_tipo_id' => $row['id_producto_tipo'],
            'descripcion_es'   => $row['descripcion_es'],
            'descripcion_en'   => $row['descripcion_en'],
            'voltaje'          => $this->limpiarNumero($row['voltaje']),
            'amperaje'         => $this->limpiarNumero($row['amperaje']),
            'terminales'       => $this->limpiarNumero($row['terminales']),
            'soporte'          => $row['soporte'] ?? 0,
'caract_1'  => $row['caract 1'] ?? null,
'valor_1'   => $row['valor 1'] ?? null,
'caract_2'  => $row['caract 2'] ?? null,
'valor_2'   => $row['valor 2'] ?? null,
'caract_3'  => $row['caract 3'] ?? null,
'valor_3'   => $row['valor 3'] ?? null,
'caract_4'  => $row['caract 4'] ?? null,
'valor_4'   => $row['valor 4'] ?? null,
'caract_5'  => $row['caract 5'] ?? null,
'valor_5'   => $row['valor 5'] ?? null,
'caract_6'  => $row['caract 6'] ?? null,
'valor_6'   => $row['valor 6'] ?? null,
'caract_7'  => $row['caract 7'] ?? null,
'valor_7'   => $row['valor 7'] ?? null,
'caract_8'  => $row['caract 8'] ?? null,
'valor_8'   => $row['valor 8'] ?? null,
'caract_9'  => $row['caract 9'] ?? null,
'valor_9'   => $row['valor 9'] ?? null,
'caract_10' => $row['caract 10'] ?? null,
'valor_10'  => $row['valor 10'] ?? null,
'pdf'       => $row['pdf'] ?? null,
            'precio'           => $this->limpiarNumero($row['precio']),
            'periodo_desde'    => $this->limpiarFecha($row['periodo_desde']),
            'periodo_hasta'    => $this->limpiarFecha($row['periodo_hasta']),
            'safe_url'         => $row['safe_url'] ?? null,
            'sonido'           => $row['sonido'] ?? null,
            'video'            => $row['video'] ?? null,
            'estado'           => $row['estado'] ?? 1,
        ]
    );

    return null;
}
}
