@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Pedido #{{ $pedido->numero_pedido }}</h2>
            <p class="text-sm text-slate-500 mt-1">Realizado el {{ $pedido->fecha_compra->format('d/m/Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.pedidos.factura', $pedido->id) }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Descargar Factura
            </a>
            <a
                href="{{ route('admin.pedidos') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-md hover:bg-slate-50 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 rounded-md bg-green-50 border border-green-200 text-green-700 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Estado + Fecha entrega --}}
    <div class="bg-white border border-slate-200 rounded-md p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4 flex-wrap">
            <div>
                <p class="text-xs text-slate-500 uppercase font-medium mb-1">Estado</p>
                <span id="badge-estado" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                    {{ $pedido->cancelado ? 'bg-red-100 text-red-700' : ($pedido->entregado ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ $pedido->cancelado ? 'Cancelado' : ($pedido->entregado ? 'Entregado' : 'Pendiente') }}
                    @if(!$pedido->cancelado && $pedido->entregado && $pedido->fecha_entregado)
                        <span class="ml-1 font-normal text-green-600">· {{ $pedido->fecha_entregado->format('d/m/Y') }}</span>
                    @endif
                </span>
            </div>

            @if(!$pedido->cancelado)
            <button
                id="btn-toggle"
                data-url="{{ route('admin.pedidos.toggle', $pedido->id) }}"
                data-entregado="{{ $pedido->entregado ? '1' : '0' }}"
                onclick="toggleEstado(this)"
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition
                    {{ $pedido->entregado ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : 'bg-[#AD0369] text-white hover:bg-[#AD0369]/90' }}"
            >
                @if($pedido->entregado)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Marcar como pendiente
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Marcar como entregado
                @endif
            </button>
            @endif

            <button
                id="btn-cancelar"
                data-url="{{ route('admin.pedidos.cancelar', $pedido->id) }}"
                data-cancelado="{{ $pedido->cancelado ? '1' : '0' }}"
                onclick="toggleCancelado(this)"
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition
                    {{ $pedido->cancelado ? 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-300' : 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-200' }}"
            >
                @if($pedido->cancelado)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l16 16M4 20L20 4"/></svg>
                    Restaurar pedido
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Cancelar pedido
                @endif
            </button>
        </div>

        <div>
            <p class="text-xs text-slate-500 uppercase font-medium mb-1">Fecha estimada de entrega</p>
            <input
                type="date"
                id="fecha-entrega"
                value="{{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('Y-m-d') : '' }}"
                data-url="{{ route('admin.pedidos.fecha', $pedido->id) }}"
                onchange="guardarFecha(this)"
                class="border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369]"
            >
            <span id="fecha-ok" class="hidden text-green-600 text-xs ml-2">✓ Guardado</span>
        </div>
    </div>

    {{-- Info cliente + pedido --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <div class="bg-white border border-slate-200 rounded-md overflow-hidden">
            <div class="px-5 py-3 bg-slate-900 text-white text-sm font-semibold">Información del cliente</div>
            <div class="p-5 space-y-3 text-sm text-slate-700">
                <div>
                    <p class="text-xs text-slate-400 uppercase">Nombre</p>
                    <p class="font-medium text-slate-900">{{ $pedido->cliente->nombre }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Email</p>
                    <p class="font-medium text-slate-900 break-all">{{ $pedido->cliente->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Teléfono</p>
                    <p class="font-medium text-slate-900">{{ $pedido->cliente->telefono }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Domicilio</p>
                    <p class="font-medium text-slate-900">{{ $pedido->cliente->domicilio }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Localidad / Provincia</p>
                    <p class="font-medium text-slate-900">{{ $pedido->cliente->localidad }}, {{ $pedido->cliente->provincia }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-md overflow-hidden">
            <div class="px-5 py-3 bg-slate-900 text-white text-sm font-semibold">Información del pedido</div>
            <div class="p-5 space-y-3 text-sm text-slate-700">
                <div>
                    <p class="text-xs text-slate-400 uppercase">Nº de pedido</p>
                    <p class="font-medium text-slate-900">{{ $pedido->numero_pedido }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Fecha de compra</p>
                    <p class="font-medium text-slate-900">{{ $pedido->fecha_compra->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Forma de pago</p>
                    <p class="font-medium text-slate-900 capitalize">{{ str_replace('_', ' ', $pedido->forma_pago) }}</p>
                </div>
                @if($pedido->mensaje)
                <div class="pt-3 border-t border-slate-100">
                    <p class="text-xs text-slate-400 uppercase">Mensaje del cliente</p>
                    <p class="font-medium text-slate-900 italic mt-1">{{ $pedido->mensaje }}</p>
                </div>
                @endif
                @if($pedido->archivo_path)
                <div class="pt-3 border-t border-slate-100">
                    <p class="text-xs text-slate-400 uppercase">Archivo adjunto</p>
                    <a href="{{ asset('storage/' . $pedido->archivo_path) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-blue-600 hover:underline text-sm mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        {{ $pedido->archivo_nombre }}
                    </a>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Productos --}}
    <div class="bg-white border border-slate-200 rounded-md overflow-hidden">
        <div class="px-5 py-3 bg-slate-900 text-white text-sm font-semibold">Productos</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">Código</th>
                        <th class="px-5 py-3 text-left font-medium">Producto</th>
                        <th class="px-5 py-3 text-right font-medium">Precio unit.</th>
                        <th class="px-5 py-3 text-center font-medium">Cant.</th>
                        <th class="px-5 py-3 text-right font-medium">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($pedido->items as $item)
                    <tr>
                        <td class="px-5 py-3 text-slate-500">{{ $item->codigo_producto }}</td>
                        <td class="px-5 py-3 font-medium text-slate-900">{!! $item->nombre_producto !!}</td>
                        <td class="px-5 py-3 text-right">
                            @if($item->descuento_unitario > 0)
                                <span class="line-through text-slate-400 text-xs block">${{ number_format($item->precio_unitario, 2, ',', '.') }}</span>
                                <span class="text-green-600 font-medium">${{ number_format($item->precio_unitario - $item->descuento_unitario, 2, ',', '.') }}</span>
                            @else
                                <span class="text-slate-900">${{ number_format($item->precio_unitario, 2, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center text-slate-900">{{ $item->cantidad }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-900">${{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Totales --}}
    <div class="bg-white border border-slate-200 rounded-md p-5">
        <div class="max-w-sm ml-auto space-y-2 text-sm text-slate-700">
            <div class="flex justify-between">
                <span>Subtotal sin descuento:</span>
                <span class="font-medium">${{ number_format($pedido->subtotal_sin_descuento, 2, ',', '.') }}</span>
            </div>

            @if($pedido->descuento_cliente > 0)
            @php
                $partesCliente = array_values(array_filter([
                    (float)($pedido->porcentaje_descuento_c1 ?? 0),
                    (float)($pedido->porcentaje_descuento_c2 ?? 0),
                    (float)($pedido->porcentaje_descuento_c3 ?? 0),
                ]));
            @endphp
            <div class="flex justify-between text-green-600">
                <span>Descuento cliente {{ !empty($partesCliente) ? '(' . implode('%+', array_map(fn($p) => number_format($p, 0, ',', '.'), $partesCliente)) . '%)' : '' }}:</span>
                <span class="font-medium">-${{ number_format($pedido->descuento_cliente, 2, ',', '.') }}</span>
            </div>
            @endif

            @if($pedido->descuento_tipo > 0)
            <div class="flex justify-between text-green-600">
                <span>Desc. tipo de producto:</span>
                <span class="font-medium">-${{ number_format($pedido->descuento_tipo, 2, ',', '.') }}</span>
            </div>
            @endif

            @if($pedido->descuento_producto > 0)
            <div class="flex justify-between text-green-600">
                <span>Descuento producto:</span>
                <span class="font-medium">-${{ number_format($pedido->descuento_producto, 2, ',', '.') }}</span>
            </div>
            @endif

            @if($pedido->descuento_pago > 0)
            <div class="flex justify-between text-green-600">
                <span>Desc. {{ ucfirst(str_replace('_', ' ', $pedido->forma_pago)) }} ({{ rtrim(rtrim(number_format($pedido->porcentaje_descuento_pago ?? 0, 2, '.', ''), '0'), '.') }}%):</span>
                <span class="font-medium">-${{ number_format($pedido->descuento_pago, 2, ',', '.') }}</span>
            </div>
            @endif

            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span class="font-medium">${{ number_format($pedido->subtotal, 2, ',', '.') }}</span>
            </div>
            @php
                $ivaDetalle = collect($pedido->iva_detalle ?? []);
                $ivaLabel = $ivaDetalle->isNotEmpty()
                    ? \App\Support\CarritoIva::etiquetaDetalle($ivaDetalle)
                    : 'IVA ('.number_format($pedido->porcentaje_iva, 2).'%)';
            @endphp
            <div class="flex justify-between">
                <span>{{ $ivaLabel }}:</span>
                <span class="font-medium">${{ number_format($pedido->iva, 2, ',', '.') }}</span>
            </div>
            @foreach($ivaDetalle as $lineaIva)
            <div class="flex justify-between text-xs text-slate-500">
                <span>IVA {{ \App\Support\CarritoIva::formatearPorcentaje((float) $lineaIva['porcentaje']) }}%</span>
                <span>${{ number_format($lineaIva['iva'], 2, ',', '.') }}</span>
            </div>
            @endforeach
            <div class="flex justify-between text-base font-bold border-t border-slate-200 pt-3 mt-1">
                <span>Total:</span>
                <span>${{ number_format($pedido->total, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function guardarFecha(input) {
    fetch(input.dataset.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ fecha_entrega: input.value })
    })
    .then(r => r.json())
    .then(() => {
        const ok = document.getElementById('fecha-ok');
        ok.classList.remove('hidden');
        setTimeout(() => ok.classList.add('hidden'), 2000);
    })
    .catch(() => alert('Error al guardar la fecha.'));
}

function toggleEstado(btn) {
    fetch(btn.dataset.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        const badge = document.getElementById('badge-estado');
        if (data.entregado) {
            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700';
            badge.textContent = 'Entregado' + (data.fecha_entregado ? ' · ' + data.fecha_entregado : '');
            btn.dataset.entregado = '1';
            btn.className = 'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200';
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Marcar como pendiente`;
        } else {
            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-700';
            badge.textContent = 'Pendiente';
            btn.dataset.entregado = '0';
            btn.className = 'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition bg-[#AD0369] text-white hover:bg-[#AD0369]/90';
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Marcar como entregado`;
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
        const badge = document.getElementById('badge-estado');
        const toggleBtn = document.getElementById('btn-toggle');
        if (data.cancelado) {
            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700';
            badge.textContent = 'Cancelado';
            btn.dataset.cancelado = '1';
            btn.className = 'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-300';
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l16 16M4 20L20 4"/></svg> Restaurar pedido`;
            if (toggleBtn) toggleBtn.style.display = 'none';
        } else {
            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-700';
            badge.textContent = 'Pendiente';
            btn.dataset.cancelado = '0';
            btn.className = 'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition bg-red-50 text-red-700 hover:bg-red-100 border border-red-200';
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Cancelar pedido`;
            if (toggleBtn) toggleBtn.style.display = '';
        }
    })
    .catch(() => alert('Error al actualizar el estado.'));
}
</script>
@endsection
