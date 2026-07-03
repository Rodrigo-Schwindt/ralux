<div x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
<style>
/* ═══════════════════════════════════════════════════════════
   Zona Productos — responsive SOLO ≤ 1239px
   Nada de esto afecta 1240px para arriba.
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 1239px) {
    .zp-filterbar {
        height: auto !important;
        padding-top: 22px !important;
        padding-bottom: 22px !important;
    }
    .zp-desc {
        width: 100% !important;
    }
}
@media (max-width: 767px) {
    .zp-filterbar {
        padding-top: 16px !important;
        padding-bottom: 16px !important;
    }
}

/* Animación de entrada de filas */
@keyframes zp-row-in {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0);   }
}
.zp-row {
    animation: zp-row-in 0.32s cubic-bezier(.25,.46,.45,.94) both;
}
.zp-row > td {
    transition: background-color 0.2s ease, color 0.2s ease;
}
.zp-row:hover > td {
    background-color: rgba(173, 3, 105, 0.055);
}
.zp-row-divider > td {
    position: relative;
    overflow: hidden;
}
.zp-row-divider > td::before,
.zp-row-divider > td::after {
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
.zp-row-divider > td::before {
    top: 0;
}
.zp-row-divider > td::after {
    bottom: 0;
}
.zp-row:hover + .zp-row-divider > td::before {
    opacity: 1;
}
.zp-row-divider:has(+ .zp-row:hover) > td::after {
    opacity: 1;
}
.zp-table-start > td {
    transition: background-color 0.2s ease;
}
.zp-table-start:has(+ .zp-row:hover) > td {
    background-color: rgba(173, 3, 105, 0.055);
}
.zp-qty-control,
.zp-qty-control input,
.zp-qty-control button,
.zp-qty-control div {
    background-color: #fff;
}
</style>
    <div class="bg-[#222] py-[24px]">
        <div class="max-w-[1224px] mx-auto px-4 lg:px-0">
            <nav class="flex items-center gap-2 text-[13px] font-inter">
                <a wire:navigate href="/" class="text-white hover:text-white transition-colors font-bold">Inicio</a>
                <span class="text-white/40">/</span>
                <span class="text-white/60 ">Productos</span>
            </nav>
        </div>
    </div>

    <div class="zp-filterbar bg-[#222] h-[134px] flex items-center">
        <div class="max-w-[1224px] mx-auto w-full px-4 lg:px-0">
            <div class="flex flex-wrap lg:flex-nowrap gap-6 items-end">

                {{-- Categorías --}}
                <div class="flex-1 flex flex-col gap-1">
                    <label for="zona-tipo-sel" class="text-white text-[16px] font-normal leading-[150%]">Categorías</label>
                    <div
                        x-data="{
                            open: false, search: '', options: [],
                            init() {
                                this.loadOptions();
                                document.addEventListener('livewire:commit', () => {
                                    this.$nextTick(() => { this.loadOptions(); });
                                });
                            },
                            loadOptions() { if (!this.$refs.sel) return; this.options = Array.from(this.$refs.sel.options).map(o => ({ value: o.value, label: o.text })); },
                            get currentValue() { return String($wire.tipo_id ?? ''); },
                            get currentLabel() { if (!this.currentValue) return 'Seleccione categoría'; const f = this.options.find(o => String(o.value) === String(this.currentValue)); return f ? f.label : 'Seleccione categoría'; },
                            get filteredOptions() { if (!this.search) return this.options; const s = this.search.toLowerCase(); return this.options.filter(o => !o.value || o.label.toLowerCase().includes(s)); },
                            selectOption(value) { this.open = false; this.search = ''; $wire.set('tipo_id', value || null); }
                        }"
                        @click.outside="open = false"
                        class="relative"
                    >
                        <select id="zona-tipo-sel" x-ref="sel" wire:model.live="tipo_id" class="sr-only" tabindex="-1">
                            <option value="">Seleccione categoría</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->descripcion_es }}</option>
                            @endforeach
                        </select>
                        <button @click="open = !open" type="button"
                            class="w-full h-[45px] rounded-[20px] border border-[#B2B2B2] bg-transparent text-[14px] font-normal pl-4 pr-3 focus:outline-none cursor-pointer flex items-center justify-between gap-2">
                            <span x-text="currentLabel" :class="currentValue ? 'text-white' : 'text-[#B2B2B2]'" class="truncate text-left"></span>
                            <svg class="w-4 h-4 text-[#B2B2B2] flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute z-50 w-full mt-1 bg-white rounded-[10px] shadow-xl border border-[#AD0369] overflow-hidden">
                            <div class="p-2 border-b border-gray-100">
                                <input x-model="search" @click.stop type="text" placeholder="Buscar..." autocomplete="off"
                                    class="w-full px-3 py-2 text-sm border border-[#AD0369] rounded-[6px] text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#AD0369]">
                            </div>
                            <div class="max-h-[200px] overflow-y-auto flex flex-col">
                                <template x-for="opt in filteredOptions" :key="opt.value">
                                    <button type="button" @click="selectOption(opt.value)"
                                        class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                        :class="String(currentValue) === String(opt.value) ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'"
                                        x-text="opt.label"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Marca --}}
                <div class="flex-1 flex flex-col gap-1">
                    <label for="zona-marca-sel" class="text-white text-[16px] font-normal leading-[150%]">Marca</label>
                    <div
                        x-data="{
                            open: false, search: '', options: [],
                            init() {
                                this.loadOptions();
                                document.addEventListener('livewire:commit', () => {
                                    this.$nextTick(() => { this.loadOptions(); });
                                });
                            },
                            loadOptions() { if (!this.$refs.sel) return; this.options = Array.from(this.$refs.sel.options).map(o => ({ value: o.value, label: o.text })); },
                            get currentValue() { return String($wire.marca_id ?? ''); },
                            get currentLabel() { if (!this.currentValue) return 'Seleccione marca'; const f = this.options.find(o => String(o.value) === String(this.currentValue)); return f ? f.label : 'Seleccione marca'; },
                            get filteredOptions() { if (!this.search) return this.options; const s = this.search.toLowerCase(); return this.options.filter(o => !o.value || o.label.toLowerCase().includes(s)); },
                            selectOption(value) { this.open = false; this.search = ''; $wire.set('marca_id', value || null); }
                        }"
                        @click.outside="open = false"
                        class="relative"
                    >
                        <select id="zona-marca-sel" x-ref="sel" wire:model.live="marca_id" class="sr-only" tabindex="-1">
                            <option value="">Seleccione marca</option>
                            @foreach($marcas as $marca)
                                <option value="{{ $marca->id }}">{{ $marca->descripcion_es }}</option>
                            @endforeach
                        </select>
                        <button @click="open = !open" type="button"
                            class="w-full h-[45px] rounded-[20px] border border-[#B2B2B2] bg-transparent text-[14px] font-normal pl-4 pr-3 focus:outline-none cursor-pointer flex items-center justify-between gap-2">
                            <span x-text="currentLabel" :class="currentValue ? 'text-white' : 'text-[#B2B2B2]'" class="truncate text-left"></span>
                            <svg class="w-4 h-4 text-[#B2B2B2] flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute z-50 w-full mt-1 bg-white rounded-[10px] shadow-xl border border-[#AD0369] overflow-hidden">
                            <div class="p-2 border-b border-gray-100">
                                <input x-model="search" @click.stop type="text" placeholder="Buscar..." autocomplete="off"
                                    class="w-full px-3 py-2 text-sm border border-[#AD0369] rounded-[6px] text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#AD0369]">
                            </div>
                            <div class="max-h-[200px] overflow-y-auto flex flex-col">
                                <template x-for="opt in filteredOptions" :key="opt.value">
                                    <button type="button" @click="selectOption(opt.value)"
                                        class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                        :class="String(currentValue) === String(opt.value) ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'"
                                        x-text="opt.label"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modelo --}}
                <div class="flex-1 flex flex-col gap-1">
                    <label class="text-white text-[16px] font-normal leading-[150%]">Modelo</label>
                    <div
                        x-data="{
                            open: false, search: '',
                            get options() { return [{ value: '', label: 'Seleccione modelo' }, ...($wire.modelosOptions ?? [])]; },
                            get currentValue() { return String($wire.modelo_id ?? ''); },
                            get currentLabel() {
                                const f = this.options.find(o => String(o.value) === this.currentValue);
                                return f ? f.label : 'Seleccione modelo';
                            },
                            get filteredOptions() {
                                if (!this.search) return this.options;
                                const s = this.search.toLowerCase();
                                return this.options.filter(o => !o.value || o.label.toLowerCase().includes(s));
                            },
                            selectOption(value) {
                                $wire.set('modelo_id', value || null);
                                this.open = false;
                                this.search = '';
                            }
                        }"
                        @click.outside="open = false"
                        class="relative"
                    >
                        <button @click="open = !open" type="button"
                            class="w-full h-[45px] rounded-[20px] border border-[#B2B2B2] bg-transparent text-[14px] font-normal pl-4 pr-3 focus:outline-none cursor-pointer flex items-center justify-between gap-2">
                            <span x-text="currentLabel" :class="currentValue ? 'text-white' : 'text-[#B2B2B2]'" class="truncate text-left"></span>
                            <svg class="w-4 h-4 text-[#B2B2B2] flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute z-50 w-full mt-1 bg-white rounded-[10px] shadow-xl border border-[#AD0369] overflow-hidden">
                            <div class="p-2 border-b border-gray-100">
                                <input x-model="search" @click.stop type="text" placeholder="Buscar..." autocomplete="off"
                                    class="w-full px-3 py-2 text-sm border border-[#AD0369] rounded-[6px] text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#AD0369]">
                            </div>
                            <div class="max-h-[200px] overflow-y-auto flex flex-col">
                                <template x-for="opt in filteredOptions" :key="opt.value">
                                    <button type="button" @click="selectOption(opt.value)"
                                        class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                        :class="currentValue === String(opt.value) ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'"
                                        x-text="opt.label"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="zp-desc w-[395px] shrink-0 flex flex-col gap-1">
                    <label for="filtro-busqueda" class="text-white text-[16px] font-normal leading-[150%]">Descripción</label>
                    <input id="filtro-busqueda" type="text"
                           wire:model="busqueda"
                           placeholder="Código OM / Código Ralux / Descripción / Equivalencia"
                           class="w-full h-[45px] bg-transparent rounded-[20px] border border-[#B2B2B2] text-[#B2B2B2] placeholder-[#B2B2B2]/60 text-[14px] font-normal px-4 focus:outline-none focus:border-white/50">
                </div>

                <div class="flex-1 flex flex-col gap-1">
                    <button wire:click="limpiarFiltros" type="button"
                            class="text-white/60 underline text-[13px] text-center cursor-pointer hover:text-white/90 bg-transparent border-0 leading-[150%]">
                        Limpiar filtros
                    </button>
                    <button wire:click="buscar"
                            class="w-full h-[44px] flex justify-center items-center rounded-[22px] bg-[#AD0369] text-white text-[16px] font-normal leading-[150%] transition-all duration-300 hover:bg-[#AD0369]/90 cursor-pointer">
                        BUSCAR
                    </button>
                </div>

            </div>
        </div>
    </div>
    
    <div class="max-w-[1224px] mx-auto px-4 sm:px-6 lg:px-0 pt-[43px] pb-[88px]"
         x-data="{ animate: false }"
         x-init="setTimeout(() => animate = true, 200)"
         x-show="animate"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 transform translate-y-8"
         x-transition:enter-end="opacity-100 transform translate-y-0">
        @if($productos && $productos->count())
            <div class="overflow-x-auto rounded-t-[20px] -mx-4 sm:mx-0 px-4 sm:px-0">
                <table class="w-full border-collapse min-w-[1000px]">
                    <thead>
                        <tr class="bg-[#F5F5F5] h-[52px] rounded-t-[20px] text-[13px] sm:text-[14px] lg:text-[16px] text-[#222] font-inter font-semibold leading-normal">
                            <th class="text-left"></th>
                            <th class="pl-[12px] sm:pl-[22px] lg:w-[80px] text-left">Código</th>
                            <th class="pl-[12px] sm:pl-[22px] lg:w-[100px] text-left">Marca</th>
                            <th class="pl-[12px] sm:pl-[22px] text-left">Modelo</th>
                            <th class="text-left lg:w-[100px] ">Descripción</th>
                            <th class="text-left pl-[12px] sm:pl-[22px]">Precio</th>
                            {{-- <th class="text-left  sm:pl-[22px]">Descuento</th> --}}
                            <th class="text-right lg:w-[130px]">Precio con descuentos</th>
                            <th class="text-center pl-[12px] sm:pl-[40px] w-[100px] min-w-[100px]">Cantidad</th>
                            <th class="text-right lg:w-[110px]">Total</th>
                          
                            <th class="text-right pr-2 sm:pr-4"></th>
                        </tr>
                    </thead>
            
                    <tbody class="bg-white ">
                        <tr class="zp-table-start">
                            <td colspan="10" class="h-[18px]"></td>
                        </tr>
                        @foreach($productos as $producto)
                        @php
                        $descuentos = $this->calcularDescuentos($producto->precio, $producto);
                        $cantidad = max(1, (int) ($this->cantidades[$producto->id] ?? 1));
                    @endphp

    <tr class="zp-row h-[73px]" style="animation-delay: {{ $loop->index * 0.05 }}s">
