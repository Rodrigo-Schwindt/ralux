<?php

namespace App\Support;

use App\Models\CarritoConfig;
use App\Models\Producto;
use Illuminate\Support\Collection;

class CarritoIva
{
    public static function ivaGeneral(?CarritoConfig $config): float
    {
        return (float) ($config?->iva ?? 21);
    }

    public static function ivaEspecial(?CarritoConfig $config): float
    {
        return (float) ($config?->iva_especial ?? 10.5);
    }

    public static function prefijosEspeciales(?CarritoConfig $config): array
    {
        $prefijos = $config?->iva_prefijos_especiales ?? ['F', 'M'];

        if (is_string($prefijos)) {
            $prefijos = preg_split('/[\s,;|]+/', $prefijos, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (! is_array($prefijos)) {
            $prefijos = [];
        }

        $prefijos = array_map(
            fn ($prefijo) => mb_strtoupper(trim((string) $prefijo)),
            $prefijos
        );

        return array_values(array_filter(array_unique($prefijos)));
    }

    public static function productoUsaIvaEspecial(?Producto $producto, ?CarritoConfig $config): bool
    {
        return self::codigoUsaIvaEspecial($producto?->codigo_ralux, $config);
    }

    public static function codigoUsaIvaEspecial(?string $codigo, ?CarritoConfig $config): bool
    {
        $codigo = mb_strtoupper(trim((string) $codigo));

        if ($codigo === '') {
            return false;
        }

        foreach (self::prefijosEspeciales($config) as $prefijo) {
            if ($prefijo !== '' && str_starts_with($codigo, $prefijo)) {
                return true;
            }
        }

        return false;
    }

    public static function porcentajeEsEspecial(float $porcentaje, ?CarritoConfig $config): bool
    {
        return abs($porcentaje - self::ivaEspecial($config)) < 0.01;
    }

    public static function ivaProducto(?Producto $producto, ?CarritoConfig $config): float
    {
        return self::productoUsaIvaEspecial($producto, $config)
            ? self::ivaEspecial($config)
            : self::ivaGeneral($config);
    }

    public static function agregarLinea(array $detalle, float $porcentaje, float $base): array
    {
        if ($base <= 0) {
            return $detalle;
        }

        $clave = number_format($porcentaje, 2, '.', '');

        if (! isset($detalle[$clave])) {
            $detalle[$clave] = [
                'porcentaje' => $porcentaje,
                'base' => 0,
                'iva' => 0,
            ];
        }

        $detalle[$clave]['base'] += $base;
        $detalle[$clave]['iva'] += $base * ($porcentaje / 100);

        return $detalle;
    }

    public static function detalleOrdenado(array $detalle): Collection
    {
        return collect($detalle)
            ->sortByDesc('porcentaje')
            ->values();
    }

    public static function etiquetaDetalle(Collection|array $detalle): string
    {
        $detalle = $detalle instanceof Collection ? $detalle : collect($detalle);

        $tasas = $detalle
            ->pluck('porcentaje')
            ->map(fn ($porcentaje) => self::formatearPorcentaje((float) $porcentaje).'%')
            ->unique()
            ->values();

        return $tasas->count() > 1
            ? 'IVA mixto ('.$tasas->implode(' / ').')'
            : 'IVA '.($tasas->first() ?? self::formatearPorcentaje(21).'%');
    }

    public static function formatearPorcentaje(float $porcentaje): string
    {
        return rtrim(rtrim(number_format($porcentaje, 2, '.', ''), '0'), '.');
    }
}
