{{-- resources/views/admin/catalogos/partials/pagination.blade.php --}}
@if($catalogos->hasPages())
    <div class="flex items-center justify-between pt-5 border-t border-slate-200">
        <a href="{{ $catalogos->previousPageUrl() }}"
           class="px-4 py-2 border border-slate-300 rounded-md text-slate-600 text-sm hover:bg-slate-50 transition {{ !$catalogos->onFirstPage() ? '' : 'opacity-50 pointer-events-none' }}">
            ← Anterior
        </a>

        <span class="text-sm text-slate-500">
            {{ $catalogos->firstItem() }}–{{ $catalogos->lastItem() }} de {{ $catalogos->total() }}
        </span>

        <a href="{{ $catalogos->nextPageUrl() }}"
           class="px-4 py-2 border border-slate-300 rounded-md text-slate-600 text-sm hover:bg-slate-50 transition {{ $catalogos->hasMorePages() ? '' : 'opacity-50 pointer-events-none' }}">
            Siguiente →
        </a>
    </div>

    <div class="flex justify-center gap-1 mt-4 flex-wrap">
        @foreach ($catalogos->getUrlRange(1, $catalogos->lastPage()) as $page => $url)
            <a href="{{ $url }}" 
               class="pagination-link px-3 py-1 border rounded-md text-sm {{ $page == $catalogos->currentPage() ? 'bg-blue-600 text-white border-blue-600' : 'border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                {{ $page }}
            </a>
        @endforeach
    </div>
@endif