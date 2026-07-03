<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Metadata extends Model
{
    protected $fillable = [
        'section',
        'keywords',
        'description',
    ];

    public static function getForSection($section)
    {
        return static::where('section', $section)->first();
    }

    public static function getForProduct($productId)
    {
        $metadata = static::where('section', 'producto-' . $productId)->first();

        if ($metadata) {
            return $metadata;
        }

        $producto = Producto::with(['tipo', 'marcas', 'modelos', 'vehiculoTipos'])
            ->find($productId);

        if (!$producto) {
            return null;
        }

        $generated = static::fromProduct($producto);

        return static::firstOrCreate(
            ['section' => $generated->section],
            [
                'keywords' => $generated->keywords,
                'description' => $generated->description,
            ]
        );
    }

    public static function getForCategoria($categoriaId)
    {
        return static::where('section', 'categoria-' . $categoriaId)->first();
    }

    public static function getForNovedad($novedadId)
    {
        return static::where('section', 'novedad-' . $novedadId)->first();
    }

    public static function fromProduct(Producto $producto): self
    {
        $description = static::cleanText(
            $producto->descripcion_es
            ?: $producto->descripcion_en
            ?: $producto->codigo_ralux
            ?: 'Ralux - Autopartes'
        );

        return new static([
            'section' => 'producto-' . $producto->id,
            'keywords' => static::productKeywords($producto),
            'description' => Str::limit($description, 160, ''),
        ]);
    }

    private static function productKeywords(Producto $producto): string
    {
        $keywords = collect()
            ->push('Ralux')
            ->push($producto->codigo_ralux)
            ->merge($producto->marcas->pluck('descripcion_es'))
            ->merge($producto->vehiculoTipos->pluck('descripcion_es'))
            ->merge($producto->modelos->pluck('descripcion_es'))
            ->push($producto->tipo?->descripcion_es)
            ->merge(static::productCharacteristics($producto))
            ->map(fn($value) => static::cleanText($value))
            ->filter()
            ->unique(fn($value) => Str::lower($value))
            ->values();

        return $keywords->implode(', ');
    }

    private static function productCharacteristics(Producto $producto): array
    {
        $characteristics = [];

        foreach (['voltaje' => 'Voltaje', 'amperaje' => 'Amperaje', 'terminales' => 'Terminales'] as $field => $label) {
            if (filled($producto->{$field})) {
                $characteristics[] = trim($label . ' ' . $producto->{$field});
            }
        }

        for ($index = 1; $index <= 10; $index++) {
            $name = $producto->{'caract_' . $index};
            $value = $producto->{'valor_' . $index};

            if (filled($name) && filled($value)) {
                $characteristics[] = trim($name . ' ' . $value);
            } elseif (filled($name)) {
                $characteristics[] = $name;
            } elseif (filled($value)) {
                $characteristics[] = $value;
            }
        }

        return $characteristics;
    }

    private static function cleanText($value): string
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text ?? '');
    }
}
