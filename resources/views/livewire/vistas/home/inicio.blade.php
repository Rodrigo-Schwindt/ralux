<div>
<style>
/* === Ralux: animaciones de entrada === */
@keyframes ralux-fade-up {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0);    }
}
@keyframes ralux-fade-in {
    from { opacity: 0; }
    to   { opacity: 1; }
}

.ralux-anim-title {
    animation: ralux-fade-up 0.75s cubic-bezier(.25,.46,.45,.94) 0.15s both;
}
.ralux-anim-desc {
    animation: ralux-fade-up 0.75s cubic-bezier(.25,.46,.45,.94) 0.32s both;
}
.ralux-anim-btn-slide {
    animation: ralux-fade-up 0.75s cubic-bezier(.25,.46,.45,.94) 0.50s both;
}
.ralux-anim-bar {
    animation: ralux-fade-in 0.65s ease 0.25s both;
}

/* scroll reveal */
.ralux-reveal {
    opacity: 0;
    transform: translateY(26px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.ralux-reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
}
.ralux-reveal-delay {
    transition-delay: 0.15s;
}

/* === Responsive: sólo aplica por debajo de 1240px === */
@media (max-width: 1239px) {
    #banner {
        height: 520px !important;
    }
    .banner-content-inner {
        top: 230px !important;
        max-width: 100% !important;
        padding-left: 28px !important;
        padding-right: 28px !important;
    }
    .banner-content-inner p {
        font-size: 34px !important;
        max-width: 90% !important;
    }
    .ralux-banner-btn {
        margin-bottom: 56px !important;
    }
    .ralux-searchbar {
        height: auto !important;
        padding-top: 22px !important;
        padding-bottom: 22px !important;
    }
    .ralux-desc-input {
        width: 100% !important;
    }
}

@media (max-width: 767px) {
    #banner {
        height: 400px !important;
    }
    .banner-content-inner {
        top: 168px !important;
        padding-left: 20px !important;
        padding-right: 20px !important;
    }
    .banner-content-inner h1 {
        font-size: 18px !important;
    }
    .banner-content-inner p {
        font-size: 24px !important;
        line-height: 1.35 !important;
        max-width: 100% !important;
    }
    .ralux-banner-btn {
        margin-bottom: 24px !important;
        width: 160px !important;
    }
    .ralux-searchbar {
        padding-top: 18px !important;
        padding-bottom: 18px !important;
    }
}
</style>
@if($sliders->count() > 0)
<div id="banner" style="position:relative; width:100%; height:634px; overflow:hidden; background:#000;">

    @foreach($sliders as $index => $slider)
    @php
        $ext = strtolower(pathinfo($slider->image, PATHINFO_EXTENSION));
        $isVideo = in_array($ext, ['mp4','webm','ogg','mov','avi']);
        $buttonText = trim((string) ($slider->button_text ?? ''));
        $buttonUrl = trim((string) ($slider->url ?? ''));
        $buttonTarget = $slider->button_target === '_blank' ? '_blank' : '_self';
        $showButton = $buttonText !== '' && $buttonUrl !== '';
    @endphp

    <div class="banner-slide" style="position:absolute; inset:0; opacity:{{ $index === 0 ? '1' : '0' }}; transition:opacity 0.7s ease;">

        @if($isVideo)
            <video style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;" autoplay loop muted playsinline>
                <source src="{{ Storage::url($slider->image) }}" type="video/{{ $ext }}">
            </video>
        @else
            <img src="{{ Storage::url($slider->image) }}" alt="{{ $slider->title }}"
                 style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
        @endif

        <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.35) 50%, rgba(0,0,0,0.15) 100%);"></div>

        <div class="banner-content-inner" style="position:absolute; inset:0; display:flex; flex-direction:column;top: 302px;  max-width:1224px; margin:0 auto;">
            <h1 style=" text-shadow:0 2px 8px rgba(0,0,0,0.4); margin:0;" class="ralux-anim-title text-[#AD0369]  text-[24px] font-bold leading-[120%]">
                {{ $slider->title }}
            </h1>
            @if($slider->description)
            <p style="color:#fff; font-size:48px; font-weight:700; margin-top:4px; max-width:600px; line-height:1.5;" class="ralux-anim-desc">
                {{ preg_replace('/(&nbsp;)+$/', '', strip_tags($slider->description)) }}
            </p>
            @endif
            @if($showButton)
            <a href="{{ $buttonUrl }}"
               target="{{ $buttonTarget }}"
               @if($buttonTarget === '_blank') rel="noopener noreferrer" @endif
               class="ralux-banner-btn ralux-anim-btn-slide mt-auto mb-[104px] inline-flex items-center justify-center
                      h-[44px]  w-[184px]
                      border border-white rounded-[22px]
                      text-white text-[16px] font-normal leading-[150%] uppercase text-center
                      no-underline transition-all duration-300
                      hover:bg-white hover:text-black">
                {{ $buttonText }}
            </a>
            @endif
        </div>
    </div>
    @endforeach

    @if($sliders->count() > 1)
    <div id="banner-dots" style="position:absolute; bottom:20px; left:50%; transform:translateX(-50%); display:flex; gap:8px; z-index:20;">
        @foreach($sliders as $i => $s)
        <button onclick="bannerGo({{ $i }})"
                id="dot-{{ $i }}"
                style="height:5px; width:{{ $i === 0 ? '32px' : '20px' }}; background:{{ $i === 0 ? '#fff' : 'rgba(255,255,255,0.5)' }}; border:none; border-radius:9999px; cursor:pointer; transition:all 0.3s; padding:0;">
        </button>
        @endforeach
    </div>
    @endif