<td wire:click="abrirDetalleProducto({{ $producto->id }})" class="zp-detail-cell w-[60px] sm:w-[80px] h-[60px] sm:h-[73px] max-[650px]:h-[60px] cursor-pointer">
    <div class="w-full h-full bg-white rounded-[10px] border border-[#D9D9D9] max-[650px]:h-[60px] overflow-hidden relative">
        <img
            src="{{ $producto->imagenPrincipal?->ruta ? asset('storage/'.$producto->imagenPrincipal->ruta) : asset('no-image.png') }}"
            class="w-full h-[73px] object-contain block max-[650px]:h-[60px]"
        >
        <div class="absolute inset-0 bg-black/10 pointer-events-none z-[1] transition-colors duration-300"></div>

    </div>
</td>

        <td wire:click="abrirDetalleProducto({{ $producto->id }})" class="zp-detail-cell pl-[12px] sm:pl-[22px] uppercase line-clamp-2 text-black/60 font-inter text-[13px] sm:text-[14px] lg:text-[14px] font-normal leading-[25px] justify-center flex items-center h-[80px] max-[650px]:h-[70px] cursor-pointer hover:text-[#AD0369] transition-colors">
            <span>{{ $producto->codigo_ralux }}</span>
            @if(\App\Support\CarritoIva::productoUsaIvaEspecial($producto, $config))
                <span class="ml-1.5 text-[#ad0369] text-[18px] leading-none" title="IVA especial">★</span>
            @endif
        </td>

        <td wire:click="abrirDetalleProducto({{ $producto->id }})" class="zp-detail-cell pl-[12px] sm:pl-[22px] text-black/60 font-inter text-[13px] sm:text-[14px] lg:text-[14px] font-normal leading-[25px] max-w-[120px] cursor-pointer hover:text-[#AD0369] transition-colors">
            {{ $producto->marcas->first()?->descripcion_es ?? '-' }}
        </td>

        <td wire:click="abrirDetalleProducto({{ $producto->id }})" class="zp-detail-cell pl-[12px] sm:pl-[22px] text-black/60 font-inter text-[13px] pr-2 sm:text-[14px] lg:text-[14px] font-normal leading-[25px] max-w-[120px] cursor-pointer hover:text-[#AD0369] transition-colors">
            {{ $producto->modelos->first()?->descripcion_es ?? '-' }}
        </td>

        <td wire:click="abrirDetalleProducto({{ $producto->id }})" class="zp-detail-cell pr-2 min-w-[120px] max-w-[160px] cursor-pointer">
            <div class="line-clamp-2 overflow-hidden text-black/60 font-inter text-[13px] sm:text-[14px] font-normal leading-[25px]">{!! $producto->descripcion_es !!}</div>
        </td>

        <td wire:click="abrirDetalleProducto({{ $producto->id }})" class="zp-detail-cell text-black/60  pl-[12px] sm:pl-[22px] font-inter text-[13px] sm:text-[14px] lg:text-[16px] font-normal leading-normal cursor-pointer hover:text-[#AD0369] transition-colors">
            ${{ number_format($descuentos['precio_original'], 2, ',', '.') }}
        </td>

