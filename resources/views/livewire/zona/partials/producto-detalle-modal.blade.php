@if($productoDetalle)
@php
    $lightboxItems = $productoDetalle->imagenes
        ->map(fn($imagen) => [
            'ruta' => $imagen->ruta,
            'tipo' => $imagen->tipo,
            'alt' => $imagen->alt ?: $productoDetalle->descripcion_es,
        ])
        ->values();

    if ($productoDetalle->imagen_diagrama) {
        $lightboxItems->push([
            'ruta' => $productoDetalle->imagen_diagrama,
            'tipo' => 'diagrama',
            'alt' => 'Diagrama dimensional',
        ]);
    }

    if ($productoDetalle->diagrama_orientativo) {
        $lightboxItems->push([
            'ruta' => $productoDetalle->diagrama_orientativo,
            'tipo' => 'diagrama',
            'alt' => 'Diagrama orientativo',
        ]);
    }

    $imagenesCount = $productoDetalle->imagenes->count();
    $lightboxCount = $lightboxItems->count();
    $indiceActual = $lightboxItems->search(fn($item) => $item['ruta'] === $modalImagenActual);
    $indiceActual = $indiceActual === false ? 0 : $indiceActual;
    $diagramaIndex = $lightboxItems->search(fn($item) => $item['ruta'] === $productoDetalle->imagen_diagrama);
    $orientativoIndex = $lightboxItems->search(fn($item) => $item['ruta'] === $productoDetalle->diagrama_orientativo);
    $modelosPorMarca = $productoDetalle->modelos
        ->filter(fn($m) => $m->marca)
        ->groupBy(fn($m) => $m->marca->descripcion_es)
        ->sortKeys();
    $codigosPorMarca = $productoDetalle->codigosOM
        ->filter(fn($c) => $c->marca)
        ->groupBy(fn($c) => $c->marca->descripcion_es)
        ->sortKeys();
@endphp

<style>
    .zona-detalle-modal-scroll {
        scrollbar-width: thin;
        scrollbar-color: #AD0369 #F1E8EE;
    }
    .zona-detalle-modal-scroll::-webkit-scrollbar {
        width: 10px;
    }
    .zona-detalle-modal-scroll::-webkit-scrollbar-track {
        background: #F1E8EE;
        border-radius: 999px;
        margin: 18px 0;
    }
    .zona-detalle-modal-scroll::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #AD0369 0%, #FD359D 100%);
        border: 2px solid #F1E8EE;
        border-radius: 999px;
    }
    .zona-detalle-modal-scroll::-webkit-scrollbar-thumb:hover {
        background: #8e0256;
    }
</style>

