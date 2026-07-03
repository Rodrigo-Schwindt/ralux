@if($productoTipos->hasPages())
    <div class="flex items-center justify-between pt-5 border-t border-slate-200">
        @if (!$productoTipos->onFirstPage())
            <a href="{{ $productoTipos->previousPageUrl() }}" data-page="{{ $productoTipos->currentPage() - 1 }}"
               class="px-4 py-2 border border-slate-300 rounded-md text-slate-600 text-sm hover:bg-slate-50 transition">
                ← Anterior
            </a>
        @else
            <button disabled class="px-4 py-2 border border-slate-300 rounded-md text-slate-600 text-sm opacity-50 cursor-not-allowed">
                ← Anterior
            </button>
        @endif

        <span class="text-sm text-slate-500">
            {{ $productoTipos->firstItem() }}–{{ $productoTipos->lastItem() }} de {{ $productoTipos->total() }}
        </span>

        @if ($productoTipos->hasMorePages())
            <a href="{{ $productoTipos->nextPageUrl() }}" data-page="{{ $productoTipos->currentPage() + 1 }}"
               class="px-4 py-2 border border-slate-300 rounded-md text-slate-600 text-sm hover:bg-slate-50 transition">
                Siguiente →
            </a>
        @else
            <button disabled class="px-4 py-2 border border-slate-300 rounded-md text-slate-600 text-sm opacity-50 cursor-not-allowed">
                Siguiente →
            </button>
        @endif
    </div>

    <div class="flex justify-center gap-1 mt-4 flex-wrap">
        @php
            $current = $productoTipos->currentPage();
            $last    = $productoTipos->lastPage();
            $pages   = collect(range(1, $last))
                ->filter(fn($p) => $p === 1 || $p === $last || abs($p - $current) <= 2)
                ->values()->all();
        @endphp
        @php $prev = null; @endphp
        @foreach ($pages as $page)
            @if ($prev !== null && $page - $prev > 1)
                <span class="px-2 py-1 text-slate-400 text-sm select-none">…</span>
            @endif
            <a href="{{ $productoTipos->url($page) }}" data-page="{{ $page }}"
               class="pagination-link px-3 py-1 border rounded-md text-sm {{ $page == $current ? 'bg-blue-600 text-white border-blue-600' : 'border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                {{ $page }}
            </a>
            @php $prev = $page; @endphp
        @endforeach
    </div>
@endif