{{-- <td class="pl-[12px] sm:pl-[22px] text-black/60  text-left font-inter text-[13px] sm:text-[14px] lg:text-[14px] font-normal leading-normal ">
    @if(empty($descuentos['partes']))
        <span class="text-black/30">—</span>
    @else
        {{ implode('%+', array_map(fn($p) => number_format($p, 0, ',', '.'), $descuentos['partes'])) }}%
    @endif
</td> --}}

        <td wire:click="abrirDetalleProducto({{ $producto->id }})" class="zp-detail-cell text-black/60 text-right font-inter text-[13px] sm:text-[14px] lg:text-[16px] pl-4 font-normal leading-normal cursor-pointer hover:text-[#AD0369] transition-colors">
            ${{ number_format($descuentos['precio_final'], 2, ',', '.') }}
        </td>

        <td class="pl-[12px] sm:pl-[40px] text-center align-middle w-[112px] min-w-[112px]">
            <div class="zp-qty-control inline-flex text-center justify-between items-center border border-[#EEE] overflow-hidden h-[36px] w-[112px] min-w-[112px] bg-white">
                <input
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="3"
                    aria-label="Cantidad"
                    x-on:input="$el.value = $el.value.replace(/\D/g, '').slice(0, 3)"
                    class="w-[54px] text-center text-black/60 text-sm focus:outline-none bg-white"
                    wire:model.live.debounce.400ms="cantidades.{{ $producto->id }}"
                >
        
                <div class="flex flex-col">
                    <button
                        type="button"
                        wire:click="incrementar({{ $producto->id }})"
                        class="w-[24px] h-[18px] flex items-center justify-center text-black/60 hover:bg-gray-100 text-xs rotate-180"
                    >
