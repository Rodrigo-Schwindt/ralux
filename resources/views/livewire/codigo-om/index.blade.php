@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Códigos OM (<span id="totalCount">{{ $codigos->total() }}</span>)</h2>
        <a href="{{ route('codigo-om.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer active:scale-[.98]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Crear Código OM
        </a>
    </div>

    <div id="alertContainer"></div>

    <div class="bg-white border border-slate-200 rounded-md p-4 shadow-sm">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <input type="text" id="searchInput" placeholder="Buscar por código, marca o código Ralux..."
                   class="w-full pl-10 pr-10 py-2.5 border border-slate-300 rounded-md bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm transition">
            <button id="clearSearch" type="button"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 hidden">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <p id="searchResults" class="mt-2 text-sm text-slate-600 hidden"></p>
    </div>

    <p class="text-sm text-slate-500 text-center">
        Página <span id="currentPage">{{ $codigos->currentPage() }}</span> de <span id="lastPage">{{ $codigos->lastPage() }}</span> —
        Mostrando <span id="firstItem">{{ $codigos->firstItem() ?? 0 }}</span>–<span id="lastItem">{{ $codigos->lastItem() ?? 0 }}</span> de <span id="totalItems">{{ $codigos->total() }}</span> registros
    </p>

    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 font-medium text-start cursor-pointer hover:text-slate-900 sort-header" data-sort="producto_id">Producto Ralux</th>
                    <th class="px-4 py-3 font-medium text-start cursor-pointer hover:text-slate-900 sort-header" data-sort="codigo">Código OM</th>
                    <th class="px-4 py-3 font-medium text-start cursor-pointer hover:text-slate-900 sort-header" data-sort="marca_id">Marca</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody id="codigosTable" class="divide-y divide-slate-200">
                @include('livewire.codigo-om.partials.table', ['codigos' => $codigos])
            </tbody>
        </table>
    </div>

    <div id="pagination">
        @include('livewire.codigo-om.partials.pagination', ['codigos' => $codigos])
    </div>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput   = document.getElementById('searchInput');
    const clearSearch   = document.getElementById('clearSearch');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;
    let currentSort = { field: 'codigo', direction: 'asc' };

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        clearSearch.classList.toggle('hidden', !this.value);
        searchResults.classList.toggle('hidden', !this.value);
        searchTimeout = setTimeout(() => loadItems(1), 300);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = ''; this.classList.add('hidden'); searchResults.classList.add('hidden'); loadItems(1);
    });

    document.querySelectorAll('.sort-header').forEach(h => h.addEventListener('click', function() {
        const field = this.dataset.sort;
        if (currentSort.field === field) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.field = field;
            currentSort.direction = 'asc';
        }
        loadItems(1);
    }));

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            if (confirm('¿Estás seguro de eliminar este código OM?')) {
                fetch(`/admin/codigo-om/${btn.dataset.id}`, {
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
        const url = `{{ route('codigo-om.index') }}?page=${page}&search=${encodeURIComponent(searchInput.value)}&sortField=${currentSort.field}&sortDirection=${currentSort.direction}`;
        fetch(url, { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => { if (!r.ok) throw new Error(); return r.json(); })
        .then(data => {
            document.getElementById('codigosTable').innerHTML  = data.html;
            document.getElementById('pagination').innerHTML    = data.pagination;
            document.getElementById('currentPage').textContent = data.currentPage;
            document.getElementById('lastPage').textContent    = data.lastPage;
            document.getElementById('firstItem').textContent   = data.firstItem;
            document.getElementById('lastItem').textContent    = data.lastItem;
            document.getElementById('totalItems').textContent  = data.total;
            document.getElementById('totalCount').textContent  = data.total;
            if (searchInput.value) {
                searchResults.classList.remove('hidden');
                searchResults.textContent = `Resultados para "${searchInput.value}": ${data.total} registro(s)`;
            }
        })
        .catch(() => showAlert('Error al cargar los datos', 'error'));
    }

    function showAlert(message, type = 'info') {
        const cls = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
        document.getElementById('alertContainer').innerHTML = `<div class="px-4 py-3 rounded-md ${cls} border text-sm">${message}</div>`;
        setTimeout(() => { document.getElementById('alertContainer').innerHTML = ''; }, 4000);
    }
});
</script>
@endsection