</div>

<script>
(function() {
    var slides = document.querySelectorAll('#banner .banner-slide');
    var dots   = document.querySelectorAll('#banner-dots button');
    var total  = slides.length;
    var current = 0;
    var timer;

    function go(n) {
        slides[current].style.opacity = '0';
        if (dots[current]) { dots[current].style.width = '20px'; dots[current].style.background = 'rgba(255,255,255,0.5)'; }
        current = (n + total) % total;
        slides[current].style.opacity = '1';
        if (dots[current]) { dots[current].style.width = '32px'; dots[current].style.background = '#fff'; }
    }

    window.bannerGo = function(n) { clearInterval(timer); go(n); startTimer(); };

    function startTimer() {
        if (total > 1) timer = setInterval(function() { go(current + 1); }, 5000);
    }

    startTimer();
})();
</script>
@endif

<script>
    window._raluxHomeData = {
        tipos:      @json($tipos->map(fn($t) => ['value' => (string)$t->id, 'label' => $t->descripcion_es])->values()),
        marcas:     @json($marcas->map(fn($m) => ['value' => (string)$m->id, 'label' => $m->descripcion_es])->values()),
        modelos:    @json($todosModelos)
    };
</script>

<div class="ralux-anim-bar ralux-searchbar bg-[#222] h-[134px] flex items-center relative z-10"
    x-data="{
        tipoId: '', marcaId: '', modeloId: '', busqueda: '',
        tipoOpen: false, tipoSearch: '',
        marcaOpen: false, marcaSearch: '',
        modeloOpen: false, modeloSearch: '',
        tipoOpts:  window._raluxHomeData.tipos,
        marcaOpts: window._raluxHomeData.marcas,
        get modeloOpts() {
            const all = window._raluxHomeData.modelos;
            return this.marcaId ? all.filter(m => (m.marca_ids || [m.marca_id]).includes(this.marcaId)) : all;
        },
        tipoLabel()   { const f = this.tipoOpts.find(o => o.value === this.tipoId);   return f ? f.label : 'Seleccione categoría'; },
        marcaLabel()  { const f = this.marcaOpts.find(o => o.value === this.marcaId); return f ? f.label : 'Seleccione marca'; },
        modeloLabel() { const f = window._raluxHomeData.modelos.find(o => o.value === this.modeloId); return f ? f.label : 'Seleccione modelo'; },
        selectTipo(v)   { this.tipoId = v; this.tipoOpen = false; this.tipoSearch = ''; },
        selectMarca(v)  {
            this.marcaId = v; this.marcaOpen = false; this.marcaSearch = '';
            if (this.modeloId && v) {
                const m = window._raluxHomeData.modelos.find(m => m.value === this.modeloId);
                if (m && !(m.marca_ids || [m.marca_id]).includes(v)) this.modeloId = '';
            }
            if (!v) this.modeloId = '';
        },
        selectModelo(v) {
            this.modeloId = v; this.modeloOpen = false; this.modeloSearch = '';
            if (v) { const m = window._raluxHomeData.modelos.find(m => m.value === v); if (m) this.marcaId = m.marca_id; }
        },
        limpiar() { this.tipoId = ''; this.marcaId = ''; this.modeloId = ''; this.busqueda = ''; },
        irAProductos() {
            const p = new URLSearchParams();
            if (this.tipoId)          p.set('tipo_id',   this.tipoId);
            if (this.marcaId)         p.set('marca_id',  this.marcaId);
            if (this.modeloId)        p.set('modelo_id', this.modeloId);
            if (this.busqueda.trim()) p.set('busqueda',  this.busqueda.trim());
            const url = '/productos' + (p.toString() ? '?' + p.toString() : '');
            if (typeof Livewire !== 'undefined' && Livewire.navigate) { Livewire.navigate(url); } else { window.location.href = url; }
        }
    }"
    @click.outside="tipoOpen = false; marcaOpen = false; modeloOpen = false">
    <div class="max-w-[1224px] mx-auto w-full px-4 lg:px-0">
        <div class="flex flex-wrap lg:flex-nowrap gap-6 items-end">

            {{-- Categorías --}}
            <div class="flex-1 flex flex-col gap-1">
                <label class="text-white text-[16px] font-normal leading-[150%]">Categorías</label>
                <div class="relative">
                    <button @click="tipoOpen = !tipoOpen; marcaOpen = false; modeloOpen = false" type="button"
                        class="w-full h-[45px] rounded-[20px] border border-[#B2B2B2] bg-transparent text-[14px] font-normal pl-4 pr-3 focus:outline-none cursor-pointer flex items-center justify-between gap-2">
                        <span :class="tipoId ? 'text-white' : 'text-[#B2B2B2]'" class="truncate text-left" x-text="tipoLabel()"></span>
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
                                :class="!tipoId ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'">
                                Seleccione categoría
                            </button>
                            <template x-for="opt in tipoOpts.filter(o => !tipoSearch || o.label.toLowerCase().includes(tipoSearch.toLowerCase()))" :key="opt.value">
                                <button type="button" @click="selectTipo(opt.value)"
                                    class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                    :class="tipoId === opt.value ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'"
                                    x-text="opt.label"></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Marca --}}
            <div class="flex-1 flex flex-col gap-1">
                <label class="text-white text-[16px] font-normal leading-[150%]">Marca</label>
                <div class="relative">
                    <button @click="marcaOpen = !marcaOpen; tipoOpen = false; modeloOpen = false" type="button"
                        class="w-full h-[45px] rounded-[20px] border border-[#B2B2B2] bg-transparent text-[14px] font-normal pl-4 pr-3 focus:outline-none cursor-pointer flex items-center justify-between gap-2">
                        <span :class="marcaId ? 'text-white' : 'text-[#B2B2B2]'" class="truncate text-left" x-text="marcaLabel()"></span>
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
                                :class="!marcaId ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'">
                                Seleccione marca
                            </button>
                            <template x-for="opt in marcaOpts.filter(o => !marcaSearch || o.label.toLowerCase().includes(marcaSearch.toLowerCase()))" :key="opt.value">
                                <button type="button" @click="selectMarca(opt.value)"
                                    class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                    :class="marcaId === opt.value ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'"
                                    x-text="opt.label"></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modelo --}}
            <div class="flex-1 flex flex-col gap-1">
                <label class="text-white text-[16px] font-normal leading-[150%]">Modelo</label>
                <div class="relative">
                    <button @click="modeloOpen = !modeloOpen; tipoOpen = false; marcaOpen = false" type="button"
                        class="w-full h-[45px] rounded-[20px] border border-[#B2B2B2] bg-transparent text-[14px] font-normal pl-4 pr-3 focus:outline-none cursor-pointer flex items-center justify-between gap-2">
                        <span :class="modeloId ? 'text-white' : 'text-[#B2B2B2]'" class="truncate text-left" x-text="modeloLabel()"></span>
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
                                :class="!modeloId ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'">
                                Seleccione modelo
                            </button>
                            <template x-for="opt in modeloOpts.filter(o => !modeloSearch || o.label.toLowerCase().includes(modeloSearch.toLowerCase()))" :key="opt.value">
                                <button type="button" @click="selectModelo(opt.value)"
                                    class="w-full text-left px-4 py-2 text-[13px] transition-colors"
                                    :class="modeloId === opt.value ? 'bg-[#AD0369] text-white' : 'text-gray-700 hover:bg-[#AD0369]/10 hover:text-[#AD0369]'"
                                    x-text="opt.label"></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Descripción --}}
            <div class="ralux-desc-input w-[395px] shrink-0 flex flex-col gap-1">
                <label class="text-white text-[16px] font-normal leading-[150%]">Descripción</label>
                <input type="text"
                       x-model="busqueda"
                       @keydown.enter.prevent="irAProductos()"
                       placeholder="Código OM / Código Ralux / Descripción / Equivalencia"
                       class="w-full h-[45px] bg-transparent rounded-[20px] border border-[#B2B2B2] text-white placeholder-[#B2B2B2]/60 text-[14px] font-normal px-4 focus:outline-none focus:border-white/50">
            </div>

            <div class="flex-1 flex flex-col gap-1">
                <button type="button" @click="limpiar()"
                        class="text-white/60 underline text-[13px] text-center cursor-pointer hover:text-white/90 bg-transparent border-0 leading-[150%]">
                    Limpiar filtros
                </button>
                <button type="button" @click="irAProductos()"
                        class="w-full h-[44px] flex justify-center items-center rounded-[22px] bg-[#AD0369] text-white text-[16px] font-normal leading-[150%] transition-all duration-300 hover:bg-[#AD0369]/90 cursor-pointer">
                    BUSCAR
                </button>
            </div>

        </div>
    </div>
</div>

<div class="ralux-reveal">
    <livewire:vistas.productos.productos-destacados/>
</div>

<div class="max-w-[1224px] mx-auto">
    <div class="ralux-reveal">
        <livewire:vistas.home.nosotros-home />
    </div>
    <div class="ralux-reveal ralux-reveal-delay">
        <livewire:vistas.novedades.destacadas/>
    </div>
</div>


<script>
(function() {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });

    document.querySelectorAll('.ralux-reveal').forEach(function(el) {
        observer.observe(el);
    });
})();
</script>

</div>