<svg xmlns="http://www.w3.org/2000/svg" width="12"  height="10" viewBox="0 0 12 10" fill="none">
  <g clip-path="url(#clip0_3409_5040)">
    <path d="M6.00098 5.2085C5.98689 5.2085 5.97591 5.20518 5.96973 5.20264L1.32715 -1.49951L10.6748 -1.49951L6.03223 5.20264C6.02618 5.20521 6.0153 5.20846 6.00098 5.2085Z" stroke="#939393"/>
  </g>
  <defs>
    <clipPath id="clip0_3409_5040">
      <rect width="12" height="10" fill="white" />
    </clipPath>
  </defs>
</svg>
                    </button>
        
                    <button
                        type="button"
                        wire:click="decrementar({{ $producto->id }})"
                        class="w-[24px] h-[18px] flex items-center justify-center   text-black/60 hover:bg-gray-100 text-xs"
                    >
<svg xmlns="http://www.w3.org/2000/svg" width="12" height="10" viewBox="0 0 12 10" fill="none">
  <g clip-path="url(#clip0_3409_5040)">
    <path d="M6.00098 5.2085C5.98689 5.2085 5.97591 5.20518 5.96973 5.20264L1.32715 -1.49951L10.6748 -1.49951L6.03223 5.20264C6.02618 5.20521 6.0153 5.20846 6.00098 5.2085Z" stroke="#939393"/>
  </g>
  <defs>
    <clipPath id="clip0_3409_5040">
      <rect width="12" height="10" fill="white" transform="translate(12 10) rotate(180)"/>
    </clipPath>
  </defs>
