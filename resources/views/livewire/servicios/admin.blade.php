@extends('layouts.admin')

@section('content')

@php
$preview = $servicio && $servicio->image ? asset('storage/'.$servicio->image) : null;
@endphp

<div class="mx-auto py-10 space-y-10 bg-white border border-slate-200 rounded-md shadow-sm p-16">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-md px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- ─────────── Contenido principal ─────────── --}}
    <form method="POST"
          action="{{ route('servicios.save') }}"
          enctype="multipart/form-data"
          class="space-y-8">
        @csrf

        <h2 class="text-[26px] font-semibold text-slate-900">Calidad — Contenido</h2>

        <div class="space-y-2">
            <label class="block text-sm font-medium text-slate-900" for="title">Título *</label>
            <input id="title" type="text" name="title"
                   value="{{ old('title', $servicio->title ?? '') }}"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600">
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-medium text-slate-900" for="description_1">Descripción</label>
            <textarea id="description_1" name="description_1" rows="6"
                      class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600">{{ old('description_1', $servicio->description_1 ?? '') }}</textarea>
        </div>

        <div class="space-y-2 max-w-[600px]">
            <label class="block text-sm font-medium text-slate-900">Imagen principal</label>

            @if($preview)
                <img id="preview-image" src="{{ $preview }}" alt="preview"
                     class="w-full max-h-64 object-cover rounded-md border border-slate-200 mb-3">
            @else
                <div id="placeholder-image"
                     class="w-full h-40 bg-slate-100 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-500 text-sm">
                    Sin imagen
                </div>
                <img id="preview-image" alt="preview" class="hidden w-full max-h-64 object-cover rounded-md border border-slate-200 mb-3">
            @endif

            <div class="flex gap-3">
                <input id="file-image" type="file" name="image" accept="image/*" class="hidden">
                <button type="button" onclick="document.getElementById('file-image').click()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer">
                    {{ $preview ? 'Cambiar imagen' : 'Subir imagen' }}
                </button>
                @if($preview)
                <button type="button" onclick="deleteImage()"
                        class="px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition cursor-pointer">
                    Eliminar
                </button>
                @endif
            </div>
        </div>

        <div class="pt-6 border-t border-slate-200">
            <button type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition cursor-pointer">
                Guardar Cambios
            </button>
        </div>
    </form>

    {{-- ─────────── Descargas ─────────── --}}
    <div class="pt-8 border-t border-slate-200 space-y-6">

        <h2 class="text-[22px] font-semibold text-slate-900">Descargas</h2>

        {{-- Lista de descargas existentes --}}
        @if($servicio && $servicio->downloads->count() > 0)
        <div class="space-y-3">
            @foreach($servicio->downloads as $dl)
            <div class="flex items-center gap-4 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">

                {{-- Logo --}}
                <div class="w-12 h-10 flex-shrink-0 flex items-center justify-center">
                    @if($dl->image)
                        <img src="{{ asset('storage/'.$dl->image) }}" alt="logo" class="w-full h-full object-contain">
                    @else
                        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-slate-800 text-sm font-semibold truncate">{{ $dl->title ?: 'Sin título' }}</p>
                    @php
                        $ext = strtoupper(pathinfo($dl->file, PATHINFO_EXTENSION));
                        $fileSize = '';
                        if ($dl->file && Storage::disk('public')->exists($dl->file)) {
                            $bytes = Storage::disk('public')->size($dl->file);
                            $fileSize = $bytes >= 1048576
                                ? round($bytes / 1048576, 1) . ' MB'
                                : round($bytes / 1024) . ' KB';
                        }
                    @endphp
                    <p class="text-slate-500 text-xs">{{ $ext }}{{ $fileSize ? ' - ' . $fileSize : '' }}</p>
                </div>

                {{-- Ver archivo --}}
                <a href="{{ Storage::url($dl->file) }}" target="_blank"
                   class="text-blue-600 text-xs underline hover:text-blue-800 whitespace-nowrap">
                    Ver archivo
                </a>

                {{-- Eliminar --}}
                <form method="POST" action="{{ route('servicios.downloads.delete', $dl->id) }}"
                      onsubmit="return confirm('¿Eliminar esta descarga?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-3 py-1.5 bg-red-600 text-white text-xs rounded-md hover:bg-red-700 transition cursor-pointer">
                        Eliminar
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @else
            <p class="text-slate-500 text-sm">No hay descargas cargadas todavía.</p>
        @endif

        {{-- Formulario para agregar nueva descarga --}}
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-4">
            <h3 class="text-[16px] font-semibold text-slate-800">Agregar descarga</h3>

            <form method="POST" action="{{ route('servicios.downloads.add') }}" enctype="multipart/form-data"
                  class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="block text-sm font-medium text-slate-700" for="dl-title">Nombre del documento</label>
                    <input id="dl-title" type="text" name="title"
                           placeholder="Ej: Políticas de calidad"
                           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Logo / ícono</label>
                        <input type="file" name="image" accept="image/*"
                               class="block w-full text-sm text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300 cursor-pointer">
                        <p class="text-xs text-slate-400">Opcional · PNG, JPG, SVG</p>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-slate-700">Archivo *</label>
                        <input type="file" name="file" accept=".pdf,.doc,.docx" required
                               class="block w-full text-sm text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="text-xs text-slate-400">PDF, DOC, DOCX · Máx. 20 MB</p>
                    </div>
                </div>

                <button type="submit"
                        class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium transition cursor-pointer">
                    + Agregar descarga
                </button>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('description_1')) CKEDITOR.replace('description_1');
});

document.getElementById('file-image').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    document.getElementById('preview-image').src = url;
    document.getElementById('preview-image').classList.remove('hidden');
    document.getElementById('placeholder-image')?.classList.add('hidden');
});

function deleteImage() {
    if (!confirm('¿Eliminar imagen principal?')) return;
    fetch("{{ route('servicios.deleteImage') }}", {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    }).then(() => location.reload());
}
</script>

@endsection
