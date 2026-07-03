@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-2xl font-semibold text-slate-900">Pedidos ({{ $pedidos->total() }})</h2>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Exportar todo --}}
                <a
                    href="{{ route('admin.pedidos.exportar-todo') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-md hover:bg-emerald-700 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exportar todo
                </a>

                <a
                    href="{{ route('admin.pedidos.exportar-filtrado', request()->only(['buscar','estado','forma_pago','fecha_desde','fecha_hasta'])) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-300 rounded-md hover:bg-emerald-100 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exportar filtrado
                </a>

                <button
                    id="btn-eliminar-seleccionados"
                    type="button"
                    onclick="eliminarSeleccionados()"
                    disabled
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition disabled:opacity-40 disabled:cursor-not-allowed"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m3-3h4a1 1 0 011 1v2H9V5a1 1 0 011-1z"/>
                    </svg>
                    Eliminar seleccionados
                </button>

                <div class="relative">
                <form method="GET" action="{{ route('admin.pedidos') }}" id="form-buscar">
                    @foreach(request()->except('buscar', 'page') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <input
                        type="text"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Buscar por nº pedido o cliente..."
                        class="w-full sm:w-72 pl-4 pr-10 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369]"
                    >
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
                </div>{{-- /relative --}}
            </div>{{-- /flex items-center gap-2 --}}
        </div>

        <form method="GET" action="{{ route('admin.pedidos') }}" class="flex flex-wrap items-end justify-end gap-3 p-4 bg-slate-50 border border-slate-200 rounded-md">
            @if(request('buscar'))
                <input type="hidden" name="buscar" value="{{ request('buscar') }}">
            @endif

            <div class="flex flex-col gap-1">
                <label for="filtro-estado" class="text-xs text-slate-500 font-medium uppercase">Estado</label>
                <select
                    id="filtro-estado"
                    name="estado"
                    onchange="this.form.submit()"
                    class="px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369] bg-white"
                >
                    <option value="todos" {{ request('estado', 'todos') === 'todos' ? 'selected' : '' }}>Todos</option>
                    <option value="pendientes" {{ request('estado') === 'pendientes' ? 'selected' : '' }}>Pendientes</option>
                    <option value="entregados" {{ request('estado') === 'entregados' ? 'selected' : '' }}>Entregados</option>
                    <option value="cancelados" {{ request('estado') === 'cancelados' ? 'selected' : '' }}>Cancelados</option>
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label for="filtro-forma-pago" class="text-xs text-slate-500 font-medium uppercase">Forma de pago</label>
                <select
                    id="filtro-forma-pago"
                    name="forma_pago"
                    onchange="this.form.submit()"
                    class="px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369] bg-white"
                >
                    <option value="todos" {{ request('forma_pago', 'todos') === 'todos' ? 'selected' : '' }}>Todas</option>
                    <option value="contado" {{ request('forma_pago') === 'contado' ? 'selected' : '' }}>Contado</option>
                    <option value="transferencia" {{ request('forma_pago') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                    <option value="cuenta_corriente" {{ request('forma_pago') === 'cuenta_corriente' ? 'selected' : '' }}>Cuenta corriente</option>
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label for="filtro-fecha-desde" class="text-xs text-slate-500 font-medium uppercase">Fecha desde</label>
                <input
                    id="filtro-fecha-desde"
                    type="date"
                    name="fecha_desde"
                    value="{{ request('fecha_desde') }}"
                    class="px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369] bg-white"
                >
            </div>

            <div class="flex flex-col gap-1">
                <label for="filtro-fecha-hasta" class="text-xs text-slate-500 font-medium uppercase">Fecha hasta</label>
                <input
                    id="filtro-fecha-hasta"
                    type="date"
                    name="fecha_hasta"
                    value="{{ request('fecha_hasta') }}"
                    class="px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369] bg-white"
                >
            </div>

            <button type="submit" class="px-4 py-2 bg-[#AD0369] text-white rounded-md text-sm hover:bg-[#AD0369]/90 transition">
                Filtrar
            </button>

            @if(request()->hasAny(['buscar', 'estado', 'forma_pago', 'fecha_desde', 'fecha_hasta']))
            <a href="{{ route('admin.pedidos') }}" class="px-3 py-2 text-sm text-slate-500 hover:text-slate-700 border border-slate-300 rounded-md bg-white hover:bg-slate-50 transition">
                Limpiar
            </a>
            @endif
        </form>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 rounded-md bg-green-50 border border-green-200 text-green-700 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Tabla --}}
    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-center font-medium w-10">
                        <input
                            id="seleccionar-todos"
                            type="checkbox"
                            onchange="toggleSeleccionTodos(this)"
                            class="rounded border-slate-300 text-[#AD0369] focus:ring-[#AD0369]"
                            aria-label="Seleccionar todos los pedidos"
                        >
                    </th>
                    <th class="px-4 py-3 text-left font-medium">Nº Pedido</th>
                    <th class="px-4 py-3 text-left font-medium">Fecha compra</th>
                    <th class="px-4 py-3 text-left font-medium">Cliente</th>
                    <th class="px-4 py-3 text-left font-medium">Forma de pago</th>
                    <th class="px-4 py-3 text-center font-medium">Fecha entrega</th>
                    <th class="px-4 py-3 text-right font-medium">Total</th>
                    <th class="px-4 py-3 text-center font-medium">Estado</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pedidos as $pedido)
                <tr id="row-{{ $pedido->id }}" class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 text-center">
                        <input
                            type="checkbox"
                            value="{{ $pedido->id }}"
                            onchange="actualizarBotonEliminarSeleccionados()"
                            class="pedido-checkbox rounded border-slate-300 text-[#AD0369] focus:ring-[#AD0369]"
                            aria-label="Seleccionar pedido {{ $pedido->numero_pedido }}"
                        >
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">{{ $pedido->numero_pedido }}</td>
                    <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $pedido->fecha_compra->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $pedido->cliente->nombre }}</div>
                        <div class="text-xs text-slate-400">{{ $pedido->cliente->email }}</div>
                    </td>
                    <td class="px-4 py-3 text-slate-500 capitalize whitespace-nowrap">{{ str_replace('_', ' ', $pedido->forma_pago) }}</td>
                    <td class="px-4 py-3 text-center">
                        <input
                            type="date"
                            value="{{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('Y-m-d') : '' }}"
                            data-id="{{ $pedido->id }}"
                            data-url="{{ route('admin.pedidos.fecha', $pedido->id) }}"
                            onchange="guardarFecha(this)"
                            class="text-sm border border-slate-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[#AD0369]"
                        >
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-slate-900 whitespace-nowrap">
                        ${{ number_format($pedido->total, 2, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        <span id="badge-{{ $pedido->id }}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            {{ $pedido->cancelado ? 'bg-red-100 text-red-700' : ($pedido->entregado ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $pedido->cancelado ? 'Cancelado' : ($pedido->entregado ? 'Entregado' : 'Pendiente') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a
                                href="{{ route('admin.pedidos.show', $pedido->id) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Ver
                            </a>
                            @if(!$pedido->cancelado)
                            <button
                                id="btn-toggle-{{ $pedido->id }}"
                                data-id="{{ $pedido->id }}"
                                data-url="{{ route('admin.pedidos.toggle', $pedido->id) }}"
                                data-entregado="{{ $pedido->entregado ? '1' : '0' }}"
                                onclick="toggleEntregado(this)"
                                type="button"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded transition
                                    {{ $pedido->entregado ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-green-600 bg-green-50 hover:bg-green-100' }}"
                            >
                                @if($pedido->entregado)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Pendiente
                                @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Entregado
                                @endif
                            </button>
                            @endif
                            <button
                                id="btn-cancelar-{{ $pedido->id }}"
                                data-id="{{ $pedido->id }}"
                                data-url="{{ route('admin.pedidos.cancelar', $pedido->id) }}"
                                data-cancelado="{{ $pedido->cancelado ? '1' : '0' }}"
                                onclick="toggleCancelado(this)"
                                type="button"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded transition
                                    {{ $pedido->cancelado ? 'text-slate-600 bg-slate-100 hover:bg-slate-200' : 'text-red-600 bg-red-50 hover:bg-red-100' }}"
                            >
                                @if($pedido->cancelado)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l16 16M4 20L20 4"/></svg>
                                    Restaurar
                                @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Cancelar
                                @endif
                            </button>
                            <button
                                data-id="{{ $pedido->id }}"
                                data-url="{{ route('admin.pedidos.destroy', $pedido->id) }}"
                                onclick="eliminarPedido(this)"
                                type="button"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200 transition"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m3-3h4a1 1 0 011 1v2H9V5a1 1 0 011-1z"/>
                                </svg>
                                Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-slate-400">No se encontraron pedidos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($pedidos->lastPage() > 1)
    @php
        $current  = $pedidos->currentPage();
        $last     = $pedidos->lastPage();
        $window   = 2;
        $start    = max(2, $current - $window);
        $end      = min($last - 1, $current + $window);
    @endphp
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm text-slate-600">

        <p class="text-xs text-slate-400">
            Mostrando {{ $pedidos->firstItem() }}–{{ $pedidos->lastItem() }} de {{ $pedidos->total() }} pedidos
        </p>

        <nav class="flex items-center gap-1 flex-wrap" aria-label="Paginación">

            {{-- Anterior --}}
            @if($pedidos->onFirstPage())
                <span class="px-3 py-1.5 rounded border border-slate-200 text-slate-300 cursor-not-allowed select-none">‹ Anterior</span>
            @else
                <a href="{{ $pedidos->previousPageUrl() }}" class="px-3 py-1.5 rounded border border-slate-300 hover:bg-slate-50 transition">‹ Anterior</a>
            @endif

            {{-- Página 1 --}}
            <a href="{{ $pedidos->url(1) }}"
               class="px-3 py-1.5 rounded border transition {{ $current === 1 ? 'bg-[#AD0369] text-white border-[#AD0369]' : 'border-slate-300 hover:bg-slate-50' }}">
                1
            </a>

            {{-- Elipsis izquierda --}}
            @if($start > 2)
                <span class="px-2 py-1.5 text-slate-400 select-none">…</span>
            @endif

            {{-- Rango central --}}
            @for($p = $start; $p <= $end; $p++)
                <a href="{{ $pedidos->url($p) }}"
                   class="px-3 py-1.5 rounded border transition {{ $current === $p ? 'bg-[#AD0369] text-white border-[#AD0369]' : 'border-slate-300 hover:bg-slate-50' }}">
                    {{ $p }}
                </a>
            @endfor

            {{-- Elipsis derecha --}}
            @if($end < $last - 1)
                <span class="px-2 py-1.5 text-slate-400 select-none">…</span>
            @endif

            {{-- Última página --}}
            @if($last > 1)
                <a href="{{ $pedidos->url($last) }}"
                   class="px-3 py-1.5 rounded border transition {{ $current === $last ? 'bg-[#AD0369] text-white border-[#AD0369]' : 'border-slate-300 hover:bg-slate-50' }}">
                    {{ $last }}
                </a>
            @endif

            {{-- Siguiente --}}
            @if($pedidos->hasMorePages())
                <a href="{{ $pedidos->nextPageUrl() }}" class="px-3 py-1.5 rounded border border-slate-300 hover:bg-slate-50 transition">Siguiente ›</a>
            @else
                <span class="px-3 py-1.5 rounded border border-slate-200 text-slate-300 cursor-not-allowed select-none">Siguiente ›</span>
            @endif

        </nav>
    </div>
    @endif

</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const DELETE_MULTIPLE_URL = @json(route('admin.pedidos.destroy-multiple'));

function pedidosSeleccionados() {
    return Array.from(document.querySelectorAll('.pedido-checkbox:checked')).map(checkbox => checkbox.value);
}

function actualizarBotonEliminarSeleccionados() {
    const seleccionados = pedidosSeleccionados();
    const btn = document.getElementById('btn-eliminar-seleccionados');
    const selectAll = document.getElementById('seleccionar-todos');
    const checkboxes = Array.from(document.querySelectorAll('.pedido-checkbox'));

    if (btn) {
        btn.disabled = seleccionados.length === 0;
        btn.innerHTML = seleccionados.length
            ? `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m3-3h4a1 1 0 011 1v2H9V5a1 1 0 011-1z"/></svg> Eliminar (${seleccionados.length})`
            : `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m3-3h4a1 1 0 011 1v2H9V5a1 1 0 011-1z"/></svg> Eliminar seleccionados`;
    }

    if (selectAll) {
        selectAll.checked = checkboxes.length > 0 && seleccionados.length === checkboxes.length;
        selectAll.indeterminate = seleccionados.length > 0 && seleccionados.length < checkboxes.length;
    }
}

function toggleSeleccionTodos(input) {
    document.querySelectorAll('.pedido-checkbox').forEach(checkbox => {
        checkbox.checked = input.checked;
    });
    actualizarBotonEliminarSeleccionados();
}

function eliminarSeleccionados() {
    const ids = pedidosSeleccionados();

    if (!ids.length) {
        return;
    }

    if (!confirm(`Se eliminarán ${ids.length} pedido(s). Esta acción no se puede deshacer. ¿Continuar?`)) {
        return;
    }

    fetch(DELETE_MULTIPLE_URL, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ ids })
    })
    .then(r => {
        if (!r.ok) throw new Error();
        return r.json();
    })
    .then(() => window.location.reload())
    .catch(() => alert('Error al eliminar los pedidos seleccionados.'));
}

function eliminarPedido(btn) {
    if (!confirm('Se eliminará este pedido. Esta acción no se puede deshacer. ¿Continuar?')) {
        return;
    }

    fetch(btn.dataset.url, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({})
    })
    .then(r => {
        if (!r.ok) throw new Error();
        return r.json();
    })
    .then(() => window.location.reload())
    .catch(() => alert('Error al eliminar el pedido.'));
}

function guardarFecha(input) {
    fetch(input.dataset.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ fecha_entrega: input.value })
    }).catch(() => alert('Error al guardar la fecha.'));
}

function toggleEntregado(btn) {
    fetch(btn.dataset.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        const id = btn.dataset.id;
        const badge = document.getElementById('badge-' + id);
        const dateInput = document.querySelector('input[type="date"][data-id="' + id + '"]');
        if (data.entregado) {
            badge.textContent = 'Entregado';
            badge.className = 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700';
            btn.dataset.entregado = '1';
            btn.className = 'inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded transition text-amber-600 bg-amber-50 hover:bg-amber-100';
            btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Pendiente`;
            if (dateInput && data.fecha_entrega) dateInput.value = data.fecha_entrega;
        } else {
            badge.textContent = 'Pendiente';
            badge.className = 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700';
            btn.dataset.entregado = '0';
            btn.className = 'inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded transition text-green-600 bg-green-50 hover:bg-green-100';
            btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Entregado`;
        }
    })
    .catch(() => alert('Error al actualizar el estado.'));
}

function toggleCancelado(btn) {
    fetch(btn.dataset.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        const id = btn.dataset.id;
        const badge = document.getElementById('badge-' + id);
        const toggleBtn = document.getElementById('btn-toggle-' + id);
        if (data.cancelado) {
            badge.textContent = 'Cancelado';
            badge.className = 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700';
            btn.dataset.cancelado = '1';
            btn.className = 'inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded transition text-slate-600 bg-slate-100 hover:bg-slate-200';
            btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l16 16M4 20L20 4"/></svg> Restaurar`;
            if (toggleBtn) toggleBtn.style.display = 'none';
        } else {
            badge.textContent = 'Pendiente';
            badge.className = 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700';
            btn.dataset.cancelado = '0';
            btn.className = 'inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded transition text-red-600 bg-red-50 hover:bg-red-100';
            btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Cancelar`;
            if (toggleBtn) toggleBtn.style.display = '';
        }
    })
    .catch(() => alert('Error al actualizar el estado.'));
}
</script>
@endsection