</svg>
                    </button>
                </div>
            </div>
        </td>

        <td class="text-black/60 text-right font-inter text-[13px] sm:text-[14px] lg:text-[16px] pl-4 font-semibold leading-normal">
            ${{ number_format($descuentos['precio_final'] * $cantidad, 2, ',', '.') }}
        </td>
        
      

        <td class="px-2 sm:px-4 align-middle text-right">
            <button 
                wire:click="agregarAlCarrito({{ $producto->id }})"
                class="ml-auto rounded-[22px] cursor-pointer border border-[#AD0369] w-[40px] sm:w-[57px] h-[35px] sm:h-[44px] text-[#AD0369] justify-center flex items-center hover:bg-[#AD0369] hover:text-white transition-colors">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M2.0498 2.05H4.0498L6.7098 14.47C6.80738 14.9249 7.06048 15.3315 7.42552 15.6199C7.79056 15.9082 8.24471 16.0603 8.7098 16.05H18.4898C18.945 16.0493 19.3863 15.8933 19.7408 15.6078C20.0954 15.3224 20.3419 14.9245 20.4398 14.48L22.0898 7.05H5.1198M8.9998 21C8.9998 21.5523 8.55209 22 7.9998 22C7.44752 22 6.9998 21.5523 6.9998 21C6.9998 20.4477 7.44752 20 7.9998 20C8.55209 20 8.9998 20.4477 8.9998 21ZM19.9998 21C19.9998 21.5523 19.5521 22 18.9998 22C18.4475 22 17.9998 21.5523 17.9998 21C17.9998 20.4477 18.4475 20 18.9998 20C19.5521 20 19.9998 20.4477 19.9998 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
            </button>
        </td>
    </tr>
    
    <tr class="zp-row-divider">
        <td colspan="10" class="py-[18px]">
            <div class="w-full h-px bg-[#E5E5E5]"></div>
        </td>
    </tr>
@endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-[48px]">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-[24px]">
                    <div class="text-[#666] font-inter text-[13px] sm:text-[14px] text-center sm:text-left">
                        Mostrando 
                        <span class="font-semibold text-black">{{ $productos->firstItem() }}</span> 
                        a 
                        <span class="font-semibold text-black">{{ $productos->lastItem() }}</span> 
                        de 
                        <span class="font-semibold text-black">{{ $productos->total() }}</span> 
                        productos
                    </div>

                    <div class="flex items-center gap-[12px]">
                        <span class="text-[#666] font-inter text-[13px] sm:text-[14px]">Mostrar:</span>
                        <select wire:model.live="perPage" class="h-[36px] px-[12px] rounded-[4px] border border-[#E5E5E5] bg-white font-inter text-[13px] sm:text-[14px] cursor-pointer">
                            <option value="12">12</option>
                            <option value="24">24</option>
                            <option value="48">48</option>
                            <option value="96">96</option>
                        </select>
                    </div>
                </div>

                @if ($productos->hasPages())
                    <div class="flex items-center justify-center gap-[6px] sm:gap-[8px] flex-wrap">
                        @if ($productos->onFirstPage())
                            <button disabled class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 12L4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        @else
                            <button wire:click="gotoPage(1)" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 12L4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        @endif

                        @if ($productos->onFirstPage())
                            <button disabled class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        @else
                            <button wire:click="previousPage" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        @endif

                        @php
                            $currentPage = $productos->currentPage();
                            $lastPage = $productos->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

                        @if ($startPage > 1)
                            <button wire:click="gotoPage(1)" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors font-inter text-[13px] sm:text-[14px]">
                                1
                            </button>
                            @if ($startPage > 2)
                                <span class="text-[#666] font-inter text-[13px] sm:text-[14px]">...</span>
                            @endif
                        @endif

                        @for ($page = $startPage; $page <= $endPage; $page++)
                            @if ($page == $currentPage)
                                <button class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] bg-[#AD0369] text-white font-inter text-[13px] sm:text-[14px] font-semibold">
                                    {{ $page }}
                                </button>
                            @else
                                <button wire:click="gotoPage({{ $page }})" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors font-inter text-[13px] sm:text-[14px]">
                                    {{ $page }}
                                </button>
                            @endif
                        @endfor

                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span class="text-[#666] font-inter text-[13px] sm:text-[14px]">...</span>
                            @endif
                            <button wire:click="gotoPage({{ $lastPage }})" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors font-inter text-[13px] sm:text-[14px]">
                                {{ $lastPage }}
                            </button>
                        @endif

                        @if ($productos->hasMorePages())
                            <button wire:click="nextPage" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        @else
                            <button disabled class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        @endif

                        @if ($productos->hasMorePages())
                            <button wire:click="gotoPage({{ $productos->lastPage() }})" class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#AD0369] hover:text-white hover:border-[#AD0369] transition-colors">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        @else
                            <button disabled class="w-[32px] sm:w-[36px] h-[32px] sm:h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        @else
            <div class="text-center text-[#777] py-20 text-[14px] sm:text-[16px]">
                No se encontraron productos
            </div>
        @endif
    </div>

    @include('livewire.zona.partials.producto-detalle-modal', ['productoDetalle' => $productoDetalle])

    @if(count($productosEnOferta) > 0 && $mostrarModalOfertas)
@php
    $producto = $productosEnOferta[$productoActualModal];
    $precio = $producto['precio'];
    $descuento = $producto['descuento'] ?? 0;
    
    if ($descuento <= 1) {
        $porcentajeDescuento = $descuento * 100;
        $precioFinal = $precio - ($precio * $descuento);
    } elseif ($descuento <= 100) {
        $porcentajeDescuento = $descuento;
        $precioFinal = $precio - ($precio * ($descuento / 100));
    } else {
        $porcentajeDescuento = ($precio > 0) ? ($descuento / $precio) * 100 : 0;
        $precioFinal = $precio - $descuento;
    }
@endphp

<div 
    x-data="{ 
        show: false,
        intervalo: null,
        iniciarCarrusel() {
            this.intervalo = setInterval(() => {
                @this.call('siguienteProductoModal');
            }, 4000);
        },
        detenerCarrusel() {
            if (this.intervalo) {
                clearInterval(this.intervalo);
            }
        }
    }"
    x-init="setTimeout(() => { show = true; iniciarCarrusel(); }, 2000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
