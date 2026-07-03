@extends('layouts.admin')

@section('content')
@php
    $prefijosEspeciales = old(
        'iva_prefijos_especiales',
        implode(', ', \App\Support\CarritoIva::prefijosEspeciales($config))
    );
@endphp
<div class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-8 animate-fadeIn">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-slate-900">Configuración del Carrito</h2>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-md bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.carrito-config.save') }}" method="POST"
          class="space-y-8">
        @csrf

        {{-- Textos --}}
        <div class="bg-white border border-slate-200 rounded-md shadow-sm p-6 space-y-6">
            <h3 class="text-base font-semibold text-slate-700 border-b border-slate-100 pb-3">Textos</h3>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Información del carrito
                    <span class="text-slate-400 font-normal">(texto lateral izquierdo)</span>
                </label>
                <textarea id="editor-informacion" name="informacion" rows="6"
                          class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">{{ old('informacion', $config->informacion) }}</textarea>
                @error('informacion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Placeholder "Escribinos un mensaje"
                </label>
                <input type="text" name="escribenos" value="{{ old('escribenos', $config->escribenos) }}"
                       placeholder="Ej: Días especiales de entrega, cambios de domicilio..."
                       class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm @error('escribenos') border-red-500 @enderror">
                @error('escribenos')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Descuentos por forma de pago --}}
        <div class="bg-white border border-slate-200 rounded-md shadow-sm p-6 space-y-6">
            <h3 class="text-base font-semibold text-slate-700 border-b border-slate-100 pb-3">Descuentos por forma de pago (%)</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Contado</label>
                    <div class="relative">
                        <input type="number" name="contado" step="0.01" min="0" max="100"
                               value="{{ old('contado', $config->contado ?? 0) }}"
                               class="w-full pl-4 pr-10 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm @error('contado') border-red-500 @enderror">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">%</span>
                    </div>
                    @error('contado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Transferencia</label>
                    <div class="relative">
                        <input type="number" name="transferencia" step="0.01" min="0" max="100"
                               value="{{ old('transferencia', $config->transferencia ?? 0) }}"
                               class="w-full pl-4 pr-10 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm @error('transferencia') border-red-500 @enderror">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">%</span>
                    </div>
                    @error('transferencia')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Cuenta corriente</label>
                    <div class="relative">
                        <input type="number" name="corriente" step="0.01" min="0" max="100"
                               value="{{ old('corriente', $config->corriente ?? 0) }}"
                               class="w-full pl-4 pr-10 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm @error('corriente') border-red-500 @enderror">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">%</span>
                    </div>
                    @error('corriente')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- IVA --}}
        <div class="bg-white border border-slate-200 rounded-md shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-700 border-b border-slate-100 pb-3 mb-6">IVA</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">IVA general</label>
                    <div class="relative">
                        <input type="number" name="iva" step="0.01" min="0" max="100"
                               value="{{ old('iva', $config->iva ?? 21) }}"
                               class="w-full pl-4 pr-10 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm @error('iva') border-red-500 @enderror">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">%</span>
                    </div>
                    @error('iva')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-slate-500">Se aplica a productos que no matchean la regla especial.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">IVA especial</label>
                    <div class="relative">
                        <input type="number" name="iva_especial" step="0.01" min="0" max="100"
                               value="{{ old('iva_especial', $config->iva_especial ?? 10.5) }}"
                               class="w-full pl-4 pr-10 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm @error('iva_especial') border-red-500 @enderror">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">%</span>
                    </div>
                    @error('iva_especial')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-slate-500">Por defecto, 10,5% para codigos especiales.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Prefijos de codigos con IVA especial</label>
                    <input type="text" name="iva_prefijos_especiales"
                           value="{{ $prefijosEspeciales }}"
                           placeholder="Ej: F, M"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm @error('iva_prefijos_especiales') border-red-500 @enderror">
                    @error('iva_prefijos_especiales')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-slate-500">Separalos con coma. No distingue mayusculas/minusculas ni espacios.</p>
                </div>
            </div>
        </div>

        <div class="flex gap-4 pt-2">
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium">
                Guardar configuración
            </button>
        </div>
    </form>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('editor-informacion')) CKEDITOR.replace('editor-informacion');
});
</script>
@endsection
