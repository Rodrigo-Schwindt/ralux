@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-8 animate-fadeIn">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Editar Equivalencia</h2>
        <a href="{{ route('equivalencias.index') }}"
           class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition cursor-pointer">
            ← Volver a la lista
        </a>
    </div>

    <div id="alertContainer"></div>

    <form id="mainForm" class="bg-white rounded-md border border-slate-200 p-6 space-y-6 shadow-sm">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Producto Ralux *</label>
            <div class="relative" id="productoDropdown">
                <input type="hidden" id="producto_id" name="producto_id" value="{{ $equivalencia->producto_id }}">
                <input type="text" id="productoSearch" autocomplete="off"
                       value="{{ $equivalencia->producto ? $equivalencia->producto->codigo_ralux . ' — ' . $equivalencia->producto->descripcion_es : '' }}"
                       placeholder="Buscar por código o descripción..."
                       class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                <div id="productoOptions"
                     class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-md shadow-lg max-h-60 overflow-y-auto hidden">
                    @foreach($productos as $producto)
                        <div class="producto-option px-3 py-2 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer"
                             data-value="{{ $producto->id }}"
                             data-label="{{ $producto->codigo_ralux }} — {!! $producto->descripcion_es !!}">
                            <span class="font-mono text-xs text-slate-500">{{ $producto->codigo_ralux }}</span>
                            <span class="ml-1">{!! $producto->descripcion_es !!}</span>
                        </div>
                    @endforeach
                    <div id="noResults" class="px-3 py-2 text-sm text-slate-400 hidden">Sin resultados</div>
                </div>
            </div>
            <p id="producto_id-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-900 mb-2">Código Distribuidor *</label>
                <input type="text" id="codigo" name="codigo"
                       value="{{ $equivalencia->codigo }}"
                       placeholder="Ej: AB-1234, XYZ-9876..."
                       class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                <p id="codigo-error" class="mt-1 text-red-600 text-sm hidden"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900 mb-2">Distribuidor</label>
                <input type="text" id="distribuidor" name="distribuidor"
                       value="{{ $equivalencia->distribuidor }}"
                       placeholder="Ej: Bosch, NGK, Valeo..."
                       class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                <p id="distribuidor-error" class="mt-1 text-red-600 text-sm hidden"></p>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route('equivalencias.index') }}"
               class="px-5 py-2 border border-slate-300 rounded-md text-sm text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                Cancelar
            </a>
            <button type="submit" id="submitBtn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="submitText">Guardar Cambios</span>
                <span id="submitLoading" class="hidden items-center gap-2">
                    <div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin inline-block"></div>
                    Guardando...
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
document.addEventListener('DOMContentLoaded', function() {
    const form       = document.getElementById('mainForm');
    const submitBtn  = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoad = document.getElementById('submitLoading');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoad.classList.remove('hidden');
        submitLoad.classList.add('flex');

        fetch('{{ route("equivalencias.update", $equivalencia->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => { window.location.href = data.redirect; }, 1000);
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const el = document.getElementById(`${field}-error`);
                        if (el) {
                            el.textContent = Array.isArray(data.errors[field]) ? data.errors[field][0] : data.errors[field];
                            el.classList.remove('hidden');
                        }
                    });
                } else {
                    showAlert(data.message || 'Error al guardar', 'error');
                }
            }
        })
        .catch(() => showAlert('Error al guardar la equivalencia', 'error'))
        .finally(() => {
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitLoad.classList.add('hidden');
            submitLoad.classList.remove('flex');
        });
    });

    // Searchable producto select
    const productoSearch  = document.getElementById('productoSearch');
    const productoId      = document.getElementById('producto_id');
    const productoOptions = document.getElementById('productoOptions');
    const noResults       = document.getElementById('noResults');
    const allOptions      = productoOptions.querySelectorAll('.producto-option');

    productoSearch.addEventListener('focus', () => productoOptions.classList.remove('hidden'));
    productoSearch.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        productoId.value = '';
        let visible = 0;
        allOptions.forEach(opt => {
            const match = opt.dataset.label.toLowerCase().includes(q);
            opt.classList.toggle('hidden', !match);
            if (match) visible++;
        });
        noResults.classList.toggle('hidden', visible > 0);
        productoOptions.classList.remove('hidden');
    });

    allOptions.forEach(opt => {
        opt.addEventListener('click', function() {
            productoId.value     = this.dataset.value;
            productoSearch.value = this.dataset.label;
            productoOptions.classList.add('hidden');
        });
    });

    document.addEventListener('click', function(e) {
        if (!document.getElementById('productoDropdown').contains(e.target)) {
            productoOptions.classList.add('hidden');
        }
    });

    function clearErrors() {
        document.querySelectorAll('[id$="-error"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
    }

    function showAlert(message, type) {
        const cls = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
        document.getElementById('alertContainer').innerHTML = `<div class="px-4 py-3 rounded-md ${cls} border text-sm">${message}</div>`;
        setTimeout(() => { document.getElementById('alertContainer').innerHTML = ''; }, 4000);
    }
});
</script>
@endsection
