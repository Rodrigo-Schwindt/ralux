<div class="w-full max-w-[1224px] mx-auto px-4 lg:px-0" x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">

    <div class="pt-[44px]">
        <nav class="flex items-center gap-1 text-[13px] font-inter">
            <a wire:navigate href="/zona-privada/productos" class="text-black hover:text-black transition-colors font-bold">Inicio</a>
            <span class="text-black/60">/</span>
            <span class="text-black/80">Lista de precios</span>
        </nav>
    </div>

    <div class="mt-[78px] pb-[127px]"
         x-data="{ animate: false }"
         x-init="setTimeout(() => animate = true, 100)"
         x-show="animate"
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0">

        <div class="max-[1199px]:hidden">
            <div class="overflow-hidden rounded-t-[20px]">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-[#F5F5F5] h-[52px] text-[13px] sm:text-[14px] lg:text-[16px] text-[#222] font-inter font-semibold leading-normal">
                            <th class="w-[80px]"></th>
                            <th class="text-left pl-[52px] lg:w-[450px]">Nombre</th>
                            <th class="text-left pl-[22px]">Formato</th>
                            <th class="text-left pl-[42px]">Peso</th>
                            <th class="text-right pr-[22px]"></th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        <tr>
                            <td colspan="5" class="h-[18px]"></td>
                        </tr>

                        @forelse($precios as $precio)
                        @php
                            $extension = strtoupper(pathinfo($precio->archivo, PATHINFO_EXTENSION));
                            $esExcel = in_array(strtolower($extension), ['xls', 'xlsx'], true);
                            $verUrl = $esExcel ? route('cliente.precios.ver', $precio->id) : Storage::url($precio->archivo);
                            $disk = Storage::disk('public');
                            $peso = $disk->exists($precio->archivo)
                                ? round($disk->size($precio->archivo) / 1024) . ' kb'
                                : '-';
                        @endphp
                        <tr>
                            <td class="w-[80px] h-[73px]">
                                <div class="flex items-center justify-center">
                                    <div class="w-[80px] h-[80px] flex items-center justify-center rounded-[10px] bg-[#F8F8F8]">
                                        <svg width="43" height="43" viewBox="0 0 43 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M25.0827 3.58337V10.75C25.0827 11.7004 25.4602 12.6118 26.1322 13.2838C26.8042 13.9558 27.7157 14.3334 28.666 14.3334H35.8327M17.916 16.125H14.3327M28.666 23.2917H14.3327M28.666 30.4584H14.3327M26.8743 3.58337H10.7493C9.79899 3.58337 8.88755 3.9609 8.21555 4.63291C7.54354 5.30491 7.16602 6.21635 7.16602 7.16671V35.8334C7.16602 36.7837 7.54354 37.6952 8.21555 38.3672C8.88755 39.0392 9.79899 39.4167 10.7493 39.4167H32.2493C33.1997 39.4167 34.1111 39.0392 34.7831 38.3672C35.4552 37.6952 35.8327 36.7837 35.8327 35.8334V12.5417L26.8743 3.58337Z" stroke="#AD0369" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </td>

                            <td class="pl-[52px] text-black/60 font-inter text-[14px] lg:w-[450px] lg:text-[16px] font-normal leading-[25px]">
                                {{ $precio->title }}
                            </td>

                            <td class="pl-[22px] text-black/60 font-inter text-[14px] lg:text-[16px] font-normal leading-normal">
                                {{ $extension }}
                            </td>

                            <td class="pl-[42px] text-black/60 font-inter text-[14px] lg:text-[16px] font-normal leading-normal">
                                {{ $peso }}
                            </td>

                            <td class="align-bottom pb-4 text-right">
                                <div class="w-full flex justify-end gap-[20px] ">
                                    <a href="{{ $verUrl }}" target="_blank"
                                       class="rounded-[22px] border border-[#AD0369] w-[185px] h-[44px] bg-white text-[#AD0369] text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center hover:bg-[#AD0369] hover:text-white transition-colors">
                                        VER ONLINE
                                    </a>
                                    <button wire:click="descargar({{ $precio->id }})"
                                            class="w-[185px] h-[44px] rounded-[22px] cursor-pointer bg-[#AD0369] text-white text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center hover:bg-[#AD0369]/90 transition-colors">
                                        DESCARGAR
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" class="py-[18px]">
                                <div class="w-full h-px bg-[#E5E5E5]"></div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-20 text-center text-black/40 font-inter text-[14px]">
                                No hay listas de precios disponibles.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile --}}
        <div class="hidden max-[1199px]:grid gap-5">
            @forelse($precios as $precio)
            @php
                $extension = strtoupper(pathinfo($precio->archivo, PATHINFO_EXTENSION));
                $esExcel = in_array(strtolower($extension), ['xls', 'xlsx'], true);
                $verUrl = $esExcel ? route('cliente.precios.ver', $precio->id) : Storage::url($precio->archivo);
                $disk = Storage::disk('public');
                $peso = $disk->exists($precio->archivo)
                    ? round($disk->size($precio->archivo) / 1024) . ' kb'
                    : '-';
            @endphp
            <div class="bg-white border border-[#E5E5E5] rounded-[20px] p-4 transition-all duration-300">
                <div class="flex gap-4 mb-4">
                    <div class="w-[60px] h-[60px] flex-shrink-0 flex items-center justify-center rounded-[10px] bg-[#F8F8F8]">
                        <svg width="32" height="32" viewBox="0 0 43 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M25.0827 3.58337V10.75C25.0827 11.7004 25.4602 12.6118 26.1322 13.2838C26.8042 13.9558 27.7157 14.3334 28.666 14.3334H35.8327M17.916 16.125H14.3327M28.666 23.2917H14.3327M28.666 30.4584H14.3327M26.8743 3.58337H10.7493C9.79899 3.58337 8.88755 3.9609 8.21555 4.63291C7.54354 5.30491 7.16602 6.21635 7.16602 7.16671V35.8334C7.16602 36.7837 7.54354 37.6952 8.21555 38.3672C8.88755 39.0392 9.79899 39.4167 10.7493 39.4167H32.2493C33.1997 39.4167 34.1111 39.0392 34.7831 38.3672C35.4552 37.6952 35.8327 36.7837 35.8327 35.8334V12.5417L26.8743 3.58337Z" stroke="#AD0369" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-black font-inter text-[15px] font-semibold leading-[22px] mb-1 break-words">
                            {{ $precio->title }}
                        </h3>
                        <div class="flex items-center gap-4 text-[13px] text-black/50 font-inter">
                            <span>{{ $extension }}</span>
                            <span>{{ $peso }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-3 border-t border-[#E5E5E5]">
                    <a href="{{ $verUrl }}" target="_blank"
                       class="flex-1 h-[44px] rounded-[22px] border border-[#AD0369] bg-white text-[#AD0369] text-center font-inter text-[13px] font-normal uppercase flex items-center justify-center hover:bg-[#AD0369] hover:text-white transition-colors">
                        VER ONLINE
                    </a>
                    <button wire:click="descargar({{ $precio->id }})"
                            class="flex-1 h-[44px] rounded-[22px] cursor-pointer bg-[#AD0369] text-white text-center font-inter text-[13px] font-normal uppercase flex items-center justify-center hover:bg-[#AD0369]/90 transition-colors">
                        DESCARGAR
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center text-black/40 font-inter text-[14px] py-20">
                No hay listas de precios disponibles.
            </div>
            @endforelse
        </div>

    </div>
</div>
