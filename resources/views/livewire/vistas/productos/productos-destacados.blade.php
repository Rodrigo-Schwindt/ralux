<section class="prod-dest-section bg-white w-full pt-[68px] overflow-hidden"
    x-data="{ show: false }"
    x-intersect.once.threshold.0.1="show = true">

    <div class="max-w-[1224px] mx-auto px-4 lg:px-0">

        <div class="flex items-center justify-between mb-[24px]  transition-all duration-700 ease-out"
             :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">

            <h2 class="prod-dest-title text-[#222] text-[32px] font-bold max-[650px]:w-[200px] leading-[120%]">
                Productos destacados
            </h2>

            <a wire:navigate href="{{ route('productos') }}"
               class="rounded-[22px] border border-[#AD0369] bg-white inline-flex h-[44px] py-[11px] px-[26px] justify-center items-center text-[#AD0369] text-center text-[16px] font-normal leading-[150%] transition-colors duration-300 hover:bg-[#AD0369] hover:text-white">
                VER TODOS
            </a>
        </div>

        @if($destacados->count() > 0)
        <div x-show="show"
             x-transition:enter="transition ease-out duration-700 delay-150"
             x-transition:enter-start="opacity-0 translate-y-6"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="swiper productosDestacadosSwiper ">
                <div class="swiper-wrapper">
                    @foreach($destacados as $producto)
                    <div class="swiper-slide">

                        <div class="bg-white rounded-[10px] border border-[#DEDFE0]  hover:shadow-sm transition-shadow overflow-hidden flex flex-col min-h-[440px]">

<div class="bg-[#F5F5F5] h-[237px] flex items-center p-2 justify-center overflow-hidden flex-shrink-0 relative">
    @if($producto->imagenPrincipal)
        <img src="{{ Storage::url($producto->imagenPrincipal->ruta) }}"
             alt="{!! $producto->descripcion_es !!}"
             class="w-full h-full object-contain">
    @else
        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    @endif
<div class="absolute inset-0  bg-black/10 pointer-events-none z-[1] transition-colors duration-300"></div>
    @if($producto->tipo)
        <span class="absolute bottom-2 left-2 text-[#AD0369] text-[14px] font-bold leading-[150%] uppercase px-2 py-1 rounded-sm">
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

                                <a  href="{{ route('productos.detalle', ['id' => $producto->id]) }}"
                                   class="prod-dest-btn relative z-10 flex w-full max-w-[253px] h-[36px] py-[11px] px-[26px] justify-center items-center rounded-[22px] border border-[#AD0369] bg-white text-[#AD0369] text-center text-[12px] font-normal leading-[150%] mt-auto self-start hover:bg-[#AD0369] hover:text-white transition-colors">
                                    VER DETALLE
                                </a>

                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination prod-dest-pagination"></div>
            </div>

        </div>
        @endif

    </div>

    <style>
        /* === Productos destacados responsive (max 1239px) === */
        @media (max-width: 1239px) {
            .prod-dest-section {
                padding-top: 48px !important;
            }
            .prod-dest-title {
                font-size: 26px !important;
            }
        }

        @media (max-width: 767px) {
            .prod-dest-section {
                padding-top: 36px !important;
            }
            .prod-dest-title {
                font-size: 22px !important;
            }
            .prod-dest-btn {
                width: 100% !important;
                align-self: stretch !important;
            }
        }

        .productosDestacadosSwiper {
            overflow: hidden;
            position: relative;
        }
        /* 768px+: slidesPerView auto, ancho fijo definido por CSS */
        .productosDestacadosSwiper .swiper-slide {
            flex-shrink: 0;
            height: auto;
        }
        @media (min-width: 1240px) {
            .productosDestacadosSwiper .swiper-slide {
                width: calc((100% - 96px) / 5);
            }
        }
        @media (min-width: 768px) and (max-width: 1239px) {
            .productosDestacadosSwiper .swiper-slide {
                width: calc((100% - 72px) / 4);
            }
        }
        /* 540–767px: slidesPerView auto, ancho relativo al viewport */
        @media (min-width: 540px) and (max-width: 767px) {
            .productosDestacadosSwiper .swiper-slide {
                width: 82vw;
            }
        }
        /* <540px: slidesPerView 1, Swiper controla el ancho — no override */
        @media (max-width: 539px) {
            .productosDestacadosSwiper .swiper-slide {
                width: 100%;
            }
        }

        /* Dots: solo mobile */
        .productosDestacadosSwiper .swiper-pagination { display: none; }
        @media (max-width: 639px) {
            .productosDestacadosSwiper { padding-bottom: 36px !important; }
            .productosDestacadosSwiper .swiper-pagination { display: block; }
        }
        .productosDestacadosSwiper .swiper-pagination-bullet {
            width: 8px; height: 8px;
            background: #DDDDE0; opacity: 1;
            transition: all 0.3s ease;
            border-radius: 9999px;
        }
        .productosDestacadosSwiper .swiper-pagination-bullet-active {
            width: 24px;
            background: #AD0369;
            border-radius: 4px;
        }
    </style>

    <script>
    (function() {
        let swiperInstance = null;

        function initSwiper() {
            if (swiperInstance) {
                swiperInstance.destroy(true, true);
                swiperInstance = null;
            }
            const el = document.querySelector('.productosDestacadosSwiper');
            if (!el) return;

            setTimeout(function() {
                swiperInstance = new Swiper('.productosDestacadosSwiper', {
                    slidesPerView: 'auto',
                    spaceBetween: 20,
                    speed: 600,
                    loop: {{ $destacados->count() > 5 ? 'true' : 'false' }},
                    grabCursor: true,
                    observer: true,
                    observeParents: true,
                    preventClicks: false,
                    preventClicksPropagation: false,
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
                            spaceBetween: 24,
                        },
                        540: {
                            slidesPerView: 'auto',
                            spaceBetween: 24,
                        },
                        768: {
                            slidesPerView: 'auto',
                            spaceBetween: 24,
                        },
                    }
                });
            }, 150);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSwiper);
        } else {
            initSwiper();
        }

        document.addEventListener('livewire:navigated', initSwiper);
        document.addEventListener('livewire:navigating', function() {
            if (swiperInstance) {
                swiperInstance.destroy(true, true);
                swiperInstance = null;
            }
        });
    })();
    </script>

</section>
