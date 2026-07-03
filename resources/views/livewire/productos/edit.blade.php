@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-8 animate-fadeIn">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Editar Producto #{{ $producto->id }}</h2>
        <a href="{{ route('productos.index') }}"
           class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition cursor-pointer">
            ← Volver a la lista
        </a>
    </div>

    <div id="alertContainer"></div>

    <form id="mainForm" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm space-y-5">
            <h3 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-3">Información General</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Código Ralux *</label>
                    <input type="text" name="codigo_ralux" value="{{ $producto->codigo_ralux }}"
                           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none font-mono">
                    <p id="codigo_ralux-error" class="mt-1 text-red-600 text-sm hidden"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Tipo de Producto *</label>
                    <select name="producto_tipo_id"
                            class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <option value="">Seleccionar tipo...</option>
                        @foreach($productoTipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ $producto->producto_tipo_id == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->descripcion_es }}
                            </option>
                        @endforeach
                    </select>
                    <p id="producto_tipo_id-error" class="mt-1 text-red-600 text-sm hidden"></p>
                </div>
                               <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Precio $</label>
                    <input type="number" step="0.01" name="precio" value="{{ $producto->precio }}" placeholder="0.00"
                           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <p id="precio-error" class="mt-1 text-red-600 text-sm hidden"></p>
                </div>
                                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Descuento (%)</label>
                    <input type="number" step="0.01" name="descuento" value="{{ $producto->descuento ?? 0 }}" placeholder="0.00" min="0" max="100"
                           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <p id="descuento-error" class="mt-1 text-red-600 text-sm hidden"></p>
                </div>


            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Orden *</label>
                    <input type="text" name="orden" min="0" value="{{ $producto->orden }}"
                           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <p id="orden-error" class="mt-1 text-red-600 text-sm hidden"></p>
                </div>
 
                {{-- <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Período Desde</label>
                    <input type="date" name="periodo_desde" value="{{ $producto->periodo_desde?->format('Y-m-d') }}"
                           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <p id="periodo_desde-error" class="mt-1 text-red-600 text-sm hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Período Hasta</label>
                    <input type="date" name="periodo_hasta" value="{{ $producto->periodo_hasta?->format('Y-m-d') }}"
                           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    <p id="periodo_hasta-error" class="mt-1 text-red-600 text-sm hidden"></p>
                </div> --}}
            </div>

            <div>
                <label for="descripcion_es" class="block text-sm font-medium text-slate-900 mb-2">Descripción</label>
                <textarea name="descripcion_es" id="descripcion_es" rows="6"
                          class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">{!! $producto->descripcion_es !!}</textarea>
                <p id="descripcion_es-error" class="mt-1 text-red-600 text-sm hidden"></p>
            </div>
        </div>

        <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm space-y-4">
            <h3 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-3">Especificaciones Técnicas</h3>
                            <div class="flex gap-3 items-center" style="">
                    <span class="text-sm font-medium text-slate-700">Soporte</span>
                    <label class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="soporte" value="1" {{ $producto->soporte ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-10 h-5 bg-slate-200 peer-checked:bg-purple-600 rounded-full transition"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                </div>
            <div class="grid gap-1" style="grid-template-columns: 1fr 1fr">
                <span class="text-xs font-medium text-slate-600">Especificación</span>
                <span class="text-xs font-medium text-slate-600">Valor</span>
            </div>

            <div class="space-y-2">
                <div class="grid gap-3 items-center" style="grid-template-columns: 1fr 1fr">
                    <span class="text-sm font-medium text-black">Voltaje</span>
                    <div>
                        <input type="text" name="voltaje" value="{{ $producto->voltaje }}" placeholder="Ej: 12V"
                               class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <p id="voltaje-error" class="mt-1 text-red-600 text-sm hidden"></p>
                    </div>
                </div>
                <div class="grid gap-3 items-center" style="grid-template-columns: 1fr 1fr">
                    <span class="text-sm font-medium text-black">Amperaje</span>
                    <div>
                        <input type="text" name="amperaje" value="{{ $producto->amperaje }}" placeholder="Ej: 5A"
                               class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <p id="amperaje-error" class="mt-1 text-red-600 text-sm hidden"></p>
                    </div>
                </div>
                <div class="grid gap-3 items-center" style="grid-template-columns: 1fr 1fr">
                    <span class="text-sm font-medium text-black">Terminales</span>
                    <div>
                        <input type="text" name="terminales" value="{{ $producto->terminales }}" placeholder="Ej: 3T"
                               class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        <p id="terminales-error" class="mt-1 text-red-600 text-sm hidden"></p>
                    </div>
                </div>
                            <div id="caractContainer" class="space-y-2"></div>

            <div class="flex items-center gap-3">
                <button type="button" id="addCaractBtn" onclick="addCaract()"
                        class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar característica
                </button>
                <span id="caractCounter" class="text-xs text-slate-400">0/10</span>
            </div>

            </div>
        </div>



        <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm space-y-5">
            <h3 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-3">Compatibilidad de Vehículos</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Tipos de Vehículo</label>
                    <input type="text" placeholder="Buscar tipo..." oninput="filterCheckboxes(this, 'vehiculoTiposList')"
                           class="w-full border border-slate-300 rounded-md px-3 py-1.5 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none mb-2">
                    <div id="vehiculoTiposList" class="border border-slate-300 rounded-md p-3 max-h-52 overflow-y-auto space-y-2 bg-white">
                        @forelse($vehiculoTipos as $vt)
                            <label class="flex items-center gap-2 cursor-pointer hover:text-blue-700">
                                <input type="checkbox" name="vehiculo_tipo_ids[]" value="{{ $vt->id }}"
                                       {{ in_array($vt->id, $selectedVehiculoTipos) ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                                <span class="text-sm text-slate-700">{{ $vt->descripcion_es }}</span>
                            </label>
                        @empty
                            <p class="text-xs text-slate-500">Sin tipos disponibles</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Marcas</label>
                    <input type="text" placeholder="Buscar marca..." oninput="filterCheckboxes(this, 'marcasList')"
                           class="w-full border border-slate-300 rounded-md px-3 py-1.5 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none mb-2">
                    <div id="marcasList" class="border border-slate-300 rounded-md p-3 max-h-52 overflow-y-auto space-y-2 bg-white">
                        @forelse($marcas as $marca)
                            @if(!empty($marca->descripcion_es))
                            <label class="flex items-center gap-2 cursor-pointer hover:text-blue-700">
                                <input type="checkbox" name="marca_ids[]" value="{{ $marca->id }}"
                                       {{ in_array($marca->id, $selectedMarcas) ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                                <span class="text-sm text-slate-700">
                                    {{ $marca->descripcion_es }}
                                    @if($marca->vehiculoTipo)<span class="text-xs text-slate-400">· {{ $marca->vehiculoTipo->descripcion_es }}</span>@endif
                                </span>
                            </label>
                            @endif
                        @empty
                            <p class="text-xs text-slate-500">Sin marcas disponibles</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Modelos</label>
                    <input type="text" placeholder="Buscar modelo..." oninput="filterCheckboxes(this, 'modelosList')"
                           class="w-full border border-slate-300 rounded-md px-3 py-1.5 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none mb-2">
                    <div id="modelosList" class="border border-slate-300 rounded-md p-3 max-h-52 overflow-y-auto space-y-2 bg-white">
                        @forelse($modelos as $modelo)
                            <label class="flex items-center gap-2 cursor-pointer hover:text-blue-700">
                                <input type="checkbox" name="modelo_ids[]" value="{{ $modelo->id }}"
                                       {{ in_array($modelo->id, $selectedModelos) ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                                <span class="text-sm text-slate-700">
                                    {{ $modelo->descripcion_es }}
                                    @if($modelo->marca)<span class="text-xs text-slate-400">· {{ $modelo->marca->descripcion_es }}</span>@endif
                                </span>
                            </label>
                        @empty
                            <p class="text-xs text-slate-500">Sin modelos disponibles</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 6: Galería del producto --}}
        <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm space-y-4">
            <h3 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-3">Galería del Producto</h3>

            @php
                $archivosProducto = [
                    'pdf' => ['label' => 'PDF', 'type' => 'documento'],
                    'sonido' => ['label' => 'Sonido', 'type' => 'audio'],
                    'video' => ['label' => 'Video principal', 'type' => 'video'],
                ];
                $tieneArchivosProducto = collect(array_keys($archivosProducto))->contains(fn($field) => filled($producto->{$field}));
            @endphp

            @if($tieneArchivosProducto)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($archivosProducto as $field => $archivo)
                        @if($producto->{$field})
                            <div id="archivo-{{ $field }}" class="rounded-md border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800">{{ $archivo['label'] }}</p>
                                        <p class="text-xs text-slate-500 truncate mt-1">{{ basename($producto->{$field}) }}</p>
                                    </div>
                                    <button type="button"
                                            onclick="deleteArchivo('{{ $field }}', '{{ $archivo['label'] }}')"
                                            class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                                        Eliminar
                                    </button>
                                </div>

                                @if($archivo['type'] === 'video')
                                    <video src="{{ Storage::url($producto->{$field}) }}" class="mt-3 w-full h-32 rounded bg-slate-900 object-cover" controls preload="metadata"></video>
                                @elseif($archivo['type'] === 'audio')
                                    <audio src="{{ Storage::url($producto->{$field}) }}" class="mt-3 w-full" controls></audio>
                                @else
                                    <a href="{{ Storage::url($producto->{$field}) }}" target="_blank" class="inline-flex mt-3 text-xs text-blue-600 hover:text-blue-700">
                                        Ver archivo
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($producto->imagenes->count())
                <div id="existingImages" class="flex flex-wrap gap-4">
                    @foreach($producto->imagenes as $imagen)
                        <div class="relative group" id="imagen-{{ $imagen->id }}">

                            @if($imagen->tipo === 'video')
                                <div class="w-28 h-28 rounded-md border border-slate-200 bg-slate-900 flex items-center justify-center overflow-hidden">
                                    <video src="{{ Storage::url($imagen->ruta) }}"
                                           class="w-full h-full object-cover" muted preload="metadata"></video>
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <svg class="w-8 h-8 text-white opacity-80" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>
                                <span class="absolute top-1 left-1 bg-slate-700 text-white text-xs px-1.5 py-0.5 rounded">Video</span>
                            @else
                                <img src="{{ Storage::url($imagen->ruta) }}" alt="{{ $imagen->alt }}"
                                     class="w-28 h-28 object-cover rounded-md border {{ $imagen->principal ? 'border-blue-500 ring-2 ring-blue-400' : 'border-slate-200' }}">
                                @if($imagen->principal)
                                    <span class="absolute top-1 left-1 bg-blue-600 text-white text-xs px-1.5 py-0.5 rounded">Principal</span>
                                @endif
                            @endif

                            <div class="absolute inset-0 bg-black/50 rounded-md opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center gap-2">
                                @if($imagen->tipo !== 'video' && !$imagen->principal)
                                    <button type="button"
                                            onclick="setPrincipal({{ $producto->id }}, {{ $imagen->id }})"
                                            class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                                        Principal
                                    </button>
                                @endif
                                <button type="button"
                                        onclick="deleteImagen({{ $producto->id }}, {{ $imagen->id }})"
                                        class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p id="noImagesMsg" class="text-sm text-slate-500">Sin archivos en la galería.</p>
            @endif

            <div class="border-t border-slate-100 pt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Agregar imágenes</label>
                    <input type="file" name="imagenes[]" id="imagenesInput" multiple accept=".jpg,.jpeg,.png,.gif,.webp"
                           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded-md file:border-0 file:cursor-pointer cursor-pointer">
                    <p class="text-xs text-slate-500 mt-1">JPG, PNG, GIF, WebP · Máx. 10MB por imagen.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-2">Agregar videos a la galería</label>
                    <input type="file" name="videos_galeria[]" id="videosGaleriaInput" multiple accept=".mp4,.webm,.ogg,.mov,.avi"
                           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white file:bg-purple-600 file:text-white file:px-4 file:py-2 file:rounded-md file:border-0 file:cursor-pointer cursor-pointer">
                    <p class="text-xs text-slate-500 mt-1">MP4, WebM, OGG, MOV, AVI · Máx. 20MB por video.</p>
                </div>
            </div>

            <div id="imagePreviewContainer" class="hidden">
                <p class="text-sm font-medium text-slate-700 mb-2">Vista previa:</p>
                <div id="imagePreviews" class="flex flex-wrap gap-3"></div>
            </div>
                        <div>
                <label for="imagen_diagrama" class="block text-sm font-medium text-slate-900 mb-2">Imagen diagrama</label>
                @if($producto->imagen_diagrama)
                    <div id="archivo-imagen_diagrama" class="mb-3 inline-block rounded-md border border-slate-200 bg-slate-50 p-3">
                        <img src="{{ Storage::url($producto->imagen_diagrama) }}" alt="Diagrama actual"
                             class="h-32 w-auto rounded border border-slate-200 object-contain bg-slate-50 p-1">
                        <div class="mt-2 flex items-center justify-between gap-3">
                            <p class="text-xs text-slate-500 truncate">{{ basename($producto->imagen_diagrama) }}</p>
                            <button type="button"
                                    onclick="deleteArchivo('imagen_diagrama', 'Imagen diagrama')"
                                    class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                                Eliminar
                            </button>
                        </div>
                    </div>
                @endif
                <input type="file" id="imagen_diagrama" name="imagen_diagrama" accept=".jpg,.jpeg,.png,.gif,.webp"
                       class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white file:bg-blue-600 file:text-white file:px-3 file:py-1 file:rounded file:border-0 file:cursor-pointer cursor-pointer">
                <p class="text-xs text-slate-500 mt-1">Máx. 10MB · JPG, PNG, GIF, WebP{{ $producto->imagen_diagrama ? ' (reemplazará la actual)' : '' }}</p>
                <p id="imagen_diagrama-error" class="mt-1 text-red-600 text-sm hidden"></p>
            </div>
            <div>
                <label for="diagrama_orientativo" class="block text-sm font-medium text-slate-900 mb-2">Diagrama orientativo</label>
                @if($producto->diagrama_orientativo)
                    <div id="archivo-diagrama_orientativo" class="mb-3 inline-block rounded-md border border-slate-200 bg-slate-50 p-3">
                        <img src="{{ Storage::url($producto->diagrama_orientativo) }}" alt="Diagrama orientativo actual"
                             class="h-32 w-auto rounded border border-slate-200 object-contain bg-slate-50 p-1">
                        <div class="mt-2 flex items-center justify-between gap-3">
                            <p class="text-xs text-slate-500 truncate">{{ basename($producto->diagrama_orientativo) }}</p>
                            <button type="button"
                                    onclick="deleteArchivo('diagrama_orientativo', 'Diagrama orientativo')"
                                    class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                                Eliminar
                            </button>
                        </div>
                    </div>
                @endif
                <input type="file" id="diagrama_orientativo" name="diagrama_orientativo" accept=".jpg,.jpeg,.png,.gif,.webp"
                       class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white file:bg-blue-600 file:text-white file:px-3 file:py-1 file:rounded file:border-0 file:cursor-pointer cursor-pointer">
                <p class="text-xs text-slate-500 mt-1">Max. 10MB - JPG, PNG, GIF, WebP{{ $producto->diagrama_orientativo ? ' (reemplazara la actual)' : '' }}</p>
                <p id="diagrama_orientativo-error" class="mt-1 text-red-600 text-sm hidden"></p>
            </div>
        </div>

        {{-- SECCIÓN 7: Visibilidad --}}
        <div class="bg-white rounded-md border border-slate-200 p-6 shadow-sm">
            <h3 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-3 mb-4">Visibilidad</h3>
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="destacado" value="1" {{ $producto->destacado ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-10 h-5 bg-slate-200 peer-checked:bg-yellow-500 rounded-full transition"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-5"></div>
                    </div>
                    <span class="text-sm text-slate-700">Destacado</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="visible" value="1" {{ $producto->visible ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-10 h-5 bg-slate-200 peer-checked:bg-green-500 rounded-full transition"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-5"></div>
                    </div>
                    <span class="text-sm text-slate-700">Visible</span>
                </label>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('productos.index') }}"
               class="px-5 py-2 border border-slate-300 rounded-md text-sm text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                Cancelar
            </a>
            <button type="submit" id="submitBtn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="submitText">Actualizar Producto</span>
                <span id="submitLoading" class="hidden items-center gap-2">
                    <div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin inline-block"></div>
                    Actualizando...
                </span>
            </button>
        </div>
    </form>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const productoId = {{ $producto->id }};

let caractCount = 0;
const MAX_CARACT = 10;

function addCaract(caract, valor) {
    caract = caract || '';
    valor  = valor  || '';
    if (caractCount >= MAX_CARACT) return;
    caractCount++;
    const n   = caractCount;
    const row = document.createElement('div');
    row.className = 'caract-row grid gap-80 items-center';
    row.style.gridTemplateColumns = '1fr 1fr 2rem';

    const i1 = document.createElement('input');
    i1.type = 'text'; i1.name = 'caract_' + n; i1.value = caract;
    i1.placeholder = 'Ej: Color, Material...';
    i1.className = 'w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none';

    const i2 = document.createElement('input');
    i2.type = 'text'; i2.name = 'valor_' + n; i2.value = valor;
    i2.placeholder = 'Ej: Rojo, Acero...';
    i2.className = 'w-full border border-slate-300 ml-5 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none';

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.onclick = function() { removeCaract(this); };
    btn.className = 'flex items-center justify-center text-slate-400 hover:text-red-500 transition';
    btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';

    row.appendChild(i1); row.appendChild(i2); row.appendChild(btn);
    document.getElementById('caractContainer').appendChild(row);
    updateAddBtn();
}

function removeCaract(btn) {
    btn.closest('.caract-row').remove();
    document.querySelectorAll('.caract-row').forEach((row, i) => {
        row.querySelectorAll('input')[0].name = 'caract_' + (i + 1);
        row.querySelectorAll('input')[1].name = 'valor_'  + (i + 1);
    });
    caractCount = document.querySelectorAll('.caract-row').length;
    updateAddBtn();
}

function updateAddBtn() {
    const btn = document.getElementById('addCaractBtn');
    if (!btn) return;
    const atMax = caractCount >= MAX_CARACT;
    btn.disabled = atMax;
    btn.classList.toggle('opacity-50', atMax);
    btn.classList.toggle('cursor-not-allowed', atMax);
    document.getElementById('caractCounter').textContent = caractCount + '/10';
}

document.addEventListener('DOMContentLoaded', function() {
    CKEDITOR.replace('descripcion_es', {
        toolbar: [
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
            { name: 'paragraph',   items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
            { name: 'links',       items: ['Link', 'Unlink'] },
            { name: 'styles',      items: ['Format'] },
        ],
        height: 200,
        removePlugins: 'elementspath',
        resize_enabled: false,
    });

    const form       = document.getElementById('mainForm');
    const submitBtn  = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoad = document.getElementById('submitLoading');

    // Pre-cargar características existentes
    @for($i = 1; $i <= 10; $i++)
        @if(!empty($producto->{'caract_'.$i}))
            addCaract(@json($producto->{'caract_'.$i}), @json($producto->{'valor_'.$i} ?? ''));
        @endif
    @endfor

    function refreshPreviewContainer() {
        const container = document.getElementById('imagePreviewContainer');
        const previews  = document.getElementById('imagePreviews');
        const hasItems  = previews.children.length > 0;
        container.classList.toggle('hidden', !hasItems);
    }

    // Image preview
    document.getElementById('imagenesInput').addEventListener('change', function() {
        const previews = document.getElementById('imagePreviews');
        // Remove old image previews (keep video ones)
        previews.querySelectorAll('[data-preview-type="image"]').forEach(el => el.remove());

        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const wrap = document.createElement('div');
                wrap.dataset.previewType = 'image';
                wrap.className = 'relative';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-24 h-24 object-cover rounded-md border border-slate-200';
                wrap.appendChild(img);
                previews.appendChild(wrap);
                refreshPreviewContainer();
            };
            reader.readAsDataURL(file);
        });
        if (!this.files.length) refreshPreviewContainer();
    });

    // Video preview
    document.getElementById('videosGaleriaInput').addEventListener('change', function() {
        const previews = document.getElementById('imagePreviews');
        previews.querySelectorAll('[data-preview-type="video"]').forEach(el => el.remove());

        Array.from(this.files).forEach(file => {
            const url  = URL.createObjectURL(file);
            const wrap = document.createElement('div');
            wrap.dataset.previewType = 'video';
            wrap.className = 'relative w-24 h-24 rounded-md border border-slate-200 bg-slate-900 overflow-hidden flex items-center justify-center';

            const vid = document.createElement('video');
            vid.src = url;
            vid.className = 'w-full h-full object-cover';
            vid.muted = true;
            vid.preload = 'metadata';

            const icon = document.createElement('div');
            icon.className = 'absolute inset-0 flex items-center justify-center pointer-events-none';
            icon.innerHTML = '<svg class="w-7 h-7 text-white opacity-80" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';

            const label = document.createElement('span');
            label.className = 'absolute bottom-1 left-1 bg-slate-700 text-white text-[10px] px-1 rounded';
            label.textContent = 'Video';

            wrap.appendChild(vid);
            wrap.appendChild(icon);
            wrap.appendChild(label);
            previews.appendChild(wrap);
            refreshPreviewContainer();
        });
        if (!this.files.length) refreshPreviewContainer();
    });

    // Main form submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (CKEDITOR.instances.descripcion_es) {
            CKEDITOR.instances.descripcion_es.updateElement();
        }
        document.querySelectorAll('[id$="-error"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
        submitBtn.disabled = true; submitText.classList.add('hidden'); submitLoad.classList.remove('hidden'); submitLoad.classList.add('flex');

        fetch('{{ route("productos.update", $producto->id) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => { window.location.href = data.redirect; }, 1000);
            } else if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const el = document.getElementById(`${field}-error`);
                    if (el) { el.textContent = Array.isArray(data.errors[field]) ? data.errors[field][0] : data.errors[field]; el.classList.remove('hidden'); }
                });
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else { showAlert(data.message || 'Error al actualizar', 'error'); }
        })
        .catch(() => showAlert('Error al actualizar el producto', 'error'))
        .finally(() => { submitBtn.disabled = false; submitText.classList.remove('hidden'); submitLoad.classList.add('hidden'); submitLoad.classList.remove('flex'); });
    });
});

