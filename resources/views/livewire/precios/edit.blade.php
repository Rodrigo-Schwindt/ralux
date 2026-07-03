@extends('layouts.admin')

@section('content')
<div class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-8 animate-fadeIn">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-slate-900">Editar Lista de Precios</h2>
        <a href="{{ route('precios.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <form action="{{ route('precios.update', $precio->id) }}" method="POST" enctype="multipart/form-data"
          class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Nombre *</label>
            <input type="text" name="title" value="{{ old('title', $precio->title) }}" required
                   class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('title') border-red-500 @enderror">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-4">
            <label class="block text-sm font-medium text-slate-700">Archivo <span class="text-slate-400 font-normal">(dejar vacío para mantener el actual)</span></label>

            {{-- Archivo actual --}}
            @php
                $extension = strtoupper(pathinfo($precio->archivo, PATHINFO_EXTENSION));
                $disk = \Illuminate\Support\Facades\Storage::disk('public');
                $peso = $disk->exists($precio->archivo) ? round($disk->size($precio->archivo) / 1024) . ' kb' : '-';
            @endphp
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-md flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-slate-700">{{ $precio->title }}</p>
                        <p class="text-xs text-slate-400">{{ $extension }} • {{ $peso }}</p>
                    </div>
                </div>
                <a href="{{ Storage::url($precio->archivo) }}" target="_blank"
                   class="text-xs text-blue-600 hover:underline">Ver archivo actual</a>
            </div>

            {{-- Nuevo archivo --}}
            <div id="placeholder-archivo"
                 class="h-28 w-full bg-slate-100 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-500 text-sm cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition"
                 onclick="document.getElementById('file-archivo').click()">
                <div class="text-center">
                    <p class="text-sm">Hacer clic para reemplazar el archivo</p>
                    <p class="text-xs text-slate-400 mt-1">PDF, XLS, XLSX • Máx 20MB</p>
                </div>
            </div>

            <div id="preview-archivo" class="hidden p-4 bg-slate-50 border border-slate-200 rounded-md flex items-center gap-3">
                <svg class="w-8 h-8 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-slate-700" id="filename-archivo"></p>
                    <p class="text-xs text-slate-400" id="filesize-archivo"></p>
                </div>
            </div>

            <input id="file-archivo" type="file" name="archivo" accept=".pdf,.xls,.xlsx" class="hidden">

            @error('archivo')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-3 text-sm text-slate-700">
            <input type="checkbox" name="publicado" value="1" {{ $precio->publicado ? 'checked' : '' }}
                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
            Publicar esta lista para clientes
        </label>

        <div class="flex gap-4 pt-4">
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium">
                Guardar cambios
            </button>
            <a href="{{ route('precios.index') }}"
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
document.addEventListener('DOMContentLoaded', function () {
    const fileInput   = document.getElementById('file-archivo');
    const preview     = document.getElementById('preview-archivo');
    const placeholder = document.getElementById('placeholder-archivo');
    const filename    = document.getElementById('filename-archivo');
    const filesize    = document.getElementById('filesize-archivo');

    fileInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            filename.textContent = file.name;
            filesize.textContent = (file.size / 1024).toFixed(0) + ' kb';
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
    });
});
</script>
@endsection
