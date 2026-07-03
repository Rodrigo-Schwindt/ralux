<div x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
<style>
/* ═══════════════════════════════════════════════════════════
   Carrito Zona — responsive SOLO ≤ 1239px
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 1239px) {
    .zc-main {
        margin-top: 40px !important;
        padding-bottom: 60px !important;
    }
}
@media (max-width: 767px) {
    .zc-main {
        margin-top: 28px !important;
    }
}
@keyframes zc-row-in {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0);   }
}
.zc-row {
    animation: zc-row-in 0.32s cubic-bezier(.25,.46,.45,.94) both;
}
.zc-row > td {
    transition: background-color 0.2s ease, color 0.2s ease;
}
.zc-row:hover > td {
    background-color: rgba(173, 3, 105, 0.055);
}
.zc-row-divider > td {
    position: relative;
    overflow: hidden;
}
.zc-row-divider > td::before,
.zc-row-divider > td::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    height: 18px;
    background-color: rgba(173, 3, 105, 0.055);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}
.zc-row-divider > td::before {
    top: 0;
}
.zc-row-divider > td::after {
    bottom: 0;
}
.zc-row:hover + .zc-row-divider > td::before {
    opacity: 1;
}
.zc-row-divider:has(+ .zc-row:hover) > td::after {
    opacity: 1;
}
.zc-table-start > td {
    transition: background-color 0.2s ease;
}
.zc-table-start:has(+ .zc-row:hover) > td {
    background-color: rgba(173, 3, 105, 0.055);
}
.zc-qty-control,
.zc-qty-control input,
.zc-qty-control button,
.zc-qty-control div {
    background-color: #fff;
}
</style>
    <div class=" pt-[44px]">
        <div class="max-w-[1224px] mx-auto px-4 lg:px-0">
            <nav class="flex items-center gap-1 text-[13px] font-inter">
                <a wire:navigate href="/zona-privada/productos" class="text-black hover:text-black transition-colors font-bold">Inicio</a>
                <span class="text-black/60">/</span>
                <span class="text-black/80 ">Carrito</span>
            </nav>
        </div>
    </div>

    <div class="zc-main max-w-[1224px] mx-auto px-4 sm:px-6 lg:px-0 pb-[127px] mt-[78px]"
         x-data="{ animate: false }"
         x-init="setTimeout(() => animate = true, 100)"
         x-show="animate"
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0">
        <div class="overflow-x-auto rounded-t-[20px] -mx-4 sm:mx-0 px-4 sm:px-0">
            <table class="w-full border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-[#F5F5F5] h-[52px] rounded-t-[20px] text-[13px] sm:text-[14px] lg:text-[16px] text-[#222] font-inter font-semibold leading-normal">
                        <th class="text-left"></th>
                        <th class="pl-[12px] sm:pl-[22px] lg:w-[80px] text-left">Código</th>
                        <th class="pl-[12px] sm:pl-[22px] lg:w-[100px] text-left">Marca</th>
                        <th class="pl-[12px] sm:pl-[22px] text-left">Modelo</th>
                        <th class="text-left lg:min-w-[120px]">Descripción</th>
                        <th class="text-left pl-[12px] sm:pl-[22px]">Precio</th>
                        {{-- <th class="text-left sm:pl-[22px]">Descuento</th> --}}
                        <th class="text-left lg:w-[130px]">Precio con descuento</th>
                        <th class="text-left pl-[12px] sm:pl-[22px] w-[100px] min-w-[100px]">Cantidad</th>
                        <th class="text-right lg:w-[110px]">Total</th>
                       
                        <th class="text-right pr-2 sm:pr-4"></th>
                    </tr>
                </thead>

                <tbody class="bg-white ">
                    <tr class="zc-table-start">
                        <td colspan="10" class="h-[18px]"></td>
                    </tr>
                    @foreach($items as $item)
@php
    $precioOriginal    = $item->precio_unitario;
    $descuentosCalc    = $this->calcularDescuentos($precioOriginal, $item->producto);
    $partes            = $descuentosCalc['partes'];
    $precioFinalUnitario = $descuentosCalc['precio_final'];
@endphp
            
    <tr class="zc-row h-[73px]" style="animation-delay: {{ $loop->index * 0.05 }}s">
        <td wire:click="abrirDetalleProducto({{ $item->producto->id }})" class="zc-detail-cell w-[60px] sm:w-[80px] h-[60px] sm:h-[73px] cursor-pointer">
            <div class="w-full h-full rounded-[10px] bg-white max-[650px]:h-[60px] border border-[#D9D9D9] overflow-hidden relative">
                <img
                    src="{{ $item->producto->imagenPrincipal?->ruta ? asset('storage/'.$item->producto->imagenPrincipal->ruta) : asset('no-image.png') }}"
                    alt="producto"
                    class="w-full h-[73px] object-contain block"
                >
                <div class="absolute inset-0 bg-black/10 pointer-events-none z-[1] transition-colors duration-300"></div>
            </div>
        </td>

        <td wire:click="abrirDetalleProducto({{ $item->producto->id }})" class="zc-detail-cell pl-[12px] sm:pl-[22px] uppercase text-black/60 font-inter text-[13px] sm:text-[14px] lg:text-[14px] font-normal leading-[25px] cursor-pointer hover:text-[#AD0369] transition-colors">
            <span>{{ $item->producto->codigo_ralux }}</span>
            @if(\App\Support\CarritoIva::productoUsaIvaEspecial($item->producto, $config))
                <span class="ml-1.5 text-[#ad0369] text-[18px] leading-none" title="IVA especial">★</span>
            @endif
        </td>

        <td wire:click="abrirDetalleProducto({{ $item->producto->id }})" class="zc-detail-cell pl-[12px] sm:pl-[22px] text-black/60 font-inter text-[13px] sm:text-[14px] lg:text-[14px] font-normal leading-[25px] max-w-[120px] cursor-pointer hover:text-[#AD0369] transition-colors">
            {{ $item->producto->marcas->first()?->descripcion_es ?? '-' }}
        </td>

        <td wire:click="abrirDetalleProducto({{ $item->producto->id }})" class="zc-detail-cell pl-[12px] sm:pl-[22px] text-black/60 font-inter text-[13px] pr-2 sm:text-[14px] lg:text-[14px] font-normal leading-[25px] max-w-[120px] cursor-pointer hover:text-[#AD0369] transition-colors">
            {{ $item->producto->modelos->first()?->descripcion_es ?? '-' }}
        </td>

        <td wire:click="abrirDetalleProducto({{ $item->producto->id }})" class="zc-detail-cell pr-2 min-w-[120px] max-w-[120px] cursor-pointer">
            <div class="line-clamp-2 overflow-hidden text-black/60 font-inter text-[13px] sm:text-[14px] font-normal leading-[25px]">{{ $item->producto->descripcion_es }}</div>
        </td>

        <td wire:click="abrirDetalleProducto({{ $item->producto->id }})" class="zc-detail-cell text-black/60 text-left pl-[12px] sm:pl-[22px] font-inter text-[13px] sm:text-[14px] lg:text-[16px] font-normal leading-normal cursor-pointer hover:text-[#AD0369] transition-colors">
            ${{ number_format($precioOriginal, 2, ',', '.') }}
        </td>

        {{-- <td class="pl-[12px] sm:pl-[22px] text-left text-black/60 font-inter text-[13px] sm:text-[14px] lg:text-[16px]  font-normal leading-normal">
            @if(empty($partes))
                <span class="text-black/30">—</span>
            @else
                {{ implode('%+', array_map(fn($p) => number_format($p, 0, ',', '.'), $partes)) }}%
            @endif
        </td> --}}

        <td wire:click="abrirDetalleProducto({{ $item->producto->id }})" class="zc-detail-cell text-black/60 text-left font-inter text-[13px] sm:text-[14px] lg:text-[16px] font-normal max-[650px]:text-center   leading-normal cursor-pointer hover:text-[#AD0369] transition-colors">
            ${{ number_format($precioFinalUnitario, 2, ',', '.') }}
        </td>

        <td class="pl-[12px] sm:pl-[22px] text-center">
            <div class="flex justify-center items-center w-[100px]">
                <div class="zc-qty-control inline-flex justify-between px-2 w-[100px] border border-gray-300 rounded-md overflow-hidden h-[36px] bg-white">
                    <input
                        type="text"
                        readonly
                        class="w-[30px] text-center text-sm focus:outline-none bg-white"
                        value="{{ $item->cantidad }}"
                    >
            
                    <div class="flex flex-col">
                        <button
                            type="button"
                            wire:click="incrementar({{ $item->id }})"
                            class="w-[24px] h-[18px] flex items-center justify-center hover:bg-gray-100 text-xs rotate-180"
                        >
                            <svg class="translate-y-[-3.5px]" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 9L12 15L18 9" stroke="#020000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
            
                        <button
                            type="button"
                            wire:click="decrementar({{ $item->id }})"
                            class="w-[24px] h-[18px] flex items-center justify-center hover:bg-gray-100 text-xs"
                        >
                            <svg class="-translate-y-[3.5px]" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 9L12 15L18 9" stroke="#020000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </td>

        <td class="text-black/60 text-right font-inter text-[13px] sm:text-[14px] lg:text-[16px] pl-4 font-semibold leading-normal">
            ${{ number_format($precioFinalUnitario * $item->cantidad, 2, ',', '.') }}
        </td>

        <td class="px-2 sm:px-4 align-middle text-right">
            <button
                wire:click="eliminar({{ $item->id }})"
                wire:confirm="¿Estás seguro de eliminar este producto?"
                class="ml-auto rounded-[22px] border border-[#AD0369] cursor-pointer w-[40px] sm:w-[57px] h-[35px] sm:h-[44px] text-[#AD0369] justify-center flex items-center hover:bg-[#AD0369] hover:text-white transition-colors">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M3 6H21M19 6V20C19 21 18 22 17 22H7C6 22 5 21 5 20V6M8 6V4C8 3 9 2 10 2H14C15 2 16 3 16 4V6M10 11V17M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
            </button>
        </td>
    </tr>

    <tr class="zc-row-divider">
        <td colspan="10" class="py-[18px]">
            <div class="w-full h-px bg-[#E5E5E5]"></div>
        </td>
    </tr>


                    
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-[44px]">
            <a 
                wire:navigate 
                href="{{ route('cliente.productos') }}"
                class="inline-flex items-center w-full sm:w-[250px] h-[44px] gap-2 bg-transparent text-[#AD0369] text-center font-inter text-[13px] sm:text-[14px] font-normal leading-normal uppercase rounded-[22px] border border-[#AD0369] justify-center hover:bg-[#AD0369] hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
  <path d="M8 3V13M3 8H13" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>   AGREGAR MÁS PRODUCTOS
            </a>
        </div>

        <form action="{{ route('cliente.carrito.realizar-pedido') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-[24px] mt-[60px] sm:mt-[80px] lg:mt-[123px]">
                <div class="space-y-[24px]">
                    <div>
                        <div class="bg-[#F5F5F5] text-[#222] px-[20px] sm:px-[26px] h-[56px] flex items-center rounded-t-[20px]">
                            <h3 class="font-inter text-[16px] sm:text-[18px] font-semibold">Información importante</h3>
                        </div>
                        <div class="bg-white min-h-[150px] border border-gray-200 px-[20px] sm:px-[28px] pt-[24px] sm:pt-[29px] pb-[24px] sm:pb-[31px] rounded-b-[20px]">
                            @if($config && $config->informacion)
                                <div class="prose prose-sm max-w-none text-[14px] sm:text-[16px]">
                                    {!! $config->informacion !!}
                                </div>

                            @endif
                        </div>
                    </div>
        
                    <div>
                        <div class="bg-[#F5F5F5] text-[#222] px-[20px] sm:px-[26px] h-[56px] flex items-center rounded-t-[20px]">
                            <h3 class="font-inter text-[16px] sm:text-[18px] font-semibold">Escribinos un mensaje</h3>
                        </div>
                        <div class="bg-white min-h-[150px] border border-gray-200 rounded-b-[20px] overflow-hidden">
                            <textarea
                                name="mensaje"
                                placeholder="{{ $config && $config->escribenos ? strip_tags($config->escribenos) : 'Escribenos' }}"
                                class="w-full h-full min-h-[150px] bg-transparent border-none outline-none resize-none px-[20px] sm:px-[27px] pt-[14px] pb-[14px] font-inter text-[14px] sm:text-[16px] text-black/80 placeholder-black/40"
                            ></textarea>
                        </div>
                    </div>
        
                    <div>
                        <h3 class="text-black font-inter text-[18px] sm:text-[20px] font-semibold leading-normal mb-[11px] ">Adjunta un archivo</h3>
                        <div class="flex flex-col sm:flex-row w-full gap-2 sm:gap-0">
                            <div class="bg-white border border-gray-200 px-[20px] sm:px-[24px] w-full h-[48px] flex items-center rounded-[22px] sm:rounded-r-none">
                                <div class="flex items-center gap-4">
                                    <input 
                                        type="file" 
                                        name="archivo"
                                        id="archivo" 
                                        class="hidden"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="document.getElementById('archivo-nombre').textContent = this.files[0]?.name || 'Seleccionar archivo'"
                                    >
                                    <span class="text-black font-inter text-black/60 text-[14px] sm:text-[16px] font-normal leading-normal truncate" id="archivo-nombre">
                                        Seleccionar archivo
                                    </span>
                                </div>
                            </div>
                            <label 
                                for="archivo" 
                                class="bg-[#F5F5F5] rounded-r-[22px] text-[#AD0369] w-full sm:w-[135px] h-[48px] flex items-center justify-center rounded-[4px] sm:rounded-l-none cursor-pointer text-[13px] sm:text-[14px]  uppercase  transition-colors">
                                ADJUNTAR
                            </label>
                        </div>
                    </div>
                </div>
        
                <div>
                    <div>
                        <div class="bg-[#F5F5F5] text-[#222] px-[20px] sm:px-[26px] h-[56px] flex items-center rounded-t-[20px]">
                            <h3 class="font-inter text-[16px] sm:text-[18px] font-semibold">Formas de pago</h3>
                        </div>
                        <div class="bg-white min-h-[150px] border border-gray-200 px-[20px] sm:px-[24px] py-[18px] rounded-b-[20px] space-y-3">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-[14px] sm:gap-[21px]">
                                    <input 
                                        type="radio" 
                                        name="forma_pago" 
                                        value="contado"
                                        wire:model.live="formaPago"
                                        class="w-[18px] h-[18px] sm:w-[20px] sm:h-[20px] text-[#E4002B] focus:ring-[#AD0369] flex-shrink-0"
                                        required
                                    >
                                    <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">Contado</span>
                                </div>
                                @if($config && $config->contado > 0)
                                <span class="text-[#308C05] text-right font-inter text-[13px] sm:text-[16px] font-normal leading-normal whitespace-nowrap ml-2">{{ rtrim(rtrim(number_format($config->contado, 2, '.', ''), '0'), '.') }}% descuento</span>
                                @endif
                            </label>
                    
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-[14px] sm:gap-[21px]">
                                    <input 
                                        type="radio" 
                                        name="forma_pago" 
                                        value="transferencia"
                                        wire:model.live="formaPago"
                                        class="w-[18px] h-[18px] sm:w-[20px] sm:h-[20px] text-[#E4002B] focus:ring-[#E4002B] flex-shrink-0"
                                    >
                                    <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">Transferencia</span>
                                </div>
                                @if($config && $config->transferencia > 0)
                                <span class="text-[#308C05] text-right font-inter text-[13px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">{{ rtrim(rtrim(number_format($config->transferencia, 2, '.', ''), '0'), '.') }}% descuento</span>
                                @endif
                            </label>
                    
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-[14px] sm:gap-[21px]">
                                    <input 
                                        type="radio" 
                                        name="forma_pago" 
                                        value="cuenta_corriente"
                                        wire:model.live="formaPago"
                                        class="w-[18px] h-[18px] sm:w-[20px] sm:h-[20px] text-[#E4002B] focus:ring-[#E4002B] flex-shrink-0"
                                    >
                                    <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">Cuenta corriente</span>
                                </div>
                                @if($config && $config->corriente > 0)
                                <span class="text-[#308C05] text-right font-inter text-[13px] sm:text-[16px] font-normal leading-normal whitespace-nowrap ml-2">{{ rtrim(rtrim(number_format($config->corriente, 2, '.', ''), '0'), '.') }}% descuento</span>
                                @endif
                            </label>
                        </div>
                    </div>
                    <div class="bg-[#F5F5F5] text-[#222] px-[20px] sm:px-[26px] h-[56px] flex items-center rounded-t-[20px] mt-[24px]">
                        <h3 class="font-inter text-[16px] sm:text-[18px] font-semibold">Tu pedido</h3>
                    </div>
                    <div class="bg-white border border-gray-200 px-4 sm:px-6 min-h-[272px] pt-[18px] rounded-b-[20px] flex flex-col gap-4 pb-[12px]">
                
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">Subtotal sin descuento</span>
                            <span class="text-black text-right font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">${{ number_format($subtotalSinDescuento, 2, ',', '.') }}</span>
                        </div>
                    
                        @if($totalDescuentoCliente > 0)
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal">
                                Descuento cliente{{ !empty($partesCliente) ? ' (' . implode('%+', array_map(fn($p) => number_format($p, 0, ',', '.'), $partesCliente)) . '%)' : '' }}
                            </span>
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">
                                -${{ number_format($totalDescuentoCliente, 2, ',', '.') }}
                            </span>
                        </div>
                        @endif
                        @if($totalDescuentoTipo > 0)
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal">
                                Desc. tipo de producto
                            </span>
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">
                                -${{ number_format($totalDescuentoTipo, 2, ',', '.') }}
                            </span>
                        </div>
                        @endif
                        @if($totalDescuentoProducto > 0)
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal">
                                Descuento producto
                            </span>
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">
                                -${{ number_format($totalDescuentoProducto, 2, ',', '.') }}
                            </span>
                        </div>
                        @endif
                    
                        @if($descuentoPorPago > 0)
                        @php
                            $porcentajePago = 0;
                            if ($config) {
                                if ($formaPago === 'contado') $porcentajePago = $config->contado;
                                elseif ($formaPago === 'transferencia') $porcentajePago = $config->transferencia;
                                elseif ($formaPago === 'cuenta_corriente') $porcentajePago = $config->corriente;
                            }
                        @endphp
                        <div class="flex justify-between items-center text-[#007600] gap-2">
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal">
                                Descuento {{ ucfirst(str_replace('_', ' ', $formaPago)) }} ({{ rtrim(rtrim(number_format($porcentajePago, 2, '.', ''), '0'), '.') }}%)
                            </span>
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">-${{ number_format($descuentoPorPago, 2, ',', '.') }}</span>
                        </div>
                        @endif
                    
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">Subtotal con descuentos</span>
                            <span class="text-black text-right font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">${{ number_format($subtotalConDescuentoPago, 2, ',', '.') }}</span>
                        </div>
                    
                        <div class="border-t border-gray-200 pt-4 mt-auto">
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">
                                    {{ \App\Support\CarritoIva::etiquetaDetalle($ivaDetalle) }}
                                    @if($ivaDetalle->count() === 1 && \App\Support\CarritoIva::porcentajeEsEspecial((float) $ivaDetalle->first()['porcentaje'], $config))
                                        <span class="ml-1.5 text-[#ad0369] text-[18px] leading-none" title="IVA especial">★</span>
                                    @endif
                                </span>
                                <span class="text-black text-right font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">${{ number_format($iva, 2, ',', '.') }}</span>
                            </div>
                            @if($ivaDetalle->count() > 1)
                                <div class="mt-2 space-y-1">
                                    @foreach($ivaDetalle as $lineaIva)
                                        <div class="flex justify-between items-center gap-2 text-black/55 font-inter text-[12px] sm:text-[13px]">
                                            <span>
                                                IVA {{ \App\Support\CarritoIva::formatearPorcentaje((float) $lineaIva['porcentaje']) }}%
                                                @if(\App\Support\CarritoIva::porcentajeEsEspecial((float) $lineaIva['porcentaje'], $config))
                                                    <span class="ml-1.5 text-[#ad0369] text-[17px] leading-none" title="IVA especial">★</span>
                                                @endif
                                            </span>
                                            <span class="whitespace-nowrap">${{ number_format($lineaIva['iva'], 2, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    
                        <div>
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1 sm:gap-2">
                                <div class="flex flex-col sm:flex-row gap-1 sm:gap-2 items-start sm:items-center">
                                    <span class="text-black font-inter text-[20px] sm:text-[24px] font-semibold leading-normal">Total</span>
                                    <span class="text-black font-inter text-[13px] sm:text-[15px] font-semibold leading-normal sm:pt-1">(IVA incluido)</span>
                                </div>
                                <span class="text-black text-right font-inter text-[20px] sm:text-[24px] font-semibold leading-normal whitespace-nowrap">${{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    
                    </div>
                
                    <div class="pt-[53px] flex flex-col gap-4 lg:flex-row">
                        <a 
                            href="{{ route('cliente.productos') }}"
                            class="w-full h-[44px] cursor-pointer rounded-[22px] border border-[#AD0369] text-[#AD0369] text-center font-inter text-[13px] sm:text-[14px] font-normal leading-normal uppercase bg-transparent flex items-center justify-center hover:bg-[#AD0369] hover:text-white transition-colors">
                            CANCELAR PEDIDO
                        </a>
                        <button 
                            type="submit"
                            class="w-full rounded-[22px] cursor-pointer bg-[#AD0369] text-white text-center font-inter text-[13px] sm:text-[14px] font-normal leading-normal uppercase h-[44px] hover:bg-[#B30034] transition-colors">
                            REALIZAR PEDIDO
                        </button>
                    </div>
                </div>
            </div>
        </form>


    </div>

    @include('livewire.zona.partials.producto-detalle-modal', ['productoDetalle' => $productoDetalle])

    <div
        x-data="{
            show: false, title: '', message: '', type: 'success', timer: null,
            init() {
                @if(session('success'))
                    setTimeout(() => this.lanzar('{{ session('success') }}', 'success'), 100);
                @elseif(session('error'))
                    setTimeout(() => this.lanzar('{{ session('error') }}', 'error'), 100);
                @endif
            },
            lanzar(msg, tipo) {
                this.title = tipo === 'success' ? 'Listo' : 'Atención';
                this.message = msg;
                this.type = tipo;
                this.show = true;
                clearTimeout(this.timer);
                this.timer = setTimeout(() => this.show = false, 4200);
            }
        }"
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-[0.98]"
        class="fixed top-5 right-5 z-[9999] w-[calc(100vw-2.5rem)] max-w-[380px] rounded-2xl border bg-white/95 shadow-[0_18px_50px_rgba(0,0,0,0.18)] backdrop-blur-md overflow-hidden"
        :class="type === 'success' ? 'border-[#EF338C]/30' : 'border-red-300'"
    >
        <div class="flex items-start gap-3 p-4">
            <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full"
                 :class="type === 'success' ? 'bg-[#AD0369]/10 text-[#AD0369]' : 'bg-red-100 text-red-600'">
                <svg x-show="type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg x-show="type !== 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-[14px] font-semibold text-[#222]" x-text="title"></p>
                <p class="text-[13px] text-[#585858] mt-0.5" x-text="message"></p>
            </div>
            <button type="button" class="text-[#777] hover:text-[#111] cursor-pointer" @click="show=false" aria-label="Cerrar toast">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="h-[3px] w-full" :class="type === 'success' ? 'bg-[#AD0369]/20' : 'bg-red-100'">
            <div class="h-full animate-toast-progress"
                 :class="type === 'success' ? 'bg-gradient-to-r from-[#AD0369] to-[#FD359D]' : 'bg-red-500'"></div>
        </div>
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</div>
