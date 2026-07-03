<?php

namespace App\Http\Controllers\Productos;

use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\ProductoTipo;
use App\Models\VehiculoTipo;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductoCreate extends Controller
{
    public function create()
    {
        $productoTipos = ProductoTipo::orderBy('orden')->get();
        $vehiculoTipos = VehiculoTipo::where('visible', true)->orderBy('orden')->get();
        $marcas        = Marca::with('vehiculoTipo')->orderBy('descripcion_es')->get();
        $modelos       = Modelo::with('marca')->orderBy('descripcion_es')->get();

        return view('livewire.productos.create', compact('productoTipos', 'vehiculoTipos', 'marcas', 'modelos'));
    }

    public function store(Request $request)
    {
        $rules = [
            'codigo_ralux'     => 'required|string|max:100',
            'descripcion_es'   => 'required|string|max:255',
            'descripcion_en'   => 'nullable|string|max:255',
            'producto_tipo_id' => 'required|exists:productos_tipo,id',
            'voltaje'          => 'nullable|string|max:100',
            'amperaje'         => 'nullable|string|max:100',
            'terminales'       => 'nullable|string|max:100',
            'precio'           => 'nullable|numeric',
            'descuento'        => 'nullable|numeric|min:0|max:100',
            'periodo_desde'    => 'nullable|date',
            'periodo_hasta'    => 'nullable|date|after_or_equal:periodo_desde',
            'safe_url'         => 'nullable|string|max:500',
            'pdf'              => 'nullable|file|mimes:pdf|max:51200',
            'sonido'           => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
            'video'            => 'nullable|file|mimes:mp4,webm,ogg,mov,avi|max:102400',
            'imagen_diagrama'  => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'diagrama_orientativo' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'orden'            => 'required|string|max:10',
            'vehiculo_tipo_ids'   => 'nullable|array',
            'vehiculo_tipo_ids.*' => 'exists:vehiculo_tipo,id',
            'marca_ids'           => 'nullable|array',
            'marca_ids.*'         => 'exists:marcas,id',
            'modelo_ids'          => 'nullable|array',
            'modelo_ids.*'        => 'exists:modelos,id',
            'imagenes'            => 'nullable|array',
            'imagenes.*'          => 'file|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ];

        for ($i = 1; $i <= 10; $i++) {
            $rules["caract_{$i}"] = 'nullable|string|max:100';
            $rules["valor_{$i}"]  = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        // Booleans
        $validated['soporte']   = $request->boolean('soporte');
        $validated['estado']    = $request->boolean('estado');
        $validated['destacado'] = $request->boolean('destacado');
        $validated['visible']   = $request->boolean('visible');

        // File uploads
        foreach (['pdf', 'sonido', 'video'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store("productos/{$field}", 'public');
            }
        }

        if ($request->hasFile('imagen_diagrama')) {
            $validated['imagen_diagrama'] = $request->file('imagen_diagrama')->store('productos/diagramas', 'public');
        }

        if ($request->hasFile('diagrama_orientativo')) {
            $validated['diagrama_orientativo'] = $request->file('diagrama_orientativo')->store('productos/diagramas-orientativos', 'public');
        }

        // Remove relation arrays before creating
        $vehiculoTipoIds = $request->input('vehiculo_tipo_ids', []);
        $marcaIds        = $request->input('marca_ids', []);
        $modeloIds       = $request->input('modelo_ids', []);
        unset($validated['vehiculo_tipo_ids'], $validated['marca_ids'], $validated['modelo_ids'], $validated['imagenes']);

        $producto = Producto::create($validated);

        // Sync many-to-many
        $producto->vehiculoTipos()->sync($vehiculoTipoIds);
        $producto->marcas()->sync($marcaIds);
        $producto->modelos()->sync($modeloIds);

        // Upload images
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $index => $file) {
                $ruta = $file->store('productos/imagenes', 'public');
                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta'        => $ruta,
                    'alt'         => $producto->descripcion_es,
                    'orden'       => $index + 1,
                    'principal'   => $index === 0,
                ]);
            }
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Producto creado correctamente',
            'redirect' => route('productos.index'),
        ]);
    }
}
