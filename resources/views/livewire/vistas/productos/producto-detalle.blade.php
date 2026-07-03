@php
    $lightboxItems = $producto->imagenes
        ->map(fn($imagen) => [
            'ruta' => $imagen->ruta,
            'tipo' => $imagen->tipo,
            'alt' => $imagen->alt ?: $producto->descripcion_es,
        ])
        ->values();

    if ($producto->imagen_diagrama) {
        $lightboxItems->push([
            'ruta' => $producto->imagen_diagrama,
            'tipo' => 'diagrama',
            'alt' => 'Diagrama dimensional',
        ]);
    }

    if ($producto->diagrama_orientativo) {
        $lightboxItems->push([
            'ruta' => $producto->diagrama_orientativo,
            'tipo' => 'diagrama',
            'alt' => 'Diagrama orientativo',
        ]);
    }

    $lightboxCount = $lightboxItems->count();
    $indiceActual = $lightboxItems->search(fn($item) => $item['ruta'] === $imagenActual);
    $indiceActual = $indiceActual === false ? 0 : $indiceActual;
    $diagramaIndex = $lightboxItems->search(fn($item) => $item['ruta'] === $producto->imagen_diagrama);
    $orientativoIndex = $lightboxItems->search(fn($item) => $item['ruta'] === $producto->diagrama_orientativo);

    $filterData = [
        'tipos' => $tipos->map(fn($t) => [
            'value' => (string) $t->id,
            'label' => $t->descripcion_es,
        ])->values()->all(),
        'marcas' => $marcas->map(fn($m) => [
            'value' => (string) $m->id,
            'label' => $m->descripcion_es,
        ])->values()->all(),
        'allModelos' => $allModelos->map(fn($m) => [
            'value' => (string) $m->id,
            'label' => $m->descripcion_es,
            'marca_id' => (string) $m->marca_id,
            'marca_ids' => $m->marca_ids ?? [(string) $m->marca_id],
        ])->values()->all(),
    ];
@endphp

