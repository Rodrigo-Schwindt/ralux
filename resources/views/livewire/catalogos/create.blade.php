{{-- resources/views/admin/catalogos/create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-8 animate-fadeIn">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-slate-900">Crear Catálogo</h2>
        <a href="{{ route('catalogos.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <form action="{{ route('catalogos.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Título *</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('title') border-red-500 @enderror">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
            <input type="text" name="orden" value="{{ old('orden') }}" placeholder="AA, AB, AC..."
                   class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('orden') border-red-500 @enderror">
            @error('orden')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Subtítulo <span class="text-slate-400 font-normal">(texto sobre la imagen — Enter para salto de línea)</span></label>
            <textarea name="subtitle" id="subtitle" rows="2" placeholder="Catálogo&#10;de Productos"
                      class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 resize-none @error('subtitle') border-red-500 @enderror">{{ old('subtitle') }}</textarea>
            @error('subtitle')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Descripción <span class="text-slate-400 font-normal">(texto debajo del título)</span></label>
            <textarea name="descripcion" id="descripcion" rows="3" placeholder="Descargá nuestro catálogo actualizado con todos nuestros artículos en venta"
                      class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('descripcion') border-red-500 @enderror">{{ old('descripcion') }}</textarea>
            @error('descripcion')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            {{-- Imagen principal: más ancha --}}
            <div class="md:col-span-2 space-y-3">
                <label class="block text-sm font-medium text-slate-700">Imagen</label>
                <div id="placeholder-image-1"
                     class="w-full h-56 bg-slate-100 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-500 text-sm">
                    <div class="text-center">
                        <svg class="w-10 h-10 mx-auto mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-xs">Sin imagen</p>
                    </div>
                </div>
                <img id="preview-image-1" class="hidden w-full h-56 object-cover rounded-md border border-slate-200">
                <input id="file-image-1" type="file" name="image_1" accept="image/*" class="hidden">
                <button type="button" onclick="document.getElementById('file-image-1').click()"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer">
                    Subir Imagen
                </button>
                @error('image_1')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                <p class="text-xs text-slate-500">JPG / PNG / WEBP • Máx 5MB</p>
            </div>

            {{-- Ícono: más pequeño --}}
            <div class="space-y-3">
                <label class="block text-sm font-medium text-slate-700">Ícono</label>
                <div id="placeholder-image-2"
                     class="w-full h-28 bg-slate-100 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-500 text-sm">
                    <div class="text-center">
                        <svg class="w-7 h-7 mx-auto mb-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-xs">Sin ícono</p>
                    </div>
                </div>
                <img id="preview-image-2" alt="Preview ícono" class="hidden w-full h-28 object-contain rounded-md border border-slate-200 bg-slate-50 p-1">
                <input id="file-image-2" type="file" name="image_2" accept="image/*" class="hidden">
                <button type="button" onclick="document.getElementById('file-image-2').click()"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer">
                    Subir Ícono
                </button>
                @error('image_2')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                <p class="text-xs text-slate-500">JPG / PNG / WEBP • Máx 5MB</p>
            </div>
        </div>

        <div class="space-y-3">
            <label class="block text-sm font-medium text-slate-700">PDF</label>
            <div class="flex items-center gap-4">
                <div id="placeholder-pdf"
                     class="flex items-center gap-3 px-4 py-3 bg-slate-100 border-2 border-dashed border-slate-300 rounded-md text-slate-500 text-sm">
                    <svg class="w-8 h-8 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-xs">Sin archivo PDF</span>
                </div>
                <div id="preview-pdf" class="hidden flex items-center gap-3 px-4 py-3 bg-slate-50 border border-slate-200 rounded-md text-sm text-slate-700">
                    <svg class="w-8 h-8 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span id="filename-pdf" class="text-xs truncate max-w-xs"></span>
                </div>
                <input id="file-pdf" type="file" name="pdf" accept=".pdf" class="hidden">
                <button type="button" onclick="document.getElementById('file-pdf').click()"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer shrink-0">
                    Subir PDF
                </button>
            </div>
            @error('pdf')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
            <p class="text-xs text-slate-500">Máx 10MB</p>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="visible" id="visible" value="1" {{ old('visible', true) ? 'checked' : '' }}
                   class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
            <label for="visible" class="ml-2 text-sm font-medium text-slate-700">Visible</label>
        </div>

        <div class="flex gap-4 pt-4">
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium">
                Crear Catálogo
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

    // Preview imagen 1
    const fileInput1 = document.getElementById('file-image-1');
    const preview1 = document.getElementById('preview-image-1');
    const placeholder1 = document.getElementById('placeholder-image-1');

    fileInput1.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview1.src = e.target.result;
                preview1.classList.remove('hidden');
                placeholder1.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Preview imagen 2
    const fileInput2 = document.getElementById('file-image-2');
    const preview2 = document.getElementById('preview-image-2');
    const placeholder2 = document.getElementById('placeholder-image-2');

    fileInput2.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview2.src = e.target.result;
                preview2.classList.remove('hidden');
                placeholder2.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Preview PDF
    const pdfInput = document.getElementById('file-pdf');
    const pdfPreview = document.getElementById('preview-pdf');
    const pdfPlaceholder = document.getElementById('placeholder-pdf');
    const pdfFilename = document.getElementById('filename-pdf');

    pdfInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            pdfFilename.textContent = file.name;
            pdfPreview.classList.remove('hidden');
            pdfPlaceholder.classList.add('hidden');
        }
    });
});
</script>
@endsection