<template x-teleport="body">
    <div
        x-data="{
            lightboxOpen: false,
            modalOpen: false,
            currentIndex: {{ $indiceActual }},
            totalImages: {{ $lightboxCount }},
            showingDiagram: @js($modalImagenActual === $productoDetalle->imagen_diagrama ? 'dimensional' : ($modalImagenActual === $productoDetalle->diagrama_orientativo ? 'orientativo' : null)),
            closeModal() {
                this.modalOpen = false;
                setTimeout(() => $wire.cerrarDetalleProducto(), 180);
            }
        }"
        x-effect="if (!lightboxOpen) currentIndex = {{ $indiceActual }}"
        x-init="$nextTick(() => { modalOpen = true; })"
        x-on:keydown.escape.window="lightboxOpen ? lightboxOpen = false : closeModal()"
        wire:key="producto-detalle-modal-{{ $productoDetalle->id }}"
        class="fixed inset-0 z-[9000] flex items-center justify-center px-4 py-6"
    >
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="closeModal()"
            class="absolute inset-0 bg-black/55"
            style="display:none;"
        ></div>

        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-220"
            x-transition:enter-start="opacity-0 translate-y-3 scale-[0.985]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-170"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-[0.985]"
            class="zona-detalle-modal-scroll relative w-full max-w-[1224px] max-h-[92vh] overflow-y-auto rounded-l-[20px] rounded-r-none bg-white shadow-[0_24px_70px_rgba(0,0,0,0.28)]"
            @click.stop
            style="display:none;"
        >
            <button
                type="button"
                @click="closeModal()"
                class="sticky top-4 ml-auto mr-4 z-50 flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-white text-[#222] shadow-[0_10px_24px_rgba(0,0,0,0.16)] hover:text-[#AD0369] transition-colors"
                aria-label="Cerrar detalle"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="px-4 sm:px-6 lg:px-10 pt-6 sm:pt-8 lg:pt-10 pb-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-[24px]">
                    <div class="flex gap-[18px] sm:gap-[26px]">
                        @if($imagenesCount > 1 || $productoDetalle->imagen_diagrama || $productoDetalle->diagrama_orientativo)
                        <div class="flex flex-col gap-[12px] sm:gap-[16px] flex-shrink-0">
                            @foreach($productoDetalle->imagenes->take(5) as $index => $imagen)
                                <button
                                    type="button"
                                    wire:click.stop="cambiarImagenDetalle('{{ $imagen->ruta }}', '{{ $imagen->tipo }}')"
                                    @click="currentIndex = {{ $index }}; showingDiagram = null"
                                    :class="!showingDiagram && currentIndex === {{ $index }} ? 'border-[#AD0369]' : 'border-[#E5E5E5] hover:border-[#AD0369]'"
                                    class="w-[62px] h-[62px] sm:w-[78px] sm:h-[78px] flex-shrink-0 border-2 rounded-[4px] p-1 transition-all duration-200 overflow-hidden bg-white"
                                >
                                    @if($imagen->tipo === 'video')
                                        <div class="w-full h-full bg-slate-900 rounded flex items-center justify-center relative">
                                            <video src="{{ Storage::url($imagen->ruta) }}" class="w-full h-full object-cover absolute inset-0" muted preload="metadata"></video>
                                            <svg class="w-5 h-5 text-white relative z-10 drop-shadow" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    @else
                                        <img src="{{ Storage::url($imagen->ruta) }}"
                                             alt="{!! $imagen->alt ?: $productoDetalle->descripcion_es !!}"
                                             class="w-full h-full object-contain">
                                    @endif
                                </button>
                            @endforeach
                            @if($productoDetalle->imagen_diagrama)
                                <button
                                    type="button"
                                    wire:click.stop="cambiarImagenDetalle('{{ $productoDetalle->imagen_diagrama }}')"
                                    @click="currentIndex = {{ $diagramaIndex === false ? 0 : $diagramaIndex }}; showingDiagram = 'dimensional'"
                                    :class="showingDiagram === 'dimensional' ? 'border-[#AD0369]' : 'border-[#E5E5E5] hover:border-[#AD0369]'"
                                    class="w-[62px] h-[62px] sm:w-[78px] sm:h-[78px] flex-shrink-0 border-2 rounded-[4px] p-1 transition-all duration-200 bg-white"
                                >
                                    <img src="{{ Storage::url($productoDetalle->imagen_diagrama) }}"
                                         alt="Diagrama dimensional"
                                         class="w-full h-full object-contain">
                                </button>
                            @endif
                            @if($productoDetalle->diagrama_orientativo)
                                <button
                                    type="button"
                                    wire:click.stop="cambiarImagenDetalle('{{ $productoDetalle->diagrama_orientativo }}')"
                                    @click="currentIndex = {{ $orientativoIndex === false ? 0 : $orientativoIndex }}; showingDiagram = 'orientativo'"
                                    :class="showingDiagram === 'orientativo' ? 'border-[#AD0369]' : 'border-[#E5E5E5] hover:border-[#AD0369]'"
                                    class="w-[62px] h-[62px] sm:w-[78px] sm:h-[78px] flex-shrink-0 border-2 rounded-[4px] p-1 transition-all duration-200 bg-white"
                                >
                                    <img src="{{ Storage::url($productoDetalle->diagrama_orientativo) }}"
                                         alt="Diagrama orientativo"
                                         class="w-full h-full object-contain">
                                </button>
                            @endif
                        </div>
                        @endif

                        <div
                            class="flex-1 border border-[#E5E5E5] rounded-[10px] p-4 sm:p-6 flex items-center justify-center h-[300px] sm:h-[380px] lg:h-[496px] min-w-0 group relative overflow-hidden {{ $modalImagenActualTipo !== 'video' ? 'cursor-zoom-in' : '' }}"
                            @click="if (totalImages > 0) lightboxOpen = true"
                        >
                            @if($modalImagenActual)
                                @if($modalImagenActualTipo === 'video')
                                    <video src="{{ Storage::url($modalImagenActual) }}"
                                           controls
                                           controlsList="nodownload"
                                           class="h-full w-full object-contain"
                                           @click.stop></video>
                                @else
                                    <img src="{{ Storage::url($modalImagenActual) }}"
                                         alt="{!! $productoDetalle->descripcion_es !!}"
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
                            @if($productoDetalle->tipo)
                                <span class="text-[#AD0369] font-inter text-[16px] font-bold uppercase tracking-wider">
                                    {{ $productoDetalle->tipo->descripcion_es }}
                                </span>
                                <span class="text-black">|</span>
                            @endif
                            <span class="text-[#111010] font-inter text-[16px] font-bold uppercase">
                                {{ $productoDetalle->codigo_ralux }}
                            </span>
                            @if($productoDetalle->marcas->count())
                                <span class="text-black">-</span>
                                <span class="text-black font-inter text-[16px] uppercase">
                                    {{ $productoDetalle->marcas->pluck('descripcion_es')->join(' - ') }}
                                </span>
                            @endif
                        </div>

                        <h2 class="text-[#111010] font-inter text-[24px] lg:text-[32px] font-semibold leading-tight mb-[17px]">
                            {!! $productoDetalle->descripcion_es !!}
                        </h2>

                        <div class="h-px bg-gray-200 mb-[30px]"></div>
                        <h3 class="text-[#111010] font-inter text-[18px] font-bold mb-0">Caracteristicas</h3>

                        <div class="mt-2 border-t border-black/10 mb-2">
                            @php $row = 0; @endphp

                            <div class="flex justify-between items-center gap-4 px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                                <span class="text-[#222] font-inter text-[16px]">Marcas</span>
                                <span class="text-[#222] font-inter text-[16px] font-normal text-right">
                                    {{ $productoDetalle->marcas->pluck('descripcion_es')->join(', ') ?: '-' }}
                                </span>
                            </div>
                            @php $row++; @endphp

                            <div class="flex justify-between items-center gap-4 px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                                <span class="text-[#222] font-inter text-[16px]">Soporte</span>
                                <span class="text-[#222] font-inter text-[16px] font-normal">
                                    {{ $productoDetalle->soporte ? 'SI' : 'NO' }}
                                </span>
                            </div>
                            @php $row++; @endphp

                            @if($productoDetalle->terminales)
                            <div class="flex justify-between items-center gap-4 px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                                <span class="text-[#222] font-inter text-[16px]">Terminales</span>
                                <span class="text-[#222] font-inter text-[16px] font-normal">{{ $productoDetalle->terminales }}T</span>
                            </div>
                            @php $row++; @endphp
                            @endif

                            @if($productoDetalle->voltaje)
                            <div class="flex justify-between items-center gap-4 px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                                <span class="text-[#222] font-inter text-[16px]">Voltaje</span>
                                <span class="text-[#222] font-inter text-[16px] font-normal">{{ $productoDetalle->voltaje }}V</span>
                            </div>
                            @php $row++; @endphp
                            @endif

                            @if($productoDetalle->amperaje)
                            <div class="flex justify-between items-center gap-4 px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                                <span class="text-[#222] font-inter text-[16px]">Amperaje</span>
                                <span class="text-[#222] font-inter text-[16px] font-normal">{{ $productoDetalle->amperaje }}A</span>
                            </div>
                            @php $row++; @endphp
                            @endif

                            @for($i = 1; $i <= 10; $i++)
                                @if($productoDetalle->{"caract_$i"})
                                <div class="flex justify-between items-center gap-4 px-2 py-[10px] border-b border-black/10 {{ $row % 2 === 1 ? 'bg-[#F5F5F5]' : '' }}">
                                    <span class="text-[#222] font-inter text-[16px]">{{ $productoDetalle->{"caract_$i"} }}</span>
                                    <span class="text-[#222] font-inter text-[16px] font-normal text-right">
                                        {{ $productoDetalle->{"valor_$i"} ?? '-' }}
                                    </span>
                                </div>
                                @php $row++; @endphp
                                @endif
                            @endfor
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 mt-auto pt-4">
                            <div x-data="{ open: false }" @click.outside="open = false" class="relative w-full sm:w-[288px]">
                                <button @click="open = !open" type="button"
                                    class="flex w-full h-[44px] px-[20px] py-[11px] justify-between items-center gap-2 border border-[#AD0369] text-[#AD0369] bg-transparent rounded-[22px] text-center font-inter text-[16px] font-normal leading-[150%] hover:bg-[#AD0369] hover:text-white transition-all duration-300">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        FICHA TECNICA
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
                                    <a href="{{ route('productos.ficha-tecnica.ver', $productoDetalle->id) }}" target="_blank"
                                        class="flex items-center gap-2 px-4 py-3 text-[14px] text-[#222] hover:bg-[#AD0369]/8 transition-colors">
                                        <svg class="w-4 h-4 text-[#AD0369] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.269 2.943 9.542 7-1.273 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver online
                                    </a>
                                    <a href="{{ route('productos.ficha-tecnica', $productoDetalle->id) }}"
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

                @if($modelosPorMarca->isNotEmpty())
                <div class="mt-[64px]">
                    <div class="flex items-center gap-3 mb-[32px]">
                        <div class="w-1 h-6 bg-[#AD0369] flex-shrink-0"></div>
                        <h3 class="text-[#111010] font-inter text-[20px] font-bold uppercase tracking-wide">
                            Marcas y Modelos
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-[48px] gap-y-[32px]">
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

                @if($codigosPorMarca->isNotEmpty())
                <div class="mt-[64px]">
                    <div class="flex items-center gap-3 mb-[32px]">
                        <div class="w-1 h-6 bg-[#AD0369] flex-shrink-0"></div>
                        <h3 class="text-[#111010] font-inter text-[20px] font-bold uppercase tracking-wide">
                            Codigos de Referencia Equipos Originales
                        </h3>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-[48px] gap-y-[32px]">
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
            </div>
        </div>

        @if($lightboxCount)
        <div
            x-show="lightboxOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.right.window="if(lightboxOpen) currentIndex = (currentIndex + 1) % totalImages"
            @keydown.left.window="if(lightboxOpen) currentIndex = (currentIndex - 1 + totalImages) % totalImages"
            class="fixed inset-0 z-[9500] flex items-center justify-center bg-black/85 backdrop-blur-sm"
            style="display:none;"
        >
            <div @click="lightboxOpen = false" class="absolute inset-0"></div>

            <button @click="lightboxOpen = false"
                    type="button"
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
                    <button type="button" @click.stop="currentIndex = (currentIndex - 1 + totalImages) % totalImages"
                            class="absolute left-4 text-white text-[70px] leading-none select-none hover:text-[#AD0369] transition-colors z-10">&lsaquo;</button>
                    <button type="button" @click.stop="currentIndex = (currentIndex + 1) % totalImages"
                            class="absolute right-4 text-white text-[70px] leading-none select-none hover:text-[#AD0369] transition-colors z-10">&rsaquo;</button>
                    @endif
                </div>

                <div class="mt-6 flex gap-2 overflow-x-auto max-w-full p-2 pointer-events-auto scrollbar-hide">
                    @foreach($lightboxItems as $index => $item)
                        <button type="button" @click.stop="currentIndex = {{ $index }}"
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
        @endif
    </div>
</template>
@endif