<div
    x-data="{
        show: false,
        showModal: false,
        currentIndex: {{ $indiceActual }},
        totalImages: {{ $lightboxCount }},
        showingDiagram: @js($imagenActual === $producto->imagen_diagrama ? 'dimensional' : ($imagenActual === $producto->diagrama_orientativo ? 'orientativo' : null))
    }"
    x-init="setTimeout(() => show = true, 50)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    class="w-full">

    {{-- Filter bar data --}}
    <script>
        window._raluxFilterData = @js($filterData);
    </script>

    {{-- Filter bar --}}
    <div
        x-data="{
            tipoOpen: false,  tipoSearch: '',
            marcaOpen: false, marcaSearch: '',
            modeloOpen: false, modeloSearch: '',
            tipo_id: '', marca_id: '', modelo_id: '', busqueda: '',
            tipos:      window._raluxFilterData.tipos,
            marcas:     window._raluxFilterData.marcas,
            allModelos: window._raluxFilterData.allModelos,
            init() {
                const p = new URLSearchParams(window.location.search);
                const t = p.get('tipo_id'), m = p.get('marca_id'),
                      mo = p.get('modelo_id'), b = p.get('busqueda');
                if (t || m || mo || b) {
                    this.tipo_id   = t  || '';
                    this.marca_id  = m  || '';
                    this.modelo_id = mo || '';
                    this.busqueda  = b  || '';
                } else {
                    try {
                        const f = JSON.parse(sessionStorage.getItem('raluxFilters') || '{}');
                        this.tipo_id   = String(f.tipo_id   || '');
                        this.marca_id  = String(f.marca_id  || '');
                        this.modelo_id = String(f.modelo_id || '');
                        this.busqueda  = String(f.busqueda  || '');
                    } catch(e) {}
                }
            },
            get modelosDisponibles() {
                return this.marca_id
                    ? this.allModelos.filter(m => (m.marca_ids || [m.marca_id]).includes(this.marca_id))
                    : this.allModelos;
            },
            get tipoLabel()   { const f = this.tipos.find(t => t.value === this.tipo_id);   return f ? f.label : 'Seleccione categoría'; },
            get marcaLabel()  { const f = this.marcas.find(m => m.value === this.marca_id); return f ? f.label : 'Seleccione marca'; },
            get modeloLabel() { const f = this.allModelos.find(m => m.value === this.modelo_id); return f ? f.label : 'Seleccione modelo'; },
            get tiposFiltrados()    { if (!this.tipoSearch)   return this.tipos;   const s = this.tipoSearch.toLowerCase();   return this.tipos.filter(t => t.label.toLowerCase().includes(s)); },
            get marcasFiltradas()   { if (!this.marcaSearch)  return this.marcas;  const s = this.marcaSearch.toLowerCase();  return this.marcas.filter(m => m.label.toLowerCase().includes(s)); },
            get modelosFiltrados()  { const base = this.modelosDisponibles; if (!this.modeloSearch) return base; const s = this.modeloSearch.toLowerCase(); return base.filter(m => m.label.toLowerCase().includes(s)); },
            selectTipo(v)   { this.tipo_id  = v; this.tipoOpen   = false; this.tipoSearch   = ''; },
            selectMarca(v)  {
                this.marca_id = v; this.marcaOpen  = false; this.marcaSearch  = '';
                if (this.modelo_id) { const m = this.allModelos.find(m => m.value === this.modelo_id); if (m && !(m.marca_ids || [m.marca_id]).includes(v)) this.modelo_id = ''; }
            },
            selectModelo(v) {
                this.modelo_id = v; this.modeloOpen = false; this.modeloSearch = '';
                if (v) { const m = this.allModelos.find(m => m.value === v); if (m) this.marca_id = m.marca_id; }
            },
            persistirFiltros() {
                sessionStorage.setItem('raluxFilters', JSON.stringify({
                    tipo_id: this.tipo_id || '',
                    marca_id: this.marca_id || '',
                    modelo_id: this.modelo_id || '',
                    busqueda: this.busqueda || '',
                }));
            },
            limpiar() {
                this.tipo_id = ''; this.marca_id = ''; this.modelo_id = ''; this.busqueda = '';
                this.persistirFiltros();
            },
            buscar()  {
                const p = new URLSearchParams();
                if (this.tipo_id)  p.set('tipo_id',   this.tipo_id);
                if (this.marca_id) p.set('marca_id',  this.marca_id);
                if (this.modelo_id) p.set('modelo_id', this.modelo_id);
                if (this.busqueda) p.set('busqueda',  this.busqueda);
                this.persistirFiltros();
                const url = '/productos' + (p.toString() ? '?' + p.toString() : '');
                if (typeof Livewire !== 'undefined' && Livewire.navigate) { Livewire.navigate(url); } else { window.location.href = url; }
            }
        }"
        class="prod-filterbar bg-[#222] relative z-10 pt-[24px] pb-[34px]"
    >
        <div class="max-w-[1224px] mx-auto w-full px-4 lg:px-0">
            <nav class="flex items-center gap-1 text-[13px] font-inter mb-14">
                <a wire:navigate href="/" class="text-white hover:text-white transition-colors font-bold">Inicio</a>
                <span class="text-white/40">/</span>
                <a wire:navigate href="/productos" class="text-white hover:text-white transition-colors font-bold">Productos</a>
                <span class="text-white/40">/</span>
                @if($producto->tipo)
                    <a wire:navigate href="/productos?tipo_id={{ $producto->tipo->id }}" class="text-white hover:text-white transition-colors font-bold">{{ $producto->tipo->descripcion_es }}</a>
                    <span class="text-white/40">/</span>
                @endif
                <span class="text-white/70">{!! $producto->descripcion_es !!}</span>
            </nav>

            <div class="flex flex-wrap lg:flex-nowrap gap-6 items-end">

                {{-- Categorías --}}
                <div class="flex-1 flex flex-col gap-1" @click.outside="tipoOpen = false">
                    <label class="text-white text-[16px] font-normal leading-[150%]">Categorías</label>
                    <div class="relative">
                        <button @click="tipoOpen = !tipoOpen" type="button"
                            class="w-full h-[45px] rounded-[20px] border border-[#B2B2B2] bg-transparent text-[14px] font-normal pl-4 pr-3 focus:outline-none cursor-pointer flex items-center justify-between gap-2">
                            <span :class="tipo_id ? 'text-white' : 'text-[#B2B2B2]'" x-text="tipoLabel" class="truncate text-left"></span>
                            <svg class="w-4 h-4 text-[#B2B2B2] flex-shrink-0 transition-transform duration-200" :class="tipoOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="tipoOpen" x-cloak class="absolute z-50 w-full mt-1 bg-white rounded-[10px] shadow-xl border border-[#AD0369] overflow-hidden">
                            <div class="p-2 border-b border-gray-100">
                                <input x-model="tipoSearch" @click.stop type="text" placeholder="Buscar..." autocomplete="off"
                                    class="w-full px-3 py-2 text-sm border border-[#AD0369] rounded-[6px] text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#AD0369]">
                            </div>
                            <div class="max-h-[200px] overflow-y-auto flex flex-col">
                                <button type="button" @click="selectTipo('')"
                                    class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                    :class="!tipo_id ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'">Seleccione categoría</button>
                                <template x-for="opt in tiposFiltrados" :key="opt.value">
                                    <button type="button" @click="selectTipo(opt.value)"
                                        class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                        :class="tipo_id === opt.value ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'"
                                        x-text="opt.label"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Marca --}}
                <div class="flex-1 flex flex-col gap-1" @click.outside="marcaOpen = false">
                    <label class="text-white text-[16px] font-normal leading-[150%]">Marca</label>
                    <div class="relative">
                        <button @click="marcaOpen = !marcaOpen" type="button"
                            class="w-full h-[45px] rounded-[20px] border border-[#B2B2B2] bg-transparent text-[14px] font-normal pl-4 pr-3 focus:outline-none cursor-pointer flex items-center justify-between gap-2">
                            <span :class="marca_id ? 'text-white' : 'text-[#B2B2B2]'" x-text="marcaLabel" class="truncate text-left"></span>
                            <svg class="w-4 h-4 text-[#B2B2B2] flex-shrink-0 transition-transform duration-200" :class="marcaOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="marcaOpen" x-cloak class="absolute z-50 w-full mt-1 bg-white rounded-[10px] shadow-xl border border-[#AD0369] overflow-hidden">
                            <div class="p-2 border-b border-gray-100">
                                <input x-model="marcaSearch" @click.stop type="text" placeholder="Buscar..." autocomplete="off"
                                    class="w-full px-3 py-2 text-sm border border-[#AD0369] rounded-[6px] text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#AD0369]">
                            </div>
                            <div class="max-h-[200px] overflow-y-auto flex flex-col">
                                <button type="button" @click="selectMarca('')"
                                    class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                    :class="!marca_id ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'">Seleccione marca</button>
                                <template x-for="opt in marcasFiltradas" :key="opt.value">
                                    <button type="button" @click="selectMarca(opt.value)"
                                        class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                        :class="marca_id === opt.value ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'"
                                        x-text="opt.label"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modelo --}}
                <div class="flex-1 flex flex-col gap-1" @click.outside="modeloOpen = false">
                    <label class="text-white text-[16px] font-normal leading-[150%]">Modelo</label>
                    <div class="relative">
                        <button @click="modeloOpen = !modeloOpen" type="button"
                            class="w-full h-[45px] rounded-[20px] border border-[#B2B2B2] bg-transparent text-[14px] font-normal pl-4 pr-3 focus:outline-none cursor-pointer flex items-center justify-between gap-2">
                            <span :class="modelo_id ? 'text-white' : 'text-[#B2B2B2]'" x-text="modeloLabel" class="truncate text-left"></span>
                            <svg class="w-4 h-4 text-[#B2B2B2] flex-shrink-0 transition-transform duration-200" :class="modeloOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="modeloOpen" x-cloak class="absolute z-50 w-full mt-1 bg-white rounded-[10px] shadow-xl border border-[#AD0369] overflow-hidden">
                            <div class="p-2 border-b border-gray-100">
                                <input x-model="modeloSearch" @click.stop type="text" placeholder="Buscar..." autocomplete="off"
                                    class="w-full px-3 py-2 text-sm border border-[#AD0369] rounded-[6px] text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#AD0369]">
                            </div>
                            <div class="max-h-[200px] overflow-y-auto flex flex-col">
                                <button type="button" @click="selectModelo('')"
                                    class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                    :class="!modelo_id ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'">Seleccione modelo</button>
                                <template x-for="opt in modelosFiltrados" :key="opt.value">
                                    <button type="button" @click="selectModelo(opt.value)"
                                        class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                        :class="modelo_id === opt.value ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'"
                                        x-text="opt.label"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="prod-desc-input w-[395px] shrink-0 flex flex-col gap-1">
                    <label class="text-white text-[16px] font-normal leading-[150%]">Descripción</label>
                    <input type="text"
                           x-model="busqueda"
                           @keydown.enter="buscar()"
                           placeholder="Código OM / Código Ralux / Descripción / Equivalencia"
                           class="w-full h-[45px] bg-transparent rounded-[20px] border border-[#B2B2B2] text-[#B2B2B2] placeholder-[#B2B2B2]/60 text-[14px] font-normal px-4 focus:outline-none focus:border-white/50">
                </div>

                <div class="flex-1 flex flex-col gap-1">
                    <button @click="limpiar()" type="button"
                            class="text-white/60 underline text-[13px] text-center cursor-pointer hover:text-white/90 bg-transparent border-0 leading-[150%]">
                        Limpiar filtros
                    </button>
                    <button @click="buscar()" type="button"
                            class="w-full h-[44px] flex justify-center items-center rounded-[22px] bg-[#AD0369] text-white text-[16px] font-normal leading-[150%] transition-all duration-300 hover:bg-[#AD0369]/90 cursor-pointer">
                        BUSCAR
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="max-w-[1224px] mx-auto px-4 lg:px-0 pb-12 mt-[78px]">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-[24px]">

            <div class="flex gap-[26px]">

                @if($producto->imagenes->count() > 1 || $producto->imagen_diagrama || $producto->diagrama_orientativo)
                <div class="flex flex-col gap-[16px] flex-shrink-0">
                    @foreach($producto->imagenes->take(5) as $index => $imagen)
                        <button wire:click="cambiarImagen('{{ $imagen->ruta }}', '{{ $imagen->tipo }}')"
                                @click="currentIndex = {{ $index }}; showingDiagram = null"
                                :class="!showingDiagram && currentIndex === {{ $index }} ? 'border-[#AD0369]' : 'border-[#E5E5E5] hover:border-[#AD0369]'"
                                class="w-[78px] h-[78px] flex-shrink-0 border-2 rounded-[4px] p-1 transition-all duration-200 overflow-hidden">
                            @if($imagen->tipo === 'video')
                                <div class="w-full h-full bg-slate-900 rounded flex items-center justify-center relative">
                                    <video src="{{ Storage::url($imagen->ruta) }}" class="w-full h-full object-cover absolute inset-0" muted preload="metadata"></video>
                                    <svg class="w-5 h-5 text-white relative z-10 drop-shadow" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            @else
                                <img src="{{ Storage::url($imagen->ruta) }}"
                                     alt="{!! $imagen->alt ?: $producto->descripcion_es !!}"
                                     class="w-full h-full object-contain">
                            @endif
                        </button>
                    @endforeach
                    @if($producto->imagen_diagrama)
                        <button wire:click="cambiarImagen('{{ $producto->imagen_diagrama }}')"
                                @click="currentIndex = {{ $diagramaIndex === false ? 0 : $diagramaIndex }}; showingDiagram = 'dimensional'"
                                :class="showingDiagram === 'dimensional' ? 'border-[#AD0369]' : 'border-[#E5E5E5] hover:border-[#AD0369]'"
                                class="w-[78px] h-[78px] flex-shrink-0 border-2 rounded-[4px] p-1 transition-all duration-200">
                            <img src="{{ Storage::url($producto->imagen_diagrama) }}"
                                 alt="Diagrama dimensional"
                                 class="w-full h-full object-contain">
                        </button>
                    @endif
                    @if($producto->diagrama_orientativo)
                        <button wire:click="cambiarImagen('{{ $producto->diagrama_orientativo }}')"
                                @click="currentIndex = {{ $orientativoIndex === false ? 0 : $orientativoIndex }}; showingDiagram = 'orientativo'"
                                :class="showingDiagram === 'orientativo' ? 'border-[#AD0369]' : 'border-[#E5E5E5] hover:border-[#AD0369]'"
                                class="w-[78px] h-[78px] flex-shrink-0 border-2 rounded-[4px] p-1 transition-all duration-200">
                            <img src="{{ Storage::url($producto->diagrama_orientativo) }}"
                                 alt="Diagrama orientativo"
                                 class="w-full h-full object-contain">
                        </button>
                    @endif
                </div>
                @endif

                <div class="flex-1 border border-[#E5E5E5] rounded-[10px] p-6 flex items-center justify-center
                            h-[380px] lg:h-[496px] w-[380px] lg:w-[496px] group relative overflow-hidden
                            {{ $imagenActualTipo !== 'video' ? 'cursor-zoom-in' : '' }}"
                     @click="if (totalImages > 0) showModal = true">
                    @if($imagenActual)
                        @if($imagenActualTipo === 'video')
                            <video src="{{ Storage::url($imagenActual) }}"
                                   controls
                                   controlsList="nodownload"
                                   class="h-full w-full object-contain"
                                   @click.stop></video>
                        @else
                            <img src="{{ Storage::url($imagenActual) }}"
                                 alt="{!! $producto->descripcion_es !!}"
                                 class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/3 transition-colors duration-300"></div>
                        @endif
                    @else
                        <div class="flex flex-col items-center gap-2 text-gray-300">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col">

                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mb-[9px]">
                    @if($producto->tipo)
                        <span class="text-[#AD0369] font-inter text-[16px] font-bold uppercase tracking-wider">
                            {{ $producto->tipo->descripcion_es }}
                        </span>
                        <span class="text-black">|</span>
                    @endif
                    <span class="text-[#111010] font-inter text-[16px] font-bold uppercase">
                        {{ $producto->codigo_ralux }}
                    </span>
                    @if($producto->marcas->count())
                        <span class="text-black">-</span>
                        <span class="text-black font-inter text-[16px] uppercase">
                            {{ $producto->marcas->pluck('descripcion_es')->join(' · ') }}
                        </span>
                    @endif
                </div>

                <h1 class="text-[#111010] font-inter text-[24px] lg:text-[32px] font-semibold leading-tight mb-[17px]">
                    {!! $producto->descripcion_es !!}
                </h1>
                
                <div class="h-px bg-gray-200 mb-[30px]"></div>
                <h2 class="text-[#111010] font-inter text-[18px] font-bold mb-0">Características</h2>

                <div class="mt-2 border-t border-black/10 mb-2">
                    @php $row = 0; @endphp

                    <div class="flex justify-between items-center px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                        <span class="text-[#222] font-inter text-[16px]">Marcas</span>
                        <span class="text-[#222] font-inter text-[16px] font-normal text-right">
                            {{ $producto->marcas->pluck('descripcion_es')->join(', ') ?: '-' }}
                        </span>
                    </div>
                    @php $row++; @endphp

                    <div class="flex justify-between items-center px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                        <span class="text-[#222] font-inter text-[16px]">Soporte</span>
                        <span class="text-[#222] font-inter text-[16px] font-normal">
                            {{ $producto->soporte ? 'SI' : 'NO' }}
                        </span>
                    </div>
                    @php $row++; @endphp

                    @if($producto->terminales)
                    <div class="flex justify-between items-center px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                        <span class="text-[#222] font-inter text-[16px]">Terminales</span>
                        <span class="text-[#222] font-inter text-[16px] font-normal">{{ $producto->terminales }}</span>
                    </div>
                    @php $row++; @endphp
                    @endif

                    @if($producto->voltaje)
                    <div class="flex justify-between items-center px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                        <span class="text-[#222] font-inter text-[16px]">Voltaje</span>
                        <span class="text-[#222] font-inter text-[16px] font-normal">{{ $producto->voltaje }}</span>
                    </div>
                    @php $row++; @endphp
                    @endif

                    @if($producto->amperaje)
                    <div class="flex justify-between items-center px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                        <span class="text-[#222] font-inter text-[16px]">Amperaje</span>
                        <span class="text-[#222] font-inter text-[16px] font-normal">{{ $producto->amperaje }}</span>
                    </div>
                    @php $row++; @endphp
                    @endif

                    @for($i = 1; $i <= 10; $i++)
                        @if($producto->{"caract_$i"})
                        <div class="flex justify-between items-center px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                            <span class="text-[#222] font-inter text-[16px]">{{ $producto->{"caract_$i"} }}</span>
                            <span class="text-[#222] font-inter text-[16px] font-normal">
                                {{ $producto->{"valor_$i"} ?? '-' }}
                            </span>
                        </div>
                        @php $row++; @endphp
                        @endif
                    @endfor

                </div>

            
<div class="flex flex-col sm:flex-row gap-3 mt-auto">
    <div x-data="{ open: false }" @click.outside="open = false" class="relative w-full sm:w-[288px]">
        <button @click="open = !open" type="button"
            class="flex w-full h-[44px] px-[20px] py-[11px] justify-between items-center gap-2 border border-[#AD0369] text-[#AD0369] bg-transparent rounded-[22px] text-center font-inter text-[16px] font-normal leading-[150%] hover:bg-[#AD0369] hover:text-white transition-all duration-300">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                FICHA TÉCNICA
            </span>
            <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="absolute left-0 right-0 mt-2 rounded-[14px] border border-[#EAD2E0] bg-white shadow-[0_14px_30px_rgba(34,34,34,0.12)] overflow-hidden z-40"
            style="display:none;">
            <a href="{{ route('productos.ficha-tecnica.ver', $producto->id) }}" target="_blank"
                class="flex items-center gap-2 px-4 py-3 text-[14px] text-[#222] hover:bg-[#AD0369]/8 transition-colors">
                <svg class="w-4 h-4 text-[#AD0369] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.269 2.943 9.542 7-1.273 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Ver online
            </a>
            <a href="{{ route('productos.ficha-tecnica', $producto->id) }}"
                class="flex items-center gap-2 px-4 py-3 text-[14px] text-[#222] hover:bg-[#AD0369]/8 transition-colors border-t border-[#F1E8EE]">
                <svg class="w-4 h-4 text-[#AD0369] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v11m0 0l4-4m-4 4l-4-4M4 19h16"/>
                </svg>
                Descargar PDF
            </a>
        </div>
    </div>

    <a wire:navigate href="/contacto"
       class="flex w-full sm:w-[288px] h-[44px] px-[26px] py-[11px] justify-center items-center bg-[#AD0369] text-white rounded-[22px] text-center font-inter text-[16px] font-normal leading-[150%] hover:bg-[#8e0256] transition-all duration-300">
        CONSULTAR
    </a>
</div>

            </div>
        </div>

        {{-- ── Marcas y Modelos ── --}}
        @php
            $modelosPorMarca = $producto->modelos
                ->filter(fn($m) => $m->marca)
                ->groupBy(fn($m) => $m->marca->descripcion_es)
                ->sortKeys();
        @endphp
        @if($modelosPorMarca->isNotEmpty())
        <div class="mt-[80px]">
            <div class="flex items-center gap-3 mb-[40px]">
                <div class="w-1 h-6 bg-[#AD0369] flex-shrink-0"></div>
                <h2 class="text-[#111010] font-inter text-[20px] font-bold uppercase tracking-wide">
                    Marcas y Modelos
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-[48px] gap-y-[40px]">
                @foreach($modelosPorMarca as $marcaNombre => $modelos)
                <div>
                    <div class="flex items-center gap-3 mb-[20px]">
                        <div class="w-1 h-5 bg-[#AD0369] flex-shrink-0"></div>
                        <span class="text-[#111010] font-inter text-[15px] font-bold uppercase tracking-wide">
                            {{ $marcaNombre }}
                        </span>
                    </div>
                    <ul class="flex flex-col gap-[10px]">
                        @foreach($modelos as $modelo)
                        <li class="flex items-center gap-[10px]">
                            <svg class="w-4 h-4 text-[#AD0369] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-[#0E7490] font-inter text-[15px]">{{ $modelo->descripcion_es }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Códigos de Referencia Equipos Originales ── --}}
        @php
            $codigosPorMarca = $producto->codigosOM
                ->filter(fn($c) => $c->marca)
                ->groupBy(fn($c) => $c->marca->descripcion_es)
                ->sortKeys();
        @endphp
        @if($codigosPorMarca->isNotEmpty())
        <div class="mt-[80px]">
            <div class="flex items-center gap-3 mb-[40px]">
                <div class="w-1 h-6 bg-[#AD0369] flex-shrink-0"></div>
                <h2 class="text-[#111010] font-inter text-[20px] font-bold uppercase tracking-wide">
                    Códigos de Referencia Equipos Originales
                </h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-[48px] gap-y-[40px]">
                @foreach($codigosPorMarca as $marcaNombre => $codigos)
                <div>
                    <div class="flex items-center gap-3 mb-[20px]">
                        <div class="w-1 h-5 bg-[#AD0369] flex-shrink-0"></div>
                        <span class="text-[#111010] font-inter text-[15px] font-bold uppercase tracking-wide">
                            {{ $marcaNombre }}
                        </span>
                    </div>
                    <ul class="flex flex-col gap-[10px]">
                        @foreach($codigos as $codigo)
                        <li class="flex items-center gap-[10px]">
                            <svg class="w-4 h-4 text-[#AD0369] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-[#0E7490] font-inter text-[15px]">{{ $codigo->codigo }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($relacionados->count())
        <div class="mt-[123px]">
            <h2 class="text-[#222] text-[24px] font-bold leading-[120%] mb-[24px]">
                Productos relacionados
            </h2>

            {{-- Grid: visible en sm+ (640px+) --}}
            <div class="relacionados-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[24px] mb-10">
                @foreach($relacionados as $rel)
                <div class="bg-white rounded-[10px] border border-[#DEDFE0] hover:shadow-sm transition-shadow overflow-hidden flex flex-col min-h-[440px]">

                    <div class=" h-[237px] flex items-center p-2 justify-center overflow-hidden flex-shrink-0 relative">
                        @if($rel->imagenPrincipal)
                            <img src="{{ Storage::url($rel->imagenPrincipal->ruta) }}"
                                 alt="{{ $rel->descripcion_es }}"
                                 class="w-full h-full object-contain">
                        @else
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        @endif
                        <div class="absolute inset-0 bg-black/10 pointer-events-none z-[1] transition-colors duration-300"></div>
                        @if($rel->tipo)
                            <span class="absolute bottom-2 left-2 text-[#AD0369] text-[14px] font-bold leading-[150%] uppercase px-2 py-1 rounded-sm z-[2]">
                                {{ $rel->tipo->descripcion_es }}
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-col flex-1 pt-[16px] pb-[23px] px-[16px]">
                        <div class="flex justify-between items-center pb-[18px]">
                            <span class="text-[#222] text-[14px] font-bold leading-[150%]">{{ $rel->codigo_ralux }}</span>
                            <span class="text-[#222] text-right text-[13px] font-normal leading-[150%] uppercase">{{ $rel->marcas->first()?->descripcion_es ?? '' }}</span>
                        </div>
                        <p class="text-black text-[18px] font-normal leading-[22px] line-clamp-2">
                            {!! $rel->descripcion_es !!}
                        </p>
                        <a wire:navigate href="{{ route('productos.detalle', $rel->id) }}"
                           class="flex w-full max-w-[253px] h-[36px] py-[11px] px-[26px] justify-center items-center rounded-[22px] border border-[#AD0369] bg-white text-[#AD0369] text-center text-[12px] font-normal leading-[150%] mt-auto self-start hover:bg-[#AD0369] hover:text-white transition-colors">
                            VER DETALLE
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Carrusel: visible solo en mobile (< 640px) --}}
            <div class="relacionados-swiper-wrapper mb-10">
                <div class="swiper relacionadosSwiper">
                    <div class="swiper-wrapper">
                        @foreach($relacionados as $rel)
                        <div class="swiper-slide">
                            <div class="bg-white rounded-[10px] border border-[#DEDFE0] hover:shadow-sm transition-shadow overflow-hidden flex flex-col min-h-[440px]">

                                <div class="h-[237px] flex items-center p-2 justify-center overflow-hidden flex-shrink-0 relative">
                                    @if($rel->imagenPrincipal)
                                        <img src="{{ Storage::url($rel->imagenPrincipal->ruta) }}"
                                             alt="{{ $rel->descripcion_es }}"
                                             class="w-full h-full object-contain">
                                    @else
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    @endif
                                    <div class="absolute inset-0 bg-black/10 pointer-events-none z-[1] transition-colors duration-300"></div>
                                    @if($rel->tipo)
                                        <span class="absolute bottom-2 left-2 text-[#AD0369] text-[14px] font-bold leading-[150%] uppercase px-2 py-1 rounded-sm z-[2]">
                                            {{ $rel->tipo->descripcion_es }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-col flex-1 pt-[16px] pb-[23px] px-[16px]">
                                    <div class="flex justify-between items-center pb-[18px]">
                                        <span class="text-[#222] text-[14px] font-bold leading-[150%]">{{ $rel->codigo_ralux }}</span>
                                        <span class="text-[#222] text-right text-[13px] font-normal leading-[150%] uppercase">{{ $rel->marcas->first()?->descripcion_es ?? '' }}</span>
                                    </div>
                                    <p class="text-black text-[18px] font-normal leading-[22px] line-clamp-2">
                                        {{ $rel->descripcion_es }}
                                    </p>
                                    <a wire:navigate href="{{ route('productos.detalle', $rel->id) }}"
                                       class="flex w-full max-w-[253px] h-[36px] py-[11px] px-[26px] justify-center items-center rounded-[22px] border border-[#AD0369] bg-white text-[#AD0369] text-center text-[12px] font-normal leading-[150%] mt-auto self-start hover:bg-[#AD0369] hover:text-white transition-colors">
                                        VER DETALLE
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination relacionados-pagination"></div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Lightbox ── --}}
    @if($lightboxCount)
    <template x-teleport="body">
        <div x-show="showModal"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="showModal = false"
             @keydown.right.window="if(showModal) currentIndex = (currentIndex + 1) % totalImages"
             @keydown.left.window="if(showModal) currentIndex = (currentIndex - 1 + totalImages) % totalImages"
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/85 backdrop-blur-sm"
             style="display:none;">

            <div @click="showModal = false" class="absolute inset-0"></div>

            <button @click="showModal = false"
                    class="absolute top-5 right-5 text-white p-2 z-[100] hover:scale-110 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="relative w-full h-full flex flex-col items-center justify-center p-4 pointer-events-none">
                <div class="relative w-full h-[70vh] flex items-center justify-center pointer-events-auto">
                    @foreach($lightboxItems as $index => $item)
                        <div x-show="currentIndex === {{ $index }}"
                             x-transition:enter="transition-opacity duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="absolute inset-0 flex items-center justify-center px-16"
                             style="display:none;">
                            @if($item['tipo'] === 'video')
                                <video src="{{ Storage::url($item['ruta']) }}"
                                       controls
                                       controlsList="nodownload"
                                       class="max-h-full max-w-full object-contain"
                                       x-on:click.stop></video>
                            @else
                                <img src="{{ Storage::url($item['ruta']) }}"
                                     alt="{!! $item['alt'] !!}"
                                     class="max-h-full max-w-full object-contain">
                            @endif
                        </div>
                    @endforeach

                    @if($lightboxCount > 1)
                    <button @click.stop="currentIndex = (currentIndex - 1 + totalImages) % totalImages"
                            class="absolute left-4 text-white text-[70px] leading-none select-none hover:text-[#AD0369] transition-colors z-10">‹</button>
                    <button @click.stop="currentIndex = (currentIndex + 1) % totalImages"
                            class="absolute right-4 text-white text-[70px] leading-none select-none hover:text-[#AD0369] transition-colors z-10">›</button>
                    @endif
                </div>

                <div class="mt-6 flex gap-2 overflow-x-auto max-w-full p-2 pointer-events-auto scrollbar-hide">
                    @foreach($lightboxItems as $index => $item)
                        <button @click.stop="currentIndex = {{ $index }}"
                                :class="currentIndex === {{ $index }} ? 'border-[#AD0369] scale-110' : 'border-transparent opacity-50'"
                                class="w-14 h-14 border-2 rounded cursor-pointer transition-all duration-300 flex-shrink-0 bg-white p-1 hover:opacity-100 overflow-hidden">
                            @if($item['tipo'] === 'video')
                                <div class="w-full h-full bg-slate-800 rounded flex items-center justify-center relative">
                                    <video src="{{ Storage::url($item['ruta']) }}" class="absolute inset-0 w-full h-full object-cover" muted preload="metadata"></video>
                                    <svg class="w-4 h-4 text-white relative z-10 drop-shadow" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            @else
                                <img src="{{ Storage::url($item['ruta']) }}"
                                     alt="{{ $item['alt'] }}"
                                     class="w-full h-full object-contain">
                            @endif
                        </button>
                    @endforeach
                </div>

                <div class="mt-3 text-white text-sm pointer-events-auto">
                    <span x-text="currentIndex + 1"></span> / <span x-text="totalImages"></span>
                </div>
            </div>
        </div>
    </template>
    @endif

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
        }

        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        /* Relacionados: grid en sm+, carrusel en mobile */
        @media (min-width: 1240px) {
            .relacionados-grid {
                grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            }
        }
        .relacionados-swiper-wrapper { display: none; }
        @media (max-width: 639px) {
            .relacionados-grid { display: none !important; }
            .relacionados-swiper-wrapper { display: block; }
        }

        .relacionadosSwiper {
            overflow: hidden;
            position: relative;
        }
        /* < 540px: slidesPerView 1 */
        @media (max-width: 539px) {
            .relacionadosSwiper .swiper-slide {
                width: 100%;
                flex-shrink: 0;
                height: auto;
            }
        }
        /* 540–639px: slidesPerView auto, peek del siguiente */
        @media (min-width: 540px) and (max-width: 639px) {
            .relacionadosSwiper .swiper-slide {
                width: 82vw;
                flex-shrink: 0;
                height: auto;
            }
        }

        /* Dots: solo mobile */
        .relacionadosSwiper .swiper-pagination { display: none; }
        @media (max-width: 639px) {
            .relacionadosSwiper { padding-bottom: 36px !important; }
            .relacionadosSwiper .swiper-pagination { display: block; }
        }
        .relacionadosSwiper .swiper-pagination-bullet {
            width: 8px; height: 8px;
            background: #DDDDE0; opacity: 1;
            transition: all 0.3s ease;
            border-radius: 9999px;
        }
        .relacionadosSwiper .swiper-pagination-bullet-active {
            width: 24px;
            background: #AD0369;
            border-radius: 4px;
        }
    </style>

    <script>
    (function() {
        let relacionadosSwiperInstance = null;

        function initRelacionadosSwiper() {
            if (relacionadosSwiperInstance) {
                relacionadosSwiperInstance.destroy(true, true);
                relacionadosSwiperInstance = null;
            }
            const el = document.querySelector('.relacionadosSwiper');
            if (!el) return;

            setTimeout(function() {
                relacionadosSwiperInstance = new Swiper('.relacionadosSwiper', {
                    slidesPerView: 'auto',
                    spaceBetween: 16,
                    speed: 400,
                    loop: true,
                    grabCursor: true,
                    observer: true,
                    observeParents: true,
                    autoplay: {
                        delay: 4000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    pagination: {
                        el: el.querySelector('.swiper-pagination'),
                        clickable: true,
                    },
                    on: {
                        slideChangeTransitionEnd: function() { this.pagination.update(); },
                        loopFix: function() { this.pagination.update(); },
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: 1,
                            spaceBetween: 16,
                        },
                        540: {
                            slidesPerView: 'auto',
                            spaceBetween: 16,
                        },
                    }
                });
            }, 150);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initRelacionadosSwiper);
        } else {
            initRelacionadosSwiper();
        }

        document.addEventListener('livewire:navigated', initRelacionadosSwiper);
        document.addEventListener('livewire:navigating', function() {
            if (relacionadosSwiperInstance) {
                relacionadosSwiperInstance.destroy(true, true);
                relacionadosSwiperInstance = null;
            }
        });
    })();
    </script>
</div>

