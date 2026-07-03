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
use Illuminate\Support\Facades\Storage;

class ProductoEdit extends Controller
{
    public function edit($id)
    {
        $producto = Producto::with([
            'tipo', 'vehiculoTipos', 'marcas', 'modelos', 'imagenes'
        ])->findOrFail($id);

        $productoTipos = ProductoTipo::orderBy('orden')->get();
        $vehiculoTipos = VehiculoTipo::where('visible', true)->orderBy('orden')->get();
        $marcas        = Marca::with('vehiculoTipo')->orderBy('descripcion_es')->get();
        $modelos       = Modelo::with('marca')->orderBy('descripcion_es')->get();

        $selectedVehiculoTipos = $producto->vehiculoTipos->pluck('id')->toArray();
        $selectedMarcas        = $producto->marcas->pluck('id')->toArray();
        $selectedModelos       = $producto->modelos->pluck('id')->toArray();

        return view('livewire.productos.edit', compact(
            'producto', 'productoTipos', 'vehiculoTipos', 'marcas', 'modelos',
            'selectedVehiculoTipos', 'selectedMarcas', 'selectedModelos'
        ));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

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
            'videos_galeria'      => 'nullable|array',
            'videos_galeria.*'    => 'file|mimes:mp4,webm,ogg,mov,avi|max:20480',
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

        // File uploads (replace if new file provided)
        foreach (['pdf', 'sonido', 'video'] as $field) {
            if ($request->hasFile($field)) {
                if ($producto->$field && Storage::disk('public')->exists($producto->$field)) {
                    Storage::disk('public')->delete($producto->$field);
                }
                $validated[$field] = $request->file($field)->store("productos/{$field}", 'public');
            } else {
                unset($validated[$field]);
            }
        }

        if ($request->hasFile('imagen_diagrama')) {
            if ($producto->imagen_diagrama && Storage::disk('public')->exists($producto->imagen_diagrama)) {
                Storage::disk('public')->delete($producto->imagen_diagrama);
            }
            $validated['imagen_diagrama'] = $request->file('imagen_diagrama')->store('productos/diagramas', 'public');
        } else {
            unset($validated['imagen_diagrama']);
        }

        if ($request->hasFile('diagrama_orientativo')) {
            if ($producto->diagrama_orientativo && Storage::disk('public')->exists($producto->diagrama_orientativo)) {
                Storage::disk('public')->delete($producto->diagrama_orientativo);
            }
            $validated['diagrama_orientativo'] = $request->file('diagrama_orientativo')->store('productos/diagramas-orientativos', 'public');
        } else {
            unset($validated['diagrama_orientativo']);
        }

        $vehiculoTipoIds = $request->input('vehiculo_tipo_ids', []);
        $marcaIds        = $request->input('marca_ids', []);
        $modeloIds       = $request->input('modelo_ids', []);
        unset($validated['vehiculo_tipo_ids'], $validated['marca_ids'], $validated['modelo_ids'], $validated['imagenes'], $validated['videos_galeria']);

        $producto->update($validated);

        // Sync many-to-many
        $producto->vehiculoTipos()->sync($vehiculoTipoIds);
        $producto->marcas()->sync($marcaIds);
        $producto->modelos()->sync($modeloIds);

        // Append new images
        if ($request->hasFile('imagenes')) {
            $maxOrden = $producto->imagenes()->max('orden') ?? 0;
            $hasPrincipal = $producto->imagenes()->where('principal', true)->exists();

            foreach ($request->file('imagenes') as $index => $file) {
                $ruta = $file->store('productos/imagenes', 'public');
                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta'        => $ruta,
                    'alt'         => $producto->descripcion_es,
                    'orden'       => $maxOrden + $index + 1,
                    'principal'   => !$hasPrincipal && $index === 0,
                    'tipo'        => 'galeria',
                ]);
                $hasPrincipal = true;
            }
        }

        // Append new gallery videos
        if ($request->hasFile('videos_galeria')) {
            $maxOrden = $producto->imagenes()->max('orden') ?? 0;

            foreach ($request->file('videos_galeria') as $index => $file) {
                $ruta = $file->store('productos/galeria/videos', 'public');
                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta'        => $ruta,
                    'alt'         => $producto->descripcion_es,
                    'orden'       => $maxOrden + $index + 1,
                    'principal'   => false,
                    'tipo'        => 'video',
                ]);
            }
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Producto actualizado correctamente',
            'redirect' => route('productos.index'),
        ]);
    }
}