function setPrincipal(productoId, imagenId) {
    fetch(`/admin/productos/${productoId}/imagenes/${imagenId}/principal`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { window.location.reload(); }
    })
    .catch(() => showAlert('Error al actualizar imagen principal', 'error'));
}

function deleteImagen(productoId, imagenId) {
    if (!confirm('¿Eliminar esta imagen?')) return;

    fetch(`/admin/productos/${productoId}/imagenes/${imagenId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`imagen-${imagenId}`)?.remove();
            showAlert(data.message, 'success');
        }
    })
    .catch(() => showAlert('Error al eliminar la imagen', 'error'));
}

function deleteArchivo(field, label) {
    if (!confirm(`¿Eliminar ${label}?`)) return;

    fetch(`/admin/productos/${productoId}/archivo/${field}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`archivo-${field}`)?.remove();
            showAlert(data.message, 'success');
        } else {
            showAlert(data.message || 'Error al eliminar el archivo', 'error');
        }
    })
    .catch(() => showAlert('Error al eliminar el archivo', 'error'));
}

function filterCheckboxes(input, listId) {
    const query = input.value.toLowerCase().trim();
    document.querySelectorAll('#' + listId + ' label').forEach(label => {
        const text = label.textContent.toLowerCase();
        label.style.display = text.includes(query) ? '' : 'none';
    });
}

function showAlert(message, type) {
    const cls = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
    document.getElementById('alertContainer').innerHTML = `<div class="px-4 py-3 rounded-md ${cls} border text-sm">${message}</div>`;
    setTimeout(() => { document.getElementById('alertContainer').innerHTML = ''; }, 5000);
}
</script>
@endsection