>
    <div 
        class="fixed inset-0 bg-gradient-to-b from-transparent to-white to-20%"
        x-show="show"
        x-transition:enter="transition-opacity ease-out duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        @click="detenerCarrusel(); show = false; setTimeout(() => $wire.cerrarModalOfertas(), 300)"
        style="background: rgba(0, 0, 0, 0.4);"
    ></div>
    
    <div class="flex items-center justify-center min-h-screen px-4">
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 -translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-10"
            class="relative bg-white rounded-[4px] max-w-[90%] sm:max-w-[680px] lg:max-w-[780px] w-full max-h-[90vh] shadow-2xl overflow-hidden cursor-pointer"
            @click.stop="@this.call('siguienteProductoModal')"
        >
            <button 
                @click.stop="detenerCarrusel(); show = false; setTimeout(() => $wire.cerrarModalOfertas(), 300)"
                class="absolute top-2 right-2 sm:right-4 z-10 w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center bg-white cursor-pointer rounded-full transition-colors"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2 h-auto md:h-[540px]">
                <div class="relative bg-[linear-gradient(90deg,#AD036980_0.22%,#FFF_80.76%)] h-[200px] md:h-[540px]">
                    <img src="/oferta.png" alt="Patrón Hexagonal" class="h-[540px] w-full object-cover ">
                </div>

                <div class="p-4 sm:p-2 md:p-2 flex flex-col justify-center relative">
                    <div 
                        wire:key="producto-{{ $productoActualModal }}"
                        x-data="{ visible: false }"
                        x-init="setTimeout(() => { visible = true }, 50)"
                        x-show="visible"
                        x-transition:enter="transition ease-out duration-400"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="w-full"
                    >
                        <div class="text-center mb-4">
                            <h2 class="text-[#AD0369] text-center font-inter text-[20px] sm:text-[24px] font-semibold leading-[25px]">OFERTA</h2>
                            <p class="text-black text-center font-inter text-[11px] sm:text-[12px] font-normal leading-[25px]">
                                HASTA {{ \Carbon\Carbon::parse($producto['oferta_fin'])->format('d/m/Y') }}
                            </p>
                        </div>

                        <div class="text-center mb-2">
                            <h3 class="text-black text-center font-inter text-[28px] sm:text-[36px] lg:text-[40px] font-semibold leading-[25px] pt-[11px]">
                                {{ $producto['code'] ?? 'N/A' }}
                            </h3>
                        </div>

                        <div class="flex justify-center py-2 sm:py-3 lg:pt-[16px]">
                            <img 
                                src="{{ $producto['image'] ? asset('storage/' . $producto['image']) : asset('no-image.png') }}" 
                                alt="{{ $producto['title'] }}"
                                class="max-h-[100px] sm:max-h-[120px] lg:max-h-[140px] object-contain"
                            >
                        </div>

                        <div class="text-center mb-2">
                            <p class="text-[#AD0369] text-center font-inter text-[14px] sm:text-[16px] font-extrabold leading-[25px]">
                                {{ round($porcentajeDescuento) }}% de descuento
                            </p>
                        </div>

                        <div class="text-center mb-6">
                            <p class="text-slate-600 text-center font-inter text-[12px] sm:text-[13px] font-normal leading-[20px] line-through">
                                ${{ number_format($precio, 2, ',', '.') }}
                            </p>
                            <p class="text-[#AD0369] text-center font-inter text-[20px] sm:text-[24px] font-bold leading-[25px]">
                                ${{ number_format($precioFinal, 2, ',', '.') }}
                            </p>
                        </div>

                        <button
                            @click.stop="$wire.agregarAlCarritoDesdeModal({{ $producto['id'] }})"
                            class="w-full sm:w-[164px] h-[44px] rounded-[4px] bg-[#AD0369] text-white text-center font-inter text-[13px] sm:text-[14px] font-normal leading-normal uppercase mx-auto flex items-center justify-center hover:bg-[#B30034] transition-colors"
                        >
                            AGREGAR A CARRITO
                        </button>

                        @if(count($productosEnOferta) > 1)
                        <div class="flex justify-center items-center gap-4 mt-6">
                            <div class="flex gap-2">
                                @foreach($productosEnOferta as $index => $prod)
                                <div 
                                    @click.stop="$wire.set('productoActualModal', {{ $index }})"
                                    class="w-2 h-2 rounded-full cursor-pointer transition-all {{ $index === $productoActualModal ? 'bg-[#AD0369] scale-125' : 'bg-gray-300 hover:bg-gray-400' }}"
                                ></div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div
    x-data="{
        show: false, title: '', message: '', type: 'success', timer: null,
        init() {
            @if(session('toast'))
                setTimeout(() => this.lanzar('{{ session('toast.message') }}', '{{ session('toast.type', 'success') }}'), 100);
            @endif
            Livewire.on('producto-agregado', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                this.lanzar(payload.message || 'Operación exitosa', payload.type || 'success');
            });
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoriaSelect = document.getElementById('categoriaSelect');
    const arrowCategoria = document.getElementById('arrowCategoria');
    const marcaSelect = document.getElementById('marcaSelect');
    const arrowMarca = document.getElementById('arrowMarca');

    if (categoriaSelect && arrowCategoria) {
        categoriaSelect.addEventListener('click', function() {
            const isRotated = arrowCategoria.style.transform.includes('rotate(180deg)');
            arrowCategoria.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
        });
    }
    
    if (marcaSelect && arrowMarca) {
        marcaSelect.addEventListener('click', function() {
            const isRotated = arrowMarca.style.transform.includes('rotate(180deg)');
            arrowMarca.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
        });
    }
    
    document.addEventListener('livewire:navigated', function() {
        const modeloSelect = document.getElementById('modeloSelect');
        const arrowModelo = document.getElementById('arrowModelo');
        
        if (modeloSelect && arrowModelo) {
            modeloSelect.addEventListener('click', function() {
                const isRotated = arrowModelo.style.transform.includes('rotate(180deg)');
                arrowModelo.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        }
    });
});
</script>
</div>
