@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn">
    <div class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-blue-600">Gestion comercial</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Listas de Precios</h2>
                <p class="mt-2 text-sm text-slate-500 max-w-2xl">
                    Carga una lista propia o genera una lista desde los productos visibles. La lista marcada como publicada es la que ve el cliente en la zona privada.
                </p>
            </div>

            <div class="rounded-md border border-slate-200 bg-slate-50 px-4 py-3 min-w-[240px]">
                <p class="text-xs uppercase tracking-wide text-slate-500">Publicada actualmente</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">
                    {{ $listaPublicada?->title ?? 'Sin lista publicada' }}
                </p>
            </div>
        </div>

        <div id="alertContainer"></div>

        @if(session('success'))
            <div class="px-4 py-3 rounded-md bg-green-50 border border-green-200 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="px-4 py-3 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm">
                Revisar los datos ingresados. {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <form action="{{ route('precios.store') }}" method="POST" enctype="multipart/form-data"
                  class="border border-slate-200 rounded-md bg-white p-6 space-y-5">
                @csrf

                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12V4m0 0L8 8m4-4l4 4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Subir lista propia</h3>
                        <p class="text-sm text-slate-500">Usa un PDF o Excel ya preparado para clientes.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nombre *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="Ej: Lista de precios Abril 2026"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Archivo *</label>
                    <label for="archivo-propio"
                           class="h-28 w-full bg-slate-50 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-500 text-sm cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                        <span id="archivo-propio-label" class="text-center px-4">
                            Seleccionar PDF, XLS o XLSX<br>
                            <span class="text-xs text-slate-400">Maximo 20MB</span>
                        </span>
                    </label>
                    <input id="archivo-propio" type="file" name="archivo" accept=".pdf,.xls,.xlsx" class="hidden" required>
                </div>

                <label class="flex items-center gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="publicado" value="1"
                           class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                    Publicar esta lista para clientes
                </label>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                    </svg>
                    Guardar lista propia
                </button>
            </form>

            <form action="{{ route('precios.generar-desde-productos') }}" method="POST"
                  class="border border-slate-200 rounded-md bg-slate-50 p-6 space-y-5">
                @csrf

                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-3M5 19h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Crear lista desde productos</h3>
                        <p class="text-sm text-slate-500">Genera un Excel profesional con {{ $productosVisibles }} productos visibles.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nombre</label>
                    <input type="text" name="title"
                           placeholder="Lista de precios - {{ now()->format('d/m/Y') }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-600">
                </div>

                <div class="rounded-md border border-emerald-100 bg-white p-4 text-sm text-slate-600">
                    <p class="font-medium text-slate-800 mb-2">Incluye:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <span>Codigo</span>
                        <span>Descripcion</span>
                        <span>Precio lista</span>
                    </div>
                </div>

                <label class="flex items-center gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="publicado" value="1" checked
                           class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-600">
                    Publicar esta lista para clientes
                </label>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M20 9A8 8 0 006.34 4.34L4 6.68M4 15a8 8 0 0013.66 4.66L20 17.32"/>
                    </svg>
                    Generar Excel de productos
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Historial de listas ({{ count($precios) }})</h3>
                <p class="text-sm text-slate-500">Puedes conservar varias listas y elegir cual publicar.</p>
            </div>
            <a href="{{ route('precios.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200 transition text-sm">
                Pantalla clasica
            </a>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-md bg-white">
            <table class="w-full text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Nombre</th>
                        <th class="px-4 py-3 text-center font-medium">Origen</th>
                        <th class="px-4 py-3 text-center font-medium">Formato</th>
                        <th class="px-4 py-3 text-center font-medium">Peso</th>
                        <th class="px-4 py-3 text-center font-medium">Fecha</th>
                        <th class="px-4 py-3 text-center font-medium">Estado</th>
                        <th class="px-4 py-3 text-center font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($precios as $precio)
                    @php
                        $esGenerada = $precio->tipo !== 'propia';
                        $extension = strtoupper(pathinfo($precio->archivo, PATHINFO_EXTENSION));
                        $disk = \Illuminate\Support\Facades\Storage::disk('public');
                        $peso = $disk->exists($precio->archivo)
                            ? round($disk->size($precio->archivo) / 1024) . ' kb'
                            : '-';
                    @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ $precio->title }}</p>
                            @if($precio->generado_at)
                                <p class="text-xs text-slate-400">Generada el {{ $precio->generado_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $esGenerada ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ $esGenerada ? 'Generada' : 'Propia' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">{{ $extension }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-500">{{ $peso }}</td>
                        <td class="px-4 py-3 text-center text-slate-500">{{ $precio->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($precio->publicado)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    Publicada
                                </span>
                            @else
                                <form action="{{ route('precios.publicar', $precio->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 rounded hover:bg-slate-200 transition">
                                        Publicar
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ Storage::url($precio->archivo) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition">
                                    Ver
                                </a>
                                <a href="{{ route('precios.edit', $precio->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-amber-600 bg-amber-50 rounded hover:bg-amber-100 transition">
                                    Editar
                                </a>
                                <button type="button"
                                        class="delete-btn inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded hover:bg-red-100 transition"
                                        data-id="{{ $precio->id }}">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-400">No hay listas de precios cargadas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertContainer = document.getElementById('alertContainer');
    const archivoPropio = document.getElementById('archivo-propio');
    const archivoPropioLabel = document.getElementById('archivo-propio-label');

    if (archivoPropio) {
        archivoPropio.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            archivoPropioLabel.innerHTML = `<strong class="text-slate-700">${file.name}</strong><br><span class="text-xs text-slate-400">${(file.size / 1024).toFixed(0)} kb</span>`;
        });
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.dataset.id;

            if (confirm('Estas seguro de eliminar esta lista de precios?')) {
                fetch(`/admin/precios/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        btn.closest('tr').remove();
                        showAlert(data.message, 'success');
                    }
                })
                .catch(() => showAlert('Error al eliminar', 'error'));
            }
        }
    });

    function showAlert(message, type) {
        const color = type === 'success'
            ? 'bg-green-50 border-green-200 text-green-700'
            : 'bg-red-50 border-red-200 text-red-700';
        alertContainer.innerHTML = `<div class="px-4 py-3 rounded-md ${color} border text-sm mb-4">${message}</div>`;
        setTimeout(() => alertContainer.innerHTML = '', 4000);
    }
});
</script>
@endsection
