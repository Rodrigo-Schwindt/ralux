<section class="bg-white"
         x-data="{ show: false }"
         x-init="setTimeout(() => show = true, 50)"
         x-show="show"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0">

    <div class="max-w-[1224px] mx-auto mb-[172px] px-4 lg:px-0">
            <div class=" pt-[44px]">
        <div class="max-w-[1224px] mx-auto px-4 lg:px-0">
            <nav class="flex items-center gap-1 text-[13px] font-inter">
                <a wire:navigate href="/" class="text-black hover:text-black transition-colors font-bold">Inicio</a>
                <span class="text-black/60">/</span>
                <span class="text-black/80 ">Productos</span>
            </nav>
        </div>
    </div>
        <div class="flex felx-col gap-[50px] mt-[78px] justify-center">
            @foreach($catalogos as $index => $catalogo)
                <div class="group rounded-[20px] overflow-hidden flex flex-row min-h-[339px] shadow-sm border border-gray-100 bg-white animate-catalog-item"
                     style="animation-delay: {{ $index * 0.1 }}s;">

                    <div class="relative w-[243px] shrink-0 flex justify-center  overflow-hidden">
                        @if($catalogo->image_1)
                            <img
                                src="{{ Storage::url($catalogo->image_1) }}"
                                alt="{{ $catalogo->title }}"
                                class="w-full h-full object-cover transition-transform duration-700  group-hover:scale-105"
                            >
                        @else
                            <div class="w-full h-full bg-slate-800"></div>
                        @endif

<div class="absolute inset-0 bg-black/40"></div>
                        @if($catalogo->image_2)
                            <div class="absolute top-[54px] ">
                                <img
                                    src="{{ Storage::url($catalogo->image_2) }}"
                                    alt="Logo"
                                    class="h-[41px] w-auto object-contain drop-shadow"
                                >
                            </div>
                        @endif

                        <div class="absolute bottom-[31px] ">
                            <p class="text-white font-semibold text-[17px] text-center leading-tight px-8">
                                {!! nl2br(e($catalogo->subtitle)) !!}
                            </p>
                        </div>
                    </div>

                    <div class="flex-1 bg-[#F6F6F6] flex flex-col  w-[566px] #F5F5F5 px-[69px] pt-[84px]">
                        <h3 class="text-[#222] font-bold text-[24px] leading-tight mb-[27px]">
                            {{ $catalogo->title }}
                        </h3>

                        <p class="text-[#222] text-[16px] leading-relaxed mb-[49px] max-w-[420px]">
                            {{ $catalogo->descripcion }}
                        </p>

@if($catalogo->pdf)
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ Storage::url($catalogo->pdf) }}"
           download
           class="flex w-[184px] h-[41px] px-[26px] py-[11px] justify-center items-center bg-[#AD0369] text-white rounded-[22px] text-center font-inter text-[16px] font-normal leading-[150%] hover:bg-[#8e0256] transition-all duration-300">
            DESCARGAR
        </a>

        <a href="{{ Storage::url($catalogo->pdf) }}"
           target="_blank"
           class="flex w-[184px] h-[41px] px-[26px] py-[11px] justify-center items-center border border-[#AD0369] text-[#AD0369] bg-transparent rounded-[22px] text-center font-inter text-[16px] font-normal leading-[150%] hover:bg-[#AD0369] hover:text-white transition-all duration-300">
            VER ONLINE
        </a>
    </div>
@endif
                    </div>

                </div>
            @endforeach
        </div>
    </div>

    <style>
        @keyframes catalogFadeInUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-catalog-item {
            animation: catalogFadeInUp 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
        }
    </style>
</section>