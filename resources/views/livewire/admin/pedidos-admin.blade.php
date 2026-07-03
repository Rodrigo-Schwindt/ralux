<div>

@if($vista === 'lista')
<div class="space-y-6 animate-fadeIn">

    {{-- Header --}}
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-2xl font-semibold text-slate-900">Pedidos ({{ $pedidos->total() }})</h2>

            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="buscar"
                    placeholder="Buscar por nº pedido o cliente..."
                    class="w-full sm:w-72 pl-4 pr-10 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369]"
                >
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="flex flex-wrap items-end gap-3 p-4 bg-slate-50 border border-slate-200 rounded-md">
            <div class="flex flex-col gap-1">
                <label for="filtro-estado" class="text-xs text-slate-500 font-medium uppercase">Estado</label>
                <select
                    id="filtro-estado"
                    wire:model.live="filtroEntregado"
                    class="px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369] bg-white"
                >
                    <option value="todos">Todos</option>
                    <option value="pendientes">Pendientes</option>
                    <option value="entregados">Entregados</option>
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label for="filtro-forma-pago" class="text-xs text-slate-500 font-medium uppercase">Forma de pago</label>
                <select
                    id="filtro-forma-pago"
                    wire:model.live="filtroFormaPago"
                    class="px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369] bg-white"
                >
                    <option value="todos">Todas</option>
                    <option value="contado">Contado</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="cuenta_corriente">Cuenta corriente</option>
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label for="filtro-fecha-desde" class="text-xs text-slate-500 font-medium uppercase">Fecha desde</label>
                <input
                    id="filtro-fecha-desde"
                    type="date"
                    wire:model.live="filtroFechaDesde"
                    class="px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369] bg-white"
                >
            </div>

            <div class="flex flex-col gap-1">
                <label for="filtro-fecha-hasta" class="text-xs text-slate-500 font-medium uppercase">Fecha hasta</label>
                <input
                    id="filtro-fecha-hasta"
                    type="date"
                    wire:model.live="filtroFechaHasta"
                    class="px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369] bg-white"
                >
            </div>

            @if($buscar !== '' || $filtroEntregado !== 'todos' || $filtroFormaPago !== 'todos' || $filtroFechaDesde !== '' || $filtroFechaHasta !== '')
            <button
                wire:click="limpiarFiltros"
                type="button"
                class="px-3 py-2 text-sm text-slate-500 hover:text-slate-700 border border-slate-300 rounded-md bg-white hover:bg-slate-50 transition"
            >
                Limpiar filtros
            </button>
            @endif
        </div>
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
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">{{ $pedido->numero_pedido }}</td>

                    <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $pedido->fecha_compra->format('d/m/Y') }}</td>

                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-900">{{ $pedido->cliente->nombre }}</div>
                        <div class="text-xs text-slate-400">{{ $pedido->cliente->email }}</div>
                    </td>

                    <td class="px-4 py-3 text-slate-500 capitalize whitespace-nowrap">
                        {{ str_replace('_', ' ', $pedido->forma_pago) }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        <input
                            type="date"
                            value="{{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('Y-m-d') : '' }}"
                            x-on:change="$wire.actualizarFechaEntrega({{ $pedido->id }}, $event.target.value)"
                            class="text-sm border border-slate-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[#AD0369]"
                        >
                    </td>

                    <td class="px-4 py-3 text-right font-semibold text-slate-900 whitespace-nowrap">
                        ${{ number_format($pedido->total, 2, ',', '.') }}
                    </td>

                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        @if($pedido->entregado)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Entregado</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">Pendiente</span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button
                                wire:click="verDetalle({{ $pedido->id }})"
                                type="button"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver
                            </button>
                            <button
                                wire:click="toggleEntregado({{ $pedido->id }})"
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
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-slate-400">No se encontraron pedidos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $pedidos->links() }}</div>

</div>
@endif


{{-- ═══════════════════════════════════════════════
     VISTA DETALLE
═══════════════════════════════════════════════ --}}
@if($vista === 'detalle' && $pedidoSeleccionado)
<div class="space-y-6 animate-fadeIn">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Pedido #{{ $pedidoSeleccionado->numero_pedido }}</h2>
            <p class="text-sm text-slate-500 mt-1">Realizado el {{ $pedidoSeleccionado->fecha_compra->format('d/m/Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.pedidos.factura', $pedidoSeleccionado->id) }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Descargar Factura
            </a>
            <button
                wire:click="volverLista"
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-md hover:bg-slate-50 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </button>
        </div>
    </div>

    {{-- Estado + Fecha entrega --}}
    <div class="bg-white border border-slate-200 rounded-md p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4 flex-wrap">
            <div>
                <p class="text-xs text-slate-500 uppercase font-medium mb-1">Estado</p>
                @if($pedidoSeleccionado->entregado)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Entregado
                        @if($pedidoSeleccionado->fecha_entregado)
                            <span class="font-normal text-green-600">· {{ $pedidoSeleccionado->fecha_entregado->format('d/m/Y') }}</span>
                        @endif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-700">
                        Pendiente
                    </span>
                @endif
            </div>

            <button
                wire:click="toggleEntregado({{ $pedidoSeleccionado->id }})"
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition
                    {{ $pedidoSeleccionado->entregado
                        ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200'
                        : 'bg-[#AD0369] text-white hover:bg-[#AD0369]/90' }}"
            >
                @if($pedidoSeleccionado->entregado)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Marcar como pendiente
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Marcar como entregado
                @endif
            </button>
        </div>

        <div>
            <p class="text-xs text-slate-500 uppercase font-medium mb-1">Fecha estimada de entrega</p>
            <input
                type="date"
                value="{{ $pedidoSeleccionado->fecha_entrega ? $pedidoSeleccionado->fecha_entrega->format('Y-m-d') : '' }}"
                x-on:change="$wire.actualizarFechaEntrega({{ $pedidoSeleccionado->id }}, $event.target.value)"
                class="border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#AD0369]"
            >
        </div>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 rounded-md bg-green-50 border border-green-200 text-green-700 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Info cliente + pedido --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <div class="bg-white border border-slate-200 rounded-md overflow-hidden">
            <div class="px-5 py-3 bg-slate-900 text-white text-sm font-semibold">Información del cliente</div>
            <div class="p-5 space-y-3 text-sm text-slate-700">
                <div>
                    <p class="text-xs text-slate-400 uppercase">Nombre</p>
                    <p class="font-medium text-slate-900">{{ $pedidoSeleccionado->cliente->nombre }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Email</p>
                    <p class="font-medium text-slate-900 break-all">{{ $pedidoSeleccionado->cliente->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Teléfono</p>
                    <p class="font-medium text-slate-900">{{ $pedidoSeleccionado->cliente->telefono }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Domicilio</p>
                    <p class="font-medium text-slate-900">{{ $pedidoSeleccionado->cliente->domicilio }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Localidad / Provincia</p>
                    <p class="font-medium text-slate-900">{{ $pedidoSeleccionado->cliente->localidad }}, {{ $pedidoSeleccionado->cliente->provincia }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-md overflow-hidden">
            <div class="px-5 py-3 bg-slate-900 text-white text-sm font-semibold">Información del pedido</div>
            <div class="p-5 space-y-3 text-sm text-slate-700">
                <div>
                    <p class="text-xs text-slate-400 uppercase">Nº de pedido</p>
                    <p class="font-medium text-slate-900">{{ $pedidoSeleccionado->numero_pedido }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Fecha de compra</p>
                    <p class="font-medium text-slate-900">{{ $pedidoSeleccionado->fecha_compra->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase">Forma de pago</p>
                    <p class="font-medium text-slate-900 capitalize">{{ str_replace('_', ' ', $pedidoSeleccionado->forma_pago) }}</p>
                </div>
                @if($pedidoSeleccionado->mensaje)
                <div class="pt-3 border-t border-slate-100">
                    <p class="text-xs text-slate-400 uppercase">Mensaje del cliente</p>
                    <p class="font-medium text-slate-900 italic mt-1">{{ $pedidoSeleccionado->mensaje }}</p>
                </div>
                @endif
                @if($pedidoSeleccionado->archivo_path)
                <div class="pt-3 border-t border-slate-100">
                    <p class="text-xs text-slate-400 uppercase">Archivo adjunto</p>
                    <a href="{{ asset('storage/' . $pedidoSeleccionado->archivo_path) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-blue-600 hover:underline text-sm mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        {{ $pedidoSeleccionado->archivo_nombre }}
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
                    @foreach($pedidoSeleccionado->items as $item)
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
                <span class="font-medium">${{ number_format($pedidoSeleccionado->subtotal_sin_descuento, 2, ',', '.') }}</span>
            </div>

            @if($pedidoSeleccionado->descuento_cliente > 0)
            @php
                $partesCliente = array_values(array_filter([
                    (float)($pedidoSeleccionado->porcentaje_descuento_c1 ?? 0),
                    (float)($pedidoSeleccionado->porcentaje_descuento_c2 ?? 0),
                    (float)($pedidoSeleccionado->porcentaje_descuento_c3 ?? 0),
                ]));
            @endphp
            <div class="flex justify-between text-green-600">
                <span>Descuento cliente {{ !empty($partesCliente) ? '(' . implode('%+', array_map(fn($p) => number_format($p, 0, ',', '.'), $partesCliente)) . '%)' : '' }}:</span>
                <span class="font-medium">-${{ number_format($pedidoSeleccionado->descuento_cliente, 2, ',', '.') }}</span>
            </div>
            @endif

            @if($pedidoSeleccionado->descuento_tipo > 0)
            <div class="flex justify-between text-green-600">
                <span>Desc. tipo de producto:</span>
                <span class="font-medium">-${{ number_format($pedidoSeleccionado->descuento_tipo, 2, ',', '.') }}</span>
            </div>
            @endif

            @if($pedidoSeleccionado->descuento_producto > 0)
            <div class="flex justify-between text-green-600">
                <span>Descuento producto:</span>
                <span class="font-medium">-${{ number_format($pedidoSeleccionado->descuento_producto, 2, ',', '.') }}</span>
            </div>
            @endif

            @if($pedidoSeleccionado->descuento_pago > 0)
            <div class="flex justify-between text-green-600">
                <span>Desc. {{ ucfirst(str_replace('_', ' ', $pedidoSeleccionado->forma_pago)) }} ({{ rtrim(rtrim(number_format($pedidoSeleccionado->porcentaje_descuento_pago ?? 0, 2, '.', ''), '0'), '.') }}%):</span>
                <span class="font-medium">-${{ number_format($pedidoSeleccionado->descuento_pago, 2, ',', '.') }}</span>
            </div>
            @endif

            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span class="font-medium">${{ number_format($pedidoSeleccionado->subtotal, 2, ',', '.') }}</span>
            </div>
            @php
                $ivaDetalle = collect($pedidoSeleccionado->iva_detalle ?? []);
                $ivaLabel = $ivaDetalle->isNotEmpty()
                    ? \App\Support\CarritoIva::etiquetaDetalle($ivaDetalle)
                    : 'IVA ('.number_format($pedidoSeleccionado->porcentaje_iva, 2).'%)';
            @endphp
            <div class="flex justify-between">
                <span>{{ $ivaLabel }}:</span>
                <span class="font-medium">${{ number_format($pedidoSeleccionado->iva, 2, ',', '.') }}</span>
            </div>
            @foreach($ivaDetalle as $lineaIva)
            <div class="flex justify-between text-xs text-slate-500">
                <span>IVA {{ \App\Support\CarritoIva::formatearPorcentaje((float) $lineaIva['porcentaje']) }}%</span>
                <span>${{ number_format($lineaIva['iva'], 2, ',', '.') }}</span>
            </div>
            @endforeach
            <div class="flex justify-between text-base font-bold border-t border-slate-200 pt-3 mt-1">
                <span>Total:</span>
                <span>${{ number_format($pedidoSeleccionado->total, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

</div>
@endif

</div>
