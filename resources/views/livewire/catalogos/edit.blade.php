{{-- resources/views/admin/catalogos/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-slate-900">Editar Catálogo</h2>
        <a href="{{ route('catalogos.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <form action="{{ route('catalogos.update', $catalogo) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Título *</label>
            <input type="text" name="title" value="{{ old('title', $catalogo->title) }}" required
                   class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('title') border-red-500 @enderror">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
            <input type="text" name="orden" value="{{ old('orden', $catalogo->orden) }}" placeholder="AA, AB, AC..."
                   class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('orden') border-red-500 @enderror">
            @error('orden')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Subtítulo <span class="text-slate-400 font-normal">(texto sobre la imagen — Enter para salto de línea)</span></label>
            <textarea name="subtitle" id="subtitle" rows="2" placeholder="Catálogo&#10;de Productos"
                      class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 resize-none @error('subtitle') border-red-500 @enderror">{{ old('subtitle', $catalogo->subtitle) }}</textarea>
            @error('subtitle')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Descripción <span class="text-slate-400 font-normal">(texto debajo del título)</span></label>
            <textarea name="descripcion" id="descripcion" rows="3" placeholder="Descargá nuestro catálogo actualizado con todos nuestros artículos en venta"
                      class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $catalogo->descripcion) }}</textarea>
            @error('descripcion')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Imagen 1</label>
                @if($catalogo->image_1)
                    <div class="mb-3">
                        <img src="{{ Storage::url($catalogo->image_1) }}" alt="Imagen actual" class="w-32 h-32 object-cover rounded-md border border-slate-200">
                        <p class="text-xs text-slate-500 mt-1">Imagen actual</p>
                    </div>
                @endif
                <input type="file" name="image_1" accept="image/*" id="image1Input"
                       class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('image_1') border-red-500 @enderror">
                @error('image_1')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="preview1" class="mt-3 hidden">
                    <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-md border border-slate-200">
                    <p class="text-xs text-slate-500 mt-1">Nueva imagen</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Imagen 2</label>
                @if($catalogo->image_2)
                    <div class="mb-3">
                        <img src="{{ Storage::url($catalogo->image_2) }}" alt="Imagen actual" class="w-32 h-32 object-cover rounded-md border border-slate-200">
                        <p class="text-xs text-slate-500 mt-1">Imagen actual</p>
                    </div>
                @endif
                <input type="file" name="image_2" accept="image/*" id="image2Input"
                       class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('image_2') border-red-500 @enderror">
                @error('image_2')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="preview2" class="mt-3 hidden">
                    <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-md border border-slate-200">
                    <p class="text-xs text-slate-500 mt-1">Nueva imagen</p>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">PDF</label>
            @if($catalogo->pdf)
                <div class="mb-3">
                    <a href="{{ Storage::url($catalogo->pdf) }}" target="_blank" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Ver PDF actual
                    </a>
                </div>
            @endif
            <input type="file" name="pdf" accept=".pdf" id="pdfInput"
                   class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('pdf') border-red-500 @enderror">
            @error('pdf')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p id="pdfName" class="mt-2 text-sm text-slate-600"></p>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="visible" id="visible" value="1" {{ old('visible', $catalogo->visible) ? 'checked' : '' }}
                   class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
            <label for="visible" class="ml-2 text-sm font-medium text-slate-700">Visible</label>
        </div>

        <div class="flex gap-4 pt-4">
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium">
                Actualizar Catálogo
            </button>
            <a href="{{ route('catalogos.index') }}"
               class="px-6 py-2.5 bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200 transition font-medium">
                Cancelar
            </a>
        </div>
    </form>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    CKEDITOR.replace('subtitle', {
        toolbar: [
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
            { name: 'paragraph',   items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
            { name: 'links',       items: ['Link', 'Unlink'] },
            { name: 'styles',      items: ['Format'] },
        ],
        height: 150,
        removePlugins: 'elementspath',
        resize_enabled: false,
    });

    CKEDITOR.replace('descripcion', {
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

    document.querySelector('form').addEventListener('submit', function() {
        if (CKEDITOR.instances.subtitle) CKEDITOR.instances.subtitle.updateElement();
        if (CKEDITOR.instances.descripcion) CKEDITOR.instances.descripcion.updateElement();
    });

    const image1Input = document.getElementById('image1Input');
    const image2Input = document.getElementById('image2Input');
    const pdfInput = document.getElementById('pdfInput');
    const preview1 = document.getElementById('preview1');
    const preview2 = document.getElementById('preview2');
    const pdfName = document.getElementById('pdfName');

    image1Input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview1.querySelector('img').src = e.target.result;
                preview1.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    image2Input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview2.querySelector('img').src = e.target.result;
                preview2.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    pdfInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            pdfName.textContent = `Nuevo archivo seleccionado: ${file.name}`;
        }
    });
});
</script>
@endsection