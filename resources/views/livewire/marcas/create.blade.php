@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-8 animate-fadeIn">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Crear Marca</h2>
        <a href="{{ route('marcas.index') }}"
           class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition cursor-pointer">
            ← Volver a la lista
        </a>
    </div>

    <div id="alertContainer"></div>

    <form id="mainForm" class="bg-white rounded-md border border-slate-200 p-6 space-y-6 shadow-sm">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Título *</label>
            <input type="text" id="descripcion_es" name="descripcion_es"
                   placeholder="Nombre de la marca"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            <p id="descripcion_es-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Tipo de Vehículo *</label>
            <select id="vehiculo_tipo_id" name="vehiculo_tipo_id"
                    class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                <option value="">Seleccionar tipo...</option>
                @foreach($vehiculoTipos as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->descripcion_es }}</option>
                @endforeach
            </select>
            <p id="vehiculo_tipo_id-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div class="w-48">
            <label class="block text-sm font-medium text-slate-900 mb-2">Orden</label>
            <input type="text" id="orden" name="orden" maxlength="10"
                   placeholder="Ej: AA, AB, AAA..."
                   oninput="this.value = this.value.toUpperCase().replace(/[^A-Z]/g,'')"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none uppercase">
            <p id="orden-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div class="border border-slate-200 rounded-md p-4 bg-slate-50">
            <p class="text-sm font-medium text-slate-900 mb-4">Opciones de visibilidad</p>
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" id="visible" name="visible" value="1" checked
                               class="sr-only peer">
                        <div class="w-10 h-5 bg-slate-200 peer-checked:bg-green-500 rounded-full transition peer-focus:ring-2 peer-focus:ring-green-400"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-5"></div>
                    </div>
                    <span class="text-sm text-slate-700">Visible</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route('marcas.index') }}"
               class="px-5 py-2 border border-slate-300 rounded-md text-sm text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                Cancelar
            </a>
            <button type="submit" id="submitBtn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="submitText">Crear Marca</span>
                <span id="submitLoading" class="hidden items-center gap-2">
                    <div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin inline-block"></div>
                    Creando...
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

        fetch('{{ route("marcas.store") }}', {
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
                        if (el) { el.textContent = Array.isArray(data.errors[field]) ? data.errors[field][0] : data.errors[field]; el.classList.remove('hidden'); }
                    });
                } else {
                    showAlert(data.message || 'Error al crear', 'error');
                }
            }
        })
        .catch(() => showAlert('Error al crear la marca', 'error'))
        .finally(() => {
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitLoad.classList.add('hidden');
            submitLoad.classList.remove('flex');
        });
    });

    function clearErrors() {
        document.querySelectorAll('[id$="-error"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
    }

    function showAlert(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        const cls = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
        alertContainer.innerHTML = `<div class="px-4 py-3 rounded-md ${cls} border text-sm">${message}</div>`;
        setTimeout(() => { alertContainer.innerHTML = ''; }, 4000);
    }
});
</script>
@endsection
