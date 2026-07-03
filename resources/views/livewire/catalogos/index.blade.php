@extends('layouts.admin')

@section('content')
<div class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-8 animate-fadeIn">
    

    <div class=" p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <h2 class="text-2xl font-semibold text-slate-900">Catálogos Existentes (<span id="totalCount">{{ $catalogos->total() }}</span>)</h2>
            <a href="{{ route('catalogos.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer active:scale-[.98]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Crear Catálogo
            </a>
        </div>

        <div id="alertContainer"></div>

        <div class="bg-white border border-slate-200 rounded-md p-4 shadow-sm">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text"
                       id="searchInput"
                       placeholder="Buscar por título u orden..."
                       class="w-full pl-10 pr-10 py-2.5 border border-slate-300 rounded-md bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm transition">
                <button id="clearSearch"
                        type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <p id="searchResults" class="mt-2 text-sm text-slate-600 hidden"></p>
        </div>

        <p class="text-sm text-slate-500 text-center my-4">
            Página <span id="currentPage">{{ $catalogos->currentPage() }}</span> de <span id="lastPage">{{ $catalogos->lastPage() }}</span> —
            Mostrando <span id="firstItem">{{ $catalogos->firstItem() }}</span>–<span id="lastItem">{{ $catalogos->lastItem() }}</span> de <span id="totalItems">{{ $catalogos->total() }}</span> registros
        </p>

        <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
            <table class="w-full text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="orden">Orden</th>
                        <th class="px-4 py-3 font-medium cursor-pointer text-start hover:text-slate-900 sort-header" data-sort="title">Título</th>
                        <th class="px-4 py-3 text-center font-medium">Imagen 1</th>
                        <th class="px-4 py-3 text-center font-medium">Imagen 2</th>
                        <th class="px-4 py-3 text-center font-medium">PDF</th>
                        <th class="px-4 py-3 text-center font-medium">Visible</th>
                        <th class="px-4 py-3 text-center font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody id="catalogosTable" class="divide-y divide-slate-200">
                    @include('livewire.catalogos.partials.table', ['catalogos' => $catalogos])
                </tbody>
            </table>
        </div>

        <div id="pagination">
            @include('livewire.catalogos.partials.pagination', ['catalogos' => $catalogos])
        </div>
    </div>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-banner_image');
    const preview = document.getElementById('preview-banner_image');
    const placeholder = document.getElementById('placeholder-banner_image');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        clearSearch.classList.toggle('hidden', !this.value);
        searchResults.classList.toggle('hidden', !this.value);

        searchTimeout = setTimeout(loadCatalogos, 300);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('hidden');
        searchResults.classList.add('hidden');
        loadCatalogos();
    });

    document.querySelectorAll('.sort-header').forEach(header => {
        header.addEventListener('click', function() {
            const sortField = this.dataset.sort;
            loadCatalogos(1, sortField);
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.dataset.id;

            if (confirm('¿Estás seguro de eliminar este catálogo?')) {
                fetch(`/admin/catalogos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadCatalogos();
                        showAlert(data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al eliminar el catálogo', 'error');
                });
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const page = new URL(url).searchParams.get('page');
            loadCatalogos(page);
        }
    });

    function loadCatalogos(page = 1, sortField = 'orden', sortDirection = 'asc') {
        const search = searchInput.value;
        const url = `{{ route('catalogos.index') }}?page=${page}&search=${encodeURIComponent(search)}&sortField=${sortField}&sortDirection=${sortDirection}`;

        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            document.getElementById('catalogosTable').innerHTML = data.html;
            document.getElementById('pagination').innerHTML = data.pagination;
            
            if (search) {
                searchResults.textContent = `Resultados para "${search}"`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al cargar los catálogos', 'error');
        });
    }

    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
        
        alertContainer.innerHTML = `
            <div class="px-4 py-3 rounded-md ${bgColor} border text-sm">
                ${message}
            </div>
        `;

        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 4000);
    }

    window.deleteImage = function() {
        if (confirm('¿Estás seguro de eliminar la imagen del banner?')) {
            fetch('{{ route('catalogos.delete-banner-image') }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error al eliminar la imagen', 'error');
            });
        }
    }
});
</script>
@endsection