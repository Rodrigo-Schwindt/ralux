<div class="max-w-[1224px] mx-auto pb-[88px] max-[1199px]:px-4 max-[1199px]:pb-16 max-[639px]:pb-12" x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
    <div class=" pt-[44px]">
        <div class="max-w-[1224px] mx-auto px-4 lg:px-0">
            <nav class="flex items-center gap-1 text-[13px] font-inter">
                <a wire:navigate href="/zona-privada/productos" class="text-black hover:text-black transition-colors font-bold">Inicio</a>
                <span class="text-black/60">/</span>
                <span class="text-black/80 ">Mis pedidos</span>
            </nav>
        </div>
    </div>
    @if($pedidos->isEmpty())
        <div class="bg-white rounded-[6px] pt-[78px] p-12 max-[1199px]:p-8 max-[639px]:p-6 text-center animate-fadeIn">
            <svg class="w-24 h-24 max-[639px]:w-20 max-[639px]:h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 font-inter text-[18px] max-[639px]:text-[16px]">No tienes pedidos realizados</p>
            <a wire:navigate href="{{ route('cliente.productos') }}" class="inline-block mt-6 max-[639px]:mt-4 bg-[#E40044] text-white px-6 py-3 max-[639px]:px-5 max-[639px]:py-2.5 rounded-[4px] font-inter text-[14px] max-[639px]:text-[13px] font-medium uppercase transition-all duration-200 hover:bg-[#b8001f]">
                Ir a Productos
            </a>
        </div>
    @else
        <div class="max-[1199px]:hidden pt-[78px]">
            <div class="overflow-hidden rounded-t-[20px]">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-[#F5F5F5] h-[52px] text-[13px] sm:text-[14px] lg:text-[16px] text-[#222] font-inter font-semibold leading-normal">
                        <th class="w-[80px]"></th>
                        <th class="text-left pl-[22px]">Nº de pedido</th>
                        <th class="text-left pl-[18px]">Fecha de compra</th>
                        <th class="text-left pl-[100px]">Estado</th>
                        <th class="text-left pl-[10px]">Importe</th>
                        <th class="text-right pr-[22px]"></th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    <tr>
                        <td colspan="8" class="h-[18px]"></td>
                    </tr>

                    @foreach($pedidos as $pedido)
                        <tr class="zmp-row" style="animation-delay: {{ $loop->index * 0.05 }}s">
                            <td class="w-[80px] h-[73px]">
                                <div class="flex items-center justify-center">
                                    <div class="w-[80px] h-[80px] flex items-center justify-center rounded bg-[#F8F8F8]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="43" height="43" viewBox="0 0 43 43" fill="none">
  <path d="M10.7497 39.4167V7.16667C10.7497 6.21631 11.1272 5.30488 11.7992 4.63287C12.4712 3.96086 13.3826 3.58334 14.333 3.58334H28.6663C29.6167 3.58334 30.5281 3.96086 31.2001 4.63287C31.8721 5.30488 32.2497 6.21631 32.2497 7.16667V39.4167M10.7497 39.4167H32.2497M10.7497 39.4167H7.16634C6.21598 39.4167 5.30455 39.0391 4.63254 38.3671C3.96054 37.6951 3.58301 36.7837 3.58301 35.8333V25.0833C3.58301 24.133 3.96054 23.2215 4.63254 22.5495C5.30455 21.8775 6.21598 21.5 7.16634 21.5H10.7497M32.2497 39.4167H35.833C36.7834 39.4167 37.6948 39.0391 38.3668 38.3671C39.0388 37.6951 39.4163 36.7837 39.4163 35.8333V19.7083C39.4163 18.758 39.0388 17.8465 38.3668 17.1745C37.6948 16.5025 36.7834 16.125 35.833 16.125H32.2497M17.9163 10.75H25.083M17.9163 17.9167H25.083M17.9163 25.0833H25.083M17.9163 32.25H25.083" stroke="#AD0369" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                                    </div>
                                </div>
                            </td>

                            <td class="pl-[22px] text-black font-inter text-[16px] font-normal leading-[25px]">
                                {{ $pedido->numero_pedido }}
                            </td>

                            <td class="pl-[18px] text-black font-inter text-[16px] font-normal leading-normal">
                                {{ $pedido->fecha_compra->format('d/m/Y') }}
                            </td>

                            <td class="pl-[100px]">
                                @if($pedido->cancelado)
                                    <span class="inline-flex items-center gap-1 text-red-500 font-inter text-[14px] font-normal">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Cancelado
                                    </span>
                                @elseif($pedido->entregado)
                                    <span class="inline-flex items-center gap-1 text-[#308C05] font-inter text-[14px] font-normal">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="#308C05" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Entregado
                                    </span>
                                @else
                                    <span class="text-black/40 font-inter text-[14px] font-normal">Pendiente</span>
                                @endif
                            </td>

                            <td class="pl-[10px] text-black font-inter text-[16px] font-normal leading-normal">
                                ${{ number_format($pedido->total, 2, ',', '.') }}
                            </td>

              

                            <td class="align-bottom pb-4 text-right">
                                <div class="w-full flex justify-end gap-[20px] ">
                                    <a
                                        href="{{ route('cliente.pedidos.detalle', $pedido->id) }}"
                                        wire:navigate
                                        class="rounded-[22px] border border-[#AD0369] w-[185px] h-[44px] bg-white text-[#AD0369] text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center hover:bg-[#AD0369] hover:text-white transition-colors"
                                    >
                                        VER DETALLE
                                    </a>

                                    <button
                                        wire:click="recomprar({{ $pedido->id }})"
                                        class="w-[185px] h-[44px] rounded-[22px] bg-[#AD0369] text-white text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center hover:bg-[#AD0369]/90 transition-colors"
                                    >
                                        RECOMPRAR
                                    </button>
                                   
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="8" class="py-[10px]">
                                <div class="w-full h-[1px] bg-[#E5E5E5]"></div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

        <div class="hidden max-[1199px]:grid gap-5 mt-10 max-[639px]:mt-6">
            @foreach($pedidos as $index => $pedido)
                <div class="bg-white border border-[#E5E5E5] rounded-[4px] p-4 max-[639px]:p-3 transition-all duration-300 animate-fadeIn" style="animation-delay: {{ $index * 0.1 }}s;">
                    <div class="flex gap-4 max-[639px]:gap-3 mb-4">
                        <div class="w-[80px] h-[80px] max-[639px]:w-[60px] max-[639px]:h-[60px] flex-shrink-0 flex items-center justify-center rounded bg-[#F8F8F8]">
                            <svg width="43" height="43" viewBox="0 0 43 43" fill="none" xmlns="http://www.w3.org/2000/svg" class="max-[639px]:w-[32px] max-[639px]:h-[32px]">
                                <path d="M28.667 7.16671H32.2503C33.2007 7.16671 34.1121 7.54424 34.7841 8.21624C35.4561 8.88825 35.8337 9.79968 35.8337 10.75V35.8334C35.8337 36.7837 35.4561 37.6952 34.7841 38.3672C34.1121 39.0392 33.2007 39.4167 32.2503 39.4167H10.7503C9.79997 39.4167 8.88853 39.0392 8.21653 38.3672C7.54452 37.6952 7.16699 36.7837 7.16699 35.8334V10.75C7.16699 9.79968 7.54452 8.88825 8.21653 8.21624C8.88853 7.54424 9.79997 7.16671 10.7503 7.16671H14.3337M21.5003 19.7084H28.667M21.5003 28.6667H28.667M14.3337 19.7084H14.3516M14.3337 28.6667H14.3516M16.1253 3.58337H26.8753C27.8648 3.58337 28.667 4.38553 28.667 5.37504V8.95837C28.667 9.94788 27.8648 10.75 26.8753 10.75H16.1253C15.1358 10.75 14.3337 9.94788 14.3337 8.95837V5.37504C14.3337 4.38553 15.1358 3.58337 16.1253 3.58337Z" stroke="#E40044" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="text-gray-600 font-inter text-[13px] max-[639px]:text-[12px] mb-1">Nº de pedido</p>
                                    <h3 class="text-black font-inter text-[16px] max-[639px]:text-[15px] font-semibold break-words">
                                        {{ $pedido->numero_pedido }}
                                    </h3>
                                </div>
                                @if($pedido->cancelado)
                                    <div class="relative w-[32px] h-[32px] max-[639px]:w-[28px] max-[639px]:h-[28px] flex items-center justify-center flex-shrink-0 ml-2">
                                        <svg width="32" height="32" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg" class="max-[639px]:w-[28px] max-[639px]:h-[28px]">
                                            <path d="M37.5 0.5V37.5H0.5V0.5H37.5Z" fill="white" stroke="#D9D9D9"/>
                                            <path d="M36.9436 1.05554H1.05469V36.9444H36.9436V1.05554Z" stroke="#D9D9D9"/>
                                        </svg>
                                        <svg class="absolute max-[639px]:w-[20px] max-[639px]:h-[20px]" width="22" height="22" viewBox="0 0 24 24" fill="none">
                                            <path d="M6 18L18 6M6 6l12 12" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                @elseif($pedido->entregado)
                                    <div class="relative w-[32px] h-[32px] max-[639px]:w-[28px] max-[639px]:h-[28px] flex items-center justify-center flex-shrink-0 ml-2">
                                        <svg width="32" height="32" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg" class="max-[639px]:w-[28px] max-[639px]:h-[28px]">
                                            <path d="M37.5 0.5V37.5H0.5V0.5H37.5Z" fill="white" stroke="#D9D9D9"/>
                                            <path d="M36.9436 1.05554H1.05469V36.9444H36.9436V1.05554Z" stroke="#D9D9D9"/>
                                        </svg>
                                        <svg class="absolute max-[639px]:w-[20px] max-[639px]:h-[20px]" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 26 26" fill="none">
                                            <path d="M8.83075 22.3135L0.380745 13.8635C-0.126915 13.3558 -0.126915 12.5327 0.380745 12.025L2.21918 10.1865C2.72684 9.67882 3.55 9.67882 4.05766 10.1865L9.74999 15.8788L21.9423 3.68653C22.45 3.17887 23.2731 3.17887 23.7808 3.68653L25.6192 5.52502C26.1269 6.03268 26.1269 6.85579 25.6192 7.3635L10.6692 22.3136C10.1615 22.8212 9.33841 22.8212 8.83075 22.3135Z" fill="#E40044"/>
                                        </svg>
                                    </div>
                                @else
                                    <svg width="32" height="32" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 ml-2 max-[639px]:w-[28px] max-[639px]:h-[28px]">
                                        <path d="M37.5 0.5V37.5H0.5V0.5H37.5Z" fill="white" stroke="#D9D9D9"/>
                                        <path d="M36.9436 1.05554H1.05469V36.9444H36.9436V1.05554Z" stroke="#D9D9D9"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-4 text-[14px] max-[639px]:text-[13px]">
                        <div>
                            <p class="text-gray-600 font-inter mb-1">Fecha de compra:</p>
                            <p class="text-black font-inter font-semibold">{{ $pedido->fecha_compra->format('d/m/Y') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-gray-600 font-inter mb-1">Fecha de entrega:</p>
                            <p class="text-black font-inter font-semibold">   {{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('d/m/Y') : '-' }}</p>
                        </div>
                        
                        <div class="col-span-2">
                            <p class="text-gray-600 font-inter mb-1">Importe:</p>
                            <p class="text-[#E40044] font-inter font-bold text-[18px] max-[639px]:text-[16px]">
                                ${{ number_format($pedido->total, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-3 border-t border-gray-200 max-[639px]:flex-col">
                        <a
                            href="{{ route('cliente.pedidos.detalle', $pedido->id) }}"
                            wire:navigate
                            class="flex-1 max-[639px]:w-full rounded-[22px] border border-[#AD0369] py-[13px] bg-white text-[#AD0369] text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center hover:bg-[#AD0369] hover:text-white transition-colors"
                        >
                            VER DETALLE
                        </a>

                        <button
                            wire:click="recomprar({{ $pedido->id }})"
                            class="flex-1 max-[639px]:w-full py-[13px] rounded-[22px] bg-[#AD0369] text-white text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center hover:bg-[#AD0369]/90 transition-colors"
                        >
                            RECOMPRAR
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        @if($pedidos->hasPages() || $pedidos->total() > 10)
        <div class="mt-[48px]">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-[24px]">
                <div class="text-[#666] font-inter text-[13px] sm:text-[14px] text-center sm:text-left">
                    Mostrando
                    <span class="font-semibold text-black">{{ $pedidos->firstItem() }}</span>
                    a
                    <span class="font-semibold text-black">{{ $pedidos->lastItem() }}</span>
                    de
                    <span class="font-semibold text-black">{{ $pedidos->total() }}</span>
                    pedidos
                </div>

                <div class="flex items-center gap-[12px]">
                    <span class="text-[#666] font-inter text-[13px] sm:text-[14px]">Mostrar:</span>
                    <select wire:model.live="perPage" class="h-[36px] px-[12px] rounded-[4px] border border-[#E5E5E5] bg-white font-inter text-[13px] sm:text-[14px] cursor-pointer">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            @if($pedidos->hasPages())
            <div class="flex items-center justify-center gap-[6px] sm:gap-[8px] flex-wrap">
                {{-- Primera página --}}
                @if($pedidos->onFirstPage())
                    <button disabled class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 12L4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                @else
                    <button wire:click="gotoPage(1)" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 12L4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                @endif

                {{-- Anterior --}}
                @if($pedidos->onFirstPage())
                    <button disabled class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                @else
                    <button wire:click="previousPage" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                @endif

                {{-- Números --}}
                @php
                    $currentPage = $pedidos->currentPage();
                    $lastPage    = $pedidos->lastPage();
                    $startPage   = max(1, $currentPage - 2);
                    $endPage     = min($lastPage, $currentPage + 2);
                @endphp

                @if($startPage > 1)
                    <button wire:click="gotoPage(1)" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors font-inter text-[13px] sm:text-[14px]">1</button>
                    @if($startPage > 2)
                        <span class="text-[#666] font-inter text-[13px] sm:text-[14px]">...</span>
                    @endif
                @endif

                @for($page = $startPage; $page <= $endPage; $page++)
                    @if($page == $currentPage)
                        <button class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] bg-[#AD0369] text-white font-inter text-[13px] sm:text-[14px] font-semibold">{{ $page }}</button>
                    @else
                        <button wire:click="gotoPage({{ $page }})" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors font-inter text-[13px] sm:text-[14px]">{{ $page }}</button>
                    @endif
                @endfor

                @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)
                        <span class="text-[#666] font-inter text-[13px] sm:text-[14px]">...</span>
                    @endif
                    <button wire:click="gotoPage({{ $lastPage }})" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors font-inter text-[13px] sm:text-[14px]">{{ $lastPage }}</button>
                @endif

                {{-- Siguiente --}}
                @if($pedidos->hasMorePages())
                    <button wire:click="nextPage" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                @else
                    <button disabled class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                @endif

                {{-- Última página --}}
                @if($pedidos->hasMorePages())
                    <button wire:click="gotoPage({{ $lastPage }})" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                @else
                    <button disabled class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                @endif
            </div>
            @endif
        </div>
        @endif
    @endif

    <style>
        @keyframes zmp-row-in {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .zmp-row { animation: zmp-row-in 0.32s cubic-bezier(.25,.46,.45,.94) both; }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
        }
    </style>

    <div
        x-data="{
            show: false, title: '', message: '', type: 'success', timer: null,
            init() {
                @if(session('toast'))
                    setTimeout(() => this.lanzar('{{ session('toast.message') }}', '{{ session('toast.type', 'success') }}'), 100);
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
</div>