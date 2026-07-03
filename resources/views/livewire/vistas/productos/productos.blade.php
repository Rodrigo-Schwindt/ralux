<div class="w-full">
<style>
@media (max-width: 1239px) {
    .prod-filterbar {
        height: auto !important;
        padding-top: 22px !important;
        padding-bottom: 22px !important;
    }
    .prod-desc-input {
        width: 100% !important;
    }
}
@media (max-width: 767px) {
    .prod-filterbar {
        padding-top: 18px !important;
        padding-bottom: 18px !important;
    }
    .prod-section {
        padding-top: 40px !important;
        padding-bottom: 48px !important;
    }
    .prod-grid {
        gap: 16px !important;
    }
    .prod-card .prod-detalle-btn {
        align-self: center !important;
    }
}

/* Animación de entrada de tarjetas */
@media (min-width: 1240px) {
    .prod-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    }
}

@keyframes prod-card-up {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}
.prod-card {
    animation: prod-card-up 0.45s cubic-bezier(.25,.46,.45,.94) both;
}
.prod-card:nth-child(1)  { animation-delay: 0.05s; }
.prod-card:nth-child(2)  { animation-delay: 0.11s; }
.prod-card:nth-child(3)  { animation-delay: 0.17s; }
.prod-card:nth-child(4)  { animation-delay: 0.23s; }
.prod-card:nth-child(5)  { animation-delay: 0.29s; }
.prod-card:nth-child(6)  { animation-delay: 0.35s; }
.prod-card:nth-child(7)  { animation-delay: 0.41s; }
.prod-card:nth-child(8)  { animation-delay: 0.47s; }
.prod-card:nth-child(9)  { animation-delay: 0.53s; }
.prod-card:nth-child(10) { animation-delay: 0.59s; }
.prod-card:nth-child(11) { animation-delay: 0.65s; }
.prod-card:nth-child(12) { animation-delay: 0.71s; }
</style>
    <div wire:key="productos-page-wrapper">
    <div wire:key="productos-header" class="bg-[#222] py-[24px]">
        <div class="max-w-[1224px] mx-auto px-4 lg:px-0">
            <nav class="flex items-center gap-2 text-[13px] font-inter">
                <a wire:navigate href="/" class="text-white hover:text-white transition-colors font-bold">Inicio</a>
                <span class="text-white/40">/</span>
                <span class="text-white/60 ">Productos</span>
            </nav>
        </div>
    </div>

    <div class="prod-filterbar bg-[#222] h-[134px] flex items-center relative z-10">
        <div class="max-w-[1224px] mx-auto w-full px-4 lg:px-0">
            <div class="flex flex-wrap lg:flex-nowrap gap-6 items-end">

                <div class="flex-1 flex flex-col gap-1">
                    <label for="prod-tipo-sel" class="text-white text-[16px] font-normal leading-[150%]">Categorías</label>
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
                        <select id="prod-tipo-sel" x-ref="sel" wire:model.live="tipo_id" class="sr-only" tabindex="-1">
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
                    <label for="prod-marca-sel" class="text-white text-[16px] font-normal leading-[150%]">Marca</label>
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
                        <select id="prod-marca-sel" x-ref="sel" wire:model.live="marca_id" class="sr-only" tabindex="-1">
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
                            get options() { return [{ value: '', label: 'Seleccione modelo' }, ...(Array.isArray($wire.modelosOptions) ? $wire.modelosOptions : [])]; },
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

                <div class="prod-desc-input w-[395px] shrink-0 flex flex-col gap-1">
                    <label for="filtro-busqueda" class="text-white text-[16px] font-normal leading-[150%]">Descripción</label>
                    <input id="filtro-busqueda" type="text"
                           wire:model="busqueda"
                           wire:keydown.enter="buscar"
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

    <div class="prod-section bg-white pt-[80px] pb-[76px]">
        <div class="max-w-[1224px] mx-auto px-4 lg:px-0">

            @if($productos->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 text-center">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="text-gray-500 font-inter text-[16px]">No se encontraron productos con los filtros seleccionados.</p>
                </div>
            @else
                @php
                    $filterQuery = http_build_query(array_filter([
                        'tipo_id'   => $tipo_id,
                        'marca_id'  => $marca_id,
                        'modelo_id' => $modelo_id,
                        'busqueda'  => $busqueda,
                    ], fn($v) => $v !== null && $v !== ''));
                @endphp
                <div class="prod-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[24px]">
                    @foreach($productos as $producto)
                    <div wire:key="prod-{{ $producto->id }}" class="prod-card bg-white rounded-[10px] border border-[#DEDFE0] hover:shadow-sm transition-shadow overflow-hidden flex flex-col min-h-[440px]">

                        <div class=" h-[237px] flex items-center p-2 justify-center overflow-hidden flex-shrink-0 relative">
                            @if($producto->imagenPrincipal)
                                <img src="{{ Storage::url($producto->imagenPrincipal->ruta) }}"
                                     alt="{!! $producto->descripcion_es !!}"
                                     class="w-full h-full object-contain">
                            @else
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif
                            <div class="absolute inset-0 bg-black/10 pointer-events-none z-[1] transition-colors duration-300"></div>
                            @if($producto->tipo)
                                <span class="absolute bottom-2 left-2 text-[#AD0369] text-[14px] font-bold leading-[150%] uppercase px-2 py-1 rounded-sm z-[2]">
                                    {{ $producto->tipo->descripcion_es }}
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-col flex-1 pt-[16px] pb-[23px] px-[16px]">

                            <div class="flex justify-between items-center pb-[18px]">
                                <span class="text-[#222] text-[14px] font-bold leading-[150%]">
                                    {{ $producto->codigo_ralux }}
                                </span>
                                <span class="text-[#222] text-right text-[13px] font-normal leading-[150%] uppercase">
                                    {{ $producto->marcas->first()?->descripcion_es ?? '' }}
                                </span>
                            </div>

                            <p class="text-black text-[18px] font-normal leading-[22px] line-clamp-2">
                                {!! $producto->descripcion_es !!}
                            </p>

                            <a wire:navigate href="{{ route('productos.detalle', $producto->id) }}{{ $filterQuery ? '?' . $filterQuery : '' }}"
                               class="prod-detalle-btn flex w-full max-w-[253px] h-[36px] py-[11px] px-[26px] justify-center items-center rounded-[22px] border border-[#AD0369] bg-white text-[#AD0369] text-center text-[12px] font-normal leading-[150%] mt-auto self-start hover:bg-[#AD0369] hover:text-white transition-colors">
                                VER DETALLE
                            </a>

                        </div>
                    </div>
                    @endforeach
                </div>

@if($hayMas)
<div class="mt-10 flex justify-center">
    <button wire:click="cargarMas"
            wire:loading.attr="disabled"
            class="flex items-center justify-center gap-2 border border-[#AD0369] text-[#AD0369] text-[16px] font-normal rounded-full px-[26px] py-[11px] hover:bg-[#AD0369] hover:text-white transition-colors cursor-pointer disabled:opacity-50">

        <span wire:loading.remove wire:target="cargarMas">
            CARGAR MÁS PRODUCTOS
        </span>

        <div wire:loading.flex wire:target="cargarMas" class="items-center gap-2">
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Cargando...</span>
        </div>

    </button>
</div>
@endif

            @endif
        </div>
    </div>
    </div>
</div>

@script
<script>
    function saveFilters() {
        sessionStorage.setItem('raluxFilters', JSON.stringify({
            tipo_id:   $wire.tipo_id   ?? '',
            marca_id:  $wire.marca_id  ?? '',
            modelo_id: $wire.modelo_id ?? '',
            busqueda:  $wire.busqueda  ?? '',
        }));
    }
    $wire.$watch('tipo_id',   saveFilters);
    $wire.$watch('marca_id',  saveFilters);
    $wire.$watch('modelo_id', saveFilters);
    $wire.$watch('busqueda',  saveFilters);
    saveFilters();
</script>
@endscript
