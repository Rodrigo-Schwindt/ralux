<?php

namespace App\Support;

use App\Models\Marca;
use App\Models\Modelo;
use App\Models\ProductoTipo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductSearch
{
    public static function applyTipoFilter(Builder $query, $tipoId): void
    {
        if (!$tipoId) {
            return;
        }

        $tipo = ProductoTipo::find($tipoId);

        if (!$tipo) {
            $query->whereRaw('1 = 0');
            return;
        }

        $prefixes = self::searchPrefixes((string) $tipo->descripcion_es);

        $query->whereHas('tipo', function (Builder $q) use ($tipoId, $prefixes) {
            $q->where('productos_tipo.id', $tipoId);

            foreach ($prefixes as $prefix) {
                $q->orWhere('productos_tipo.descripcion_es', 'like', "{$prefix}%");
            }
        });
    }

    public static function applyMarcaFilter(Builder $query, $marcaId): void
    {
        if (!$marcaId) {
            return;
        }

        $marca = Marca::find($marcaId);

        if (!$marca) {
            $query->whereRaw('1 = 0');
            return;
        }

        $marcaIds = self::equivalentMarcaIds($marcaId);
        $prefixes = self::searchPrefixes((string) $marca->descripcion_es);

        $query->whereHas('marcas', function (Builder $q) use ($marcaIds, $prefixes) {
            $q->whereIn('marcas.id', $marcaIds);

            foreach ($prefixes as $prefix) {
                $q->orWhere('marcas.descripcion_es', 'like', "{$prefix}%");
            }
        });
    }

    public static function equivalentMarcaIds($marcaId): array
    {
        if (!$marcaId) {
            return [];
        }

        $marca = Marca::find($marcaId);

        if (!$marca) {
            return [(int) $marcaId];
        }

        $normalized = self::normalizeBrandName((string) $marca->descripcion_es);
        $equivalentGroups = [
            ['general-motors', 'chevrolet'],
        ];

        foreach ($equivalentGroups as $group) {
            if (in_array($normalized, $group, true)) {
                return Marca::query()
                    ->get(['id', 'descripcion_es'])
                    ->filter(fn (Marca $item) => in_array(self::normalizeBrandName((string) $item->descripcion_es), $group, true))
                    ->pluck('id')
                    ->push((int) $marcaId)
                    ->unique()
                    ->values()
                    ->all();
            }
        }

        return [(int) $marcaId];
    }

    public static function applyModeloFilter(Builder $query, $modeloId): void
    {
        if (!$modeloId) {
            return;
        }

        $modelo = Modelo::find($modeloId);

        if (!$modelo) {
            $query->whereRaw('1 = 0');
            return;
        }

        $prefixes = self::searchPrefixes((string) $modelo->descripcion_es);

        $query->whereHas('modelos', function (Builder $q) use ($modeloId, $prefixes) {
            $q->where('modelos.id', $modeloId);

            foreach ($prefixes as $prefix) {
                $q->orWhere('modelos.descripcion_es', 'like', "{$prefix}%");
            }
        });
    }

    public static function applyTextSearch(Builder $query, ?string $search): void
    {
        $search = trim((string) $search);

        if ($search === '') {
            return;
        }

        $terms = preg_split('/\s+/', $search) ?: [];
        $terms = array_values(array_unique(array_filter($terms, fn($term) => trim($term) !== '')));

        $query->where(function (Builder $q) use ($terms) {
            foreach ($terms as $term) {
                $q->where(function (Builder $termQuery) use ($term) {
                    self::applySingleTermSearch($termQuery, trim($term));
                });
            }
        });
    }

    private static function applySingleTermSearch(Builder $query, string $term): void
    {
        $like = "%{$term}%";
        $termNorm = self::normalizeCode($term);

        $query->where('descripcion_es', 'like', $like)
            ->orWhere('codigo_ralux', 'like', $like)
            ->orWhereHas('tipo', fn(Builder $q) => $q->where('descripcion_es', 'like', $like))
            ->orWhereHas('marcas', fn(Builder $q) => $q->where('descripcion_es', 'like', $like))
            ->orWhereHas('modelos', fn(Builder $q) => $q->where('descripcion_es', 'like', $like))
            ->orWhereHas('codigosOM', fn(Builder $q) => $q->where('codigo', 'like', $like))
            ->orWhereHas('equivalencias', fn(Builder $q) => $q->where('codigo', 'like', $like));

        if ($termNorm !== '') {
            $query->orWhereRaw(self::normalizedSql('codigo_ralux') . ' LIKE ?', ["%{$termNorm}%"])
                ->orWhereHas('codigosOM', fn(Builder $q) => $q->whereRaw(self::normalizedSql('codigo') . ' LIKE ?', ["%{$termNorm}%"]))
                ->orWhereHas('equivalencias', fn(Builder $q) => $q->whereRaw(self::normalizedSql('codigo') . ' LIKE ?', ["%{$termNorm}%"]));
        }
    }

    public static function applyRelevanceOrder(Builder $query, ?string $search): void
    {
        $search = trim((string) $search);

        if ($search === '') {
            $query->orderBy('orden');
            return;
        }

        $terms = array_values(array_unique(array_filter(
            preg_split('/\s+/', $search) ?: [],
            fn($t) => trim($t) !== ''
        )));

        $conditions = [];
        $bindings   = [];

        foreach ($terms as $term) {
            $conditions[] = 'codigo_ralux LIKE ?';
            $bindings[]   = "%{$term}%";

            $norm = self::normalizeCode($term);
            if ($norm !== '') {
                $conditions[] = self::normalizedSql('codigo_ralux') . ' LIKE ?';
                $bindings[]   = "%{$norm}%";
            }
        }

        // Computed priority column: 0 when codigo_ralux matches, 1 otherwise.
        // Using selectRaw instead of orderByRaw to avoid binding issues with paginate().
        $caseSql = 'CASE WHEN (' . implode(' OR ', $conditions) . ') THEN 0 ELSE 1 END';
        $query->selectRaw("*, {$caseSql} as _ralux_priority", $bindings)
              ->orderBy('_ralux_priority')
              ->orderBy('orden');
    }

    private static function normalizeCode(string $value): string
    {
        return preg_replace('/[-\/\s]+/', '', trim($value)) ?? '';
    }

    private static function normalizeBrandName(string $value): string
    {
        $value = Str::ascii(Str::lower(trim($value)));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';

        return trim($value, '-');
    }

    private static function searchPrefixes(string $value): array
    {
        $value = trim($value);

        if ($value === '') {
            return [];
        }

        $firstWord = preg_split('/\s+/', $value)[0] ?? $value;

        return array_values(array_unique(array_filter([$value, $firstWord])));
    }

    private static function normalizedSql(string $column): string
    {
        return "REPLACE(REPLACE(REPLACE({$column}, '-', ''), '/', ''), ' ', '')";
    }
}
