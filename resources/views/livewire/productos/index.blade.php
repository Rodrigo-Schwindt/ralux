@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Productos (<span id="totalCount">{{ $productos->total() }}</span>)</h2>

        <div class="flex items-center gap-2 flex-wrap">

            {{-- Exportar dropdown --}}
            <div class="relative" id="exportDropdownWrapper">
                <button id="exportDropdownBtn" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition text-sm font-medium cursor-pointer active:scale-[.98]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exportar
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="exportDropdown"
                     class="hidden absolute right-0 mt-1 w-52 bg-white border border-slate-200 rounded-md shadow-lg z-20 overflow-hidden">
                    <a href="{{ route('productos.exportar') }}"
                       class="flex items-center gap-2.5 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Exportar productos
                    </a>
                    <div class="border-t border-slate-100"></div>
                    <a href="{{ route('productos.exportar-plantilla') }}"
                       class="flex items-center gap-2.5 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Descargar plantilla
                    </a>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <a href="{{ route('productos.backup-db') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 text-white rounded-md hover:bg-slate-900 transition text-sm font-medium cursor-pointer active:scale-[.98]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-8m0 8l-3-3m3 3l3-3M5 20h14"/>
                    </svg>
                    Crear copia de seguridad
                </a>
            </div>

            {{-- Importar --}}
            <button id="openImportBtn" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 text-white rounded-md hover:bg-amber-600 transition text-sm font-medium cursor-pointer active:scale-[.98]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Importar
            </button>

            {{-- Crear --}}
            <a href="{{ route('productos.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm font-medium cursor-pointer active:scale-[.98]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Crear Producto
            </a>
        </div>
    </div>

    <div id="alertContainer"></div>

    @if (session('error'))
        <div class="px-4 py-3 rounded-md bg-red-50 border border-red-200 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-md p-4 shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_220px_170px_auto] gap-3">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" id="searchInput" value="{{ request('search', '') }}" placeholder="Buscar por código, descripción..."
                       class="w-full pl-10 pr-10 py-2.5 border border-slate-300 rounded-md bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm transition">
                <button id="clearSearch" type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 {{ request('search', '') ? '' : 'hidden' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <select id="tipoFilter"
                    class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm transition">
                <option value="">Todas las categorias</option>
                @foreach($tipos as $tipo)
                    <option value="{{ $tipo->id }}" @selected((string) request('tipo_id', '') === (string) $tipo->id)>{{ $tipo->descripcion_es }}</option>
                @endforeach
            </select>

            <select id="visibleFilter"
                    class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm transition">
                <option value="">Todos</option>
                <option value="1" @selected((string) request('visible', '') === '1')>Visibles</option>
                <option value="0" @selected((string) request('visible', '') === '0')>Ocultos</option>
                <option value="destacado" @selected((string) request('visible', '') === 'destacado')>Destacados</option>
            </select>

            <button id="clearFilters" type="button"
                    class="px-4 py-2.5 border border-slate-300 text-slate-700 rounded-md hover:bg-slate-100 transition text-sm font-medium">
                Limpiar
            </button>
        </div>
        <p id="searchResults" class="mt-2 text-sm text-slate-600 hidden"></p>
    </div>

    <p class="text-sm text-slate-500 text-center">
        Página <span id="currentPage">{{ $productos->currentPage() }}</span> de <span id="lastPage">{{ $productos->lastPage() }}</span> —
        Mostrando <span id="firstItem">{{ $productos->firstItem() ?? 0 }}</span>–<span id="lastItem">{{ $productos->lastItem() ?? 0 }}</span> de <span id="totalItems">{{ $productos->total() }}</span> registros
    </p>

    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="orden">Orden</th>
                    <th class="px-4 py-3 text-center font-medium">Imagen</th>
                    <th class="px-4 py-3 font-medium text-start cursor-pointer hover:text-slate-900 sort-header" data-sort="codigo_ralux">Código / Descripción</th>
                    <th class="px-4 py-3 text-end font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="precio">Precio</th>
                    <th class="px-4 py-3 font-medium text-start">Tipo</th>
                    <th class="px-4 py-3 text-center font-medium">Destacado</th>
                    <th class="px-4 py-3 text-center font-medium">Visible</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody id="productosTable" class="divide-y divide-slate-200">
                @include('livewire.productos.partials.table', ['productos' => $productos])
            </tbody>
        </table>
    </div>

    <div id="pagination">
        @include('livewire.productos.partials.pagination', ['productos' => $productos])
    </div>
</div>

{{-- ══════════════════════════════════════════════════════ SLIDE-OVER IMPORT ══ --}}
<div id="importOverlay" class="fixed inset-0 bg-black/40 z-40 hidden"></div>

<aside id="importPanel"
       class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-50 flex flex-col transform translate-x-full transition-transform duration-300 ease-in-out">

    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 bg-slate-50 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-md bg-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-slate-900">Importar Productos</h3>
                <p class="text-xs text-slate-500">Archivo Excel (.xlsx / .xls)</p>
            </div>
        </div>
        <button id="closeImportBtn" type="button"
                class="text-slate-400 hover:text-slate-600 transition p-1.5 rounded-md hover:bg-slate-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Body --}}
    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-5">

        {{-- Drop zone --}}
        <div id="dropZone"
             class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:border-amber-400 hover:bg-amber-50/60 transition-all group">
            <input type="file" id="importFile" accept=".xlsx,.xls" class="hidden">

            <div id="dropZoneIdle">
                <svg class="w-10 h-10 mx-auto text-slate-300 group-hover:text-amber-400 transition mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium text-slate-600">Arrastrá el archivo aquí</p>
                <p class="text-xs text-slate-400 mt-1">o hacé clic para seleccionar</p>
                <p class="text-xs text-slate-300 mt-3">.xlsx / .xls — máx. 10 MB</p>
            </div>

            <div id="dropZoneFile" class="hidden">
                <svg class="w-9 h-9 mx-auto text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p id="dropZoneFileName" class="text-sm font-semibold text-slate-700 break-all px-2"></p>
                <button type="button" id="removeFileBtn"
                        class="mt-2 text-xs text-red-500 hover:underline">Quitar archivo</button>
            </div>
        </div>

        {{-- Info boxes --}}
        <div class="rounded-lg bg-amber-50 border border-amber-100 px-4 py-3 flex gap-3">
            <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-xs text-amber-700 space-y-1.5">
                <p class="font-semibold">Modos disponibles</p>
                <ul class="list-disc list-inside space-y-0.5 text-amber-600">
                    <li><strong>Crear y actualizar</strong>: actualiza por <code class="bg-amber-100 px-1 rounded font-mono">codigo_ralux</code> y crea si no existe</li>
                    <li><strong>Solo actualizar</strong>: modifica solo productos existentes y omite los codigos nuevos</li>
                </ul>
            </div>
        </div>

        <div class="rounded-lg bg-blue-50 border border-blue-100 px-4 py-3 flex gap-3">
            <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-xs text-blue-700 space-y-1.5">
                <p class="font-semibold">Comportamiento de la importación</p>
                <ul class="list-disc list-inside space-y-0.5 text-blue-600">
                    <li>Si el <code class="bg-blue-100 px-1 rounded font-mono">codigo_ralux</code> ya existe → actualiza</li>
                    <li>Si no existe → crea el producto</li>
                    <li>Las celdas vacías <strong>no sobreescriben</strong> datos existentes</li>
                    <li>Las relaciones solo se sincronizan si la columna tiene valor</li>
                </ul>
            </div>
        </div>

        <div class="rounded-lg bg-red-50 border border-red-100 px-4 py-3 flex gap-3">
            <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="text-xs text-red-700 space-y-1">
                <p class="font-semibold">Columna <code class="bg-red-100 px-1 rounded font-mono">eliminar</code></p>
                <p class="text-red-600">Poniendo <strong>1</strong> en esa columna, el producto se elimina permanentemente junto con todas sus imágenes y archivos. Esta acción no se puede deshacer.</p>
            </div>
        </div>

        <div class="rounded-lg bg-slate-50 border border-slate-200 px-4 py-3 flex gap-3">
            <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xs text-slate-500">
                Usá <strong>Descargar productos o plantilla</strong> para obtener el archivo con el formato correcto y las hojas de referencia de IDs (vehículo tipos, marcas, modelos, producto tipos).
            </p>
        </div>
    </div>

    {{-- Footer --}}
    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 shrink-0">
        <div id="importError"
             class="hidden mb-3 px-3 py-2.5 rounded-md bg-red-50 border border-red-200 text-xs text-red-700 leading-relaxed"></div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" id="cancelImportBtn"
                    class="flex-1 px-4 py-2.5 border border-slate-300 text-slate-700 rounded-md hover:bg-slate-100 transition text-sm font-medium">
                Cancelar
            </button>
            <button type="button" id="submitImportBtn"
                    class="flex-1 px-4 py-2.5 bg-amber-500 text-white rounded-md hover:bg-amber-600 transition text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <svg id="importSpinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span id="importBtnText">Crear y actualizar</span>
            </button>
            <button type="button" id="submitImportUpdateOnlyBtn"
                    class="flex-1 px-4 py-2.5 bg-slate-800 text-white rounded-md hover:bg-slate-900 transition text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <svg id="importUpdateOnlySpinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span id="importUpdateOnlyBtnText">Solo actualizar</span>
            </button>
        </div>
    </div>
</aside>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput   = document.getElementById('searchInput');
    const clearSearch   = document.getElementById('clearSearch');
    const searchResults = document.getElementById('searchResults');
    const tipoFilter    = document.getElementById('tipoFilter');
    const visibleFilter = document.getElementById('visibleFilter');
    const clearFilters  = document.getElementById('clearFilters');
    let searchTimeout;
    let currentSort = { field: 'orden', direction: 'asc' };

    // ── Búsqueda ──────────────────────────────────────────────────────────────
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        clearSearch.classList.toggle('hidden', !this.value);
        searchTimeout = setTimeout(() => loadItems(1), 300);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = ''; this.classList.add('hidden'); loadItems(1);
    });

    tipoFilter.addEventListener('change', () => loadItems(1));
    visibleFilter.addEventListener('change', () => loadItems(1));

    clearFilters.addEventListener('click', function() {
        searchInput.value = '';
        tipoFilter.value = '';
        visibleFilter.value = '';
        clearSearch.classList.add('hidden');
        searchResults.classList.add('hidden');
        loadItems(1);
    });

    document.querySelectorAll('.sort-header').forEach(h => h.addEventListener('click', function() {
        const field = this.dataset.sort;
        if (currentSort.field === field) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.field = field; currentSort.direction = 'asc';
        }
        loadItems(1);
    }));

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            if (confirm('¿Estás seguro de eliminar este producto? Se eliminarán también todas sus imágenes y archivos.')) {
                fetch(`/admin/productos/${btn.dataset.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => { if (data.success) { loadItems(1); showAlert(data.message, 'success'); } })
                .catch(() => showAlert('Error al eliminar', 'error'));
            }
        }
        const paginationLink = e.target.closest('#pagination a');
        if (paginationLink) {
            e.preventDefault();
            const page = paginationLink.dataset.page || new URL(paginationLink.href).searchParams.get('page') || 1;
            loadItems(parseInt(page));
        }
    });

    function loadItems(page = 1) {
        const params = new URLSearchParams({
            page,
            search: searchInput.value,
            sortField: currentSort.field,
            sortDirection: currentSort.direction,
            tipo_id: tipoFilter.value,
            visible: visibleFilter.value
        });
        const url = `{{ route('productos.index') }}?${params.toString()}`;
        fetch(url, { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            document.getElementById('productosTable').innerHTML = data.html;
            document.getElementById('pagination').innerHTML     = data.pagination;
            document.getElementById('currentPage').textContent  = data.currentPage;
            document.getElementById('lastPage').textContent     = data.lastPage;
            document.getElementById('firstItem').textContent    = data.firstItem;
            document.getElementById('lastItem').textContent     = data.lastItem;
            document.getElementById('totalItems').textContent   = data.total;
            document.getElementById('totalCount').textContent   = data.total;
            if (hayFiltrosActivos()) {
                searchResults.classList.remove('hidden');
                searchResults.textContent = `${data.total} producto(s) encontrado(s)`;
            } else {
                searchResults.classList.add('hidden');
                searchResults.textContent = '';
            }
        })
        .catch(() => showAlert('Error al cargar los datos', 'error'));
    }

    function hayFiltrosActivos() {
        return searchInput.value || tipoFilter.value || visibleFilter.value;
    }

    function showAlert(message, type = 'info') {
        const cls = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
        document.getElementById('alertContainer').innerHTML = `<div class="px-4 py-3 rounded-md ${cls} border text-sm">${message}</div>`;
        setTimeout(() => { document.getElementById('alertContainer').innerHTML = ''; }, 5000);
    }

    // ── Export dropdown ───────────────────────────────────────────────────────
    const exportBtn      = document.getElementById('exportDropdownBtn');
    const exportDropdown = document.getElementById('exportDropdown');

    exportBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        exportDropdown.classList.toggle('hidden');
    });
    document.addEventListener('click', () => exportDropdown.classList.add('hidden'));

    // ── Import slide-over ─────────────────────────────────────────────────────
    const overlay       = document.getElementById('importOverlay');
    const panel         = document.getElementById('importPanel');
    const openBtn       = document.getElementById('openImportBtn');
    const closeBtn      = document.getElementById('closeImportBtn');
    const cancelBtn     = document.getElementById('cancelImportBtn');
    const submitBtn     = document.getElementById('submitImportBtn');
    const submitUpdateOnlyBtn = document.getElementById('submitImportUpdateOnlyBtn');
    const fileInput     = document.getElementById('importFile');
    const dropZone      = document.getElementById('dropZone');
    const dropIdle      = document.getElementById('dropZoneIdle');
    const dropFile      = document.getElementById('dropZoneFile');
    const dropFileName  = document.getElementById('dropZoneFileName');
    const removeFileBtn = document.getElementById('removeFileBtn');
    const importError   = document.getElementById('importError');
    const importSpinner = document.getElementById('importSpinner');
    const importUpdateOnlySpinner = document.getElementById('importUpdateOnlySpinner');
    const importBtnText = document.getElementById('importBtnText');
    const importUpdateOnlyBtnText = document.getElementById('importUpdateOnlyBtnText');

    function openPanel() {
        overlay.classList.remove('hidden');
        panel.classList.remove('translate-x-full');
        document.body.style.overflow = 'hidden';
    }

    function closePanel() {
        panel.classList.add('translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
        resetForm();
    }

    function resetForm() {
        fileInput.value = '';
        dropIdle.classList.remove('hidden');
        dropFile.classList.add('hidden');
        dropZone.classList.remove('border-amber-400', 'bg-amber-50/60');
        importError.classList.add('hidden');
        importError.textContent = '';
        setLoading(false);
    }

    function setFileSelected(file) {
        dropFileName.textContent = file.name;
        dropIdle.classList.add('hidden');
        dropFile.classList.remove('hidden');
        dropZone.classList.add('border-amber-400', 'bg-amber-50/60');
        importError.classList.add('hidden');
    }

    function setLoading(loading) {
        submitBtn.disabled = loading;
        importSpinner.classList.toggle('hidden', !loading);
        importBtnText.textContent = loading ? 'Importando…' : 'Importar';
    }

    openBtn.addEventListener('click', openPanel);
    closeBtn.addEventListener('click', closePanel);
    cancelBtn.addEventListener('click', closePanel);
    overlay.addEventListener('click', closePanel);

    // Click dropzone → open file picker
    dropZone.addEventListener('click', (e) => {
        if (e.target !== removeFileBtn) fileInput.click();
    });

    // File via input
    fileInput.addEventListener('change', () => {
        if (fileInput.files[0]) setFileSelected(fileInput.files[0]);
    });

    // Remove file
    removeFileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        dropIdle.classList.remove('hidden');
        dropFile.classList.add('hidden');
        dropZone.classList.remove('border-amber-400', 'bg-amber-50/60');
    });

    // Drag & drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-amber-400', 'bg-amber-50/60');
    });
    dropZone.addEventListener('dragleave', () => {
        if (!fileInput.files[0]) dropZone.classList.remove('border-amber-400', 'bg-amber-50/60');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            setFileSelected(file);
        }
    });

    // Submit
    submitBtn.addEventListener('click', () => {
        if (!fileInput.files[0]) {
            importError.textContent = 'Seleccioná un archivo Excel primero.';
            importError.classList.remove('hidden');
            return;
        }

        setLoading(true);
        importError.classList.add('hidden');

        const formData = new FormData();
        formData.append('archivo', fileInput.files[0]);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('{{ route('productos.importar') }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(data => {
            setLoading(false);
            if (data.success) {
                closePanel();
                loadItems(1);
                showAlert(data.message, 'success');
            } else {
                importError.textContent = data.message;
                importError.classList.remove('hidden');
            }
        })
        .catch(() => {
            setLoading(false);
            importError.textContent = 'Error inesperado. Intentá nuevamente.';
            importError.classList.remove('hidden');
        });
    });

    // Cerrar con Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !panel.classList.contains('translate-x-full')) closePanel();
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const submitUpdateOnlyBtn = document.getElementById('submitImportUpdateOnlyBtn');
    const importUpdateOnlySpinner = document.getElementById('importUpdateOnlySpinner');
    const importUpdateOnlyBtnText = document.getElementById('importUpdateOnlyBtnText');
    const fileInput = document.getElementById('importFile');
    const importError = document.getElementById('importError');
    const panel = document.getElementById('importPanel');
    const overlay = document.getElementById('importOverlay');
    const dropIdle = document.getElementById('dropZoneIdle');
    const dropFile = document.getElementById('dropZoneFile');
    const dropZone = document.getElementById('dropZone');
    const importBtnText = document.getElementById('importBtnText');
    const importSpinner = document.getElementById('importSpinner');

    if (!submitUpdateOnlyBtn) {
        return;
    }

    function showAlert(message, type = 'success') {
        const cls = type === 'success'
            ? 'bg-green-50 border-green-200 text-green-700'
            : 'bg-red-50 border-red-200 text-red-700';

        document.getElementById('alertContainer').innerHTML =
            `<div class="px-4 py-3 rounded-md ${cls} border text-sm">${message}</div>`;

        setTimeout(() => {
            document.getElementById('alertContainer').innerHTML = '';
        }, 5000);
    }

    function setUpdateOnlyLoading(loading) {
        submitUpdateOnlyBtn.disabled = loading;
        importUpdateOnlySpinner.classList.toggle('hidden', !loading);
        importUpdateOnlyBtnText.textContent = loading ? 'Importando...' : 'Solo actualizar';
    }

    function normalizeUpsertButton() {
        if (importBtnText) {
            importBtnText.textContent = 'Crear y actualizar';
        }

        if (importSpinner) {
            importSpinner.classList.add('hidden');
        }
    }

    function closePanelManually() {
        panel.classList.add('translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
        fileInput.value = '';
        dropIdle.classList.remove('hidden');
        dropFile.classList.add('hidden');
        dropZone.classList.remove('border-amber-400', 'bg-amber-50/60');
        importError.classList.add('hidden');
        importError.textContent = '';
        setUpdateOnlyLoading(false);
    }

    const pendingAlert = sessionStorage.getItem('productos-import-alert');
    if (pendingAlert) {
        sessionStorage.removeItem('productos-import-alert');
        const alertData = JSON.parse(pendingAlert);
        showAlert(alertData.message, alertData.type);
    }

    normalizeUpsertButton();
    new MutationObserver(normalizeUpsertButton).observe(panel, {
        attributes: true,
        attributeFilter: ['class'],
    });

    submitUpdateOnlyBtn.addEventListener('click', () => {
        if (!fileInput.files[0]) {
            importError.textContent = 'Selecciona un archivo Excel primero.';
            importError.classList.remove('hidden');
            return;
        }

        importError.classList.add('hidden');
        setUpdateOnlyLoading(true);

        const formData = new FormData();
        formData.append('archivo', fileInput.files[0]);
        formData.append('modo', 'solo_actualizar');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('{{ route('productos.importar') }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                sessionStorage.setItem('productos-import-alert', JSON.stringify({
                    message: data.message,
                    type: 'success',
                }));
                closePanelManually();
                window.location.reload();
            } else {
                setUpdateOnlyLoading(false);
                importError.textContent = data.message;
                importError.classList.remove('hidden');
            }
        })
        .catch(() => {
            setUpdateOnlyLoading(false);
            importError.textContent = 'Error inesperado. Intenta nuevamente.';
            importError.classList.remove('hidden');
        });
    });
});
</script>
@endsection
