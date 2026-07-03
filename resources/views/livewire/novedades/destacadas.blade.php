<section class="novedades-dest-section bg-white w-full pt-[94px] pb-[72px] max-[1199px]:pt-[60px] max-[1199px]:pb-[80px] max-[767px]:pt-[40px] max-[767px]:pb-[48px]"
    x-data="{ show: false }"
    x-intersect.once.threshold.0.1="show = true">

    <div class="max-w-[1224px] mx-auto max-[1199px]:px-6 max-[767px]:px-4">

        <div class="flex items-center justify-between mb-[16px] transition-all duration-700 ease-out"
             :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">

            <h2 class="novedades-dest-title text-[#222] text-[32px] font-bold leading-[120%]">
                Novedades
            </h2>

            <a wire:navigate href="{{ route('productos') }}"
               class="rounded-[22px] border border-[#AD0369] bg-white inline-flex h-[44px] py-[11px] px-[26px] justify-center items-center text-[#AD0369] text-center text-[16px] font-normal leading-[150%] transition-colors duration-300 hover:bg-[#AD0369] hover:text-white">
                VER TODOS
            </a>
        </div>

        @if($destacadas->count() > 0)
        <div class="transition-all duration-700 delay-200 ease-out"
             :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
            <div class="swiper novedadesDestacadasSwiper">
                <div class="swiper-wrapper">
                    @foreach($destacadas as $nov)
                    <div class="swiper-slide">
                        <a href="/novedades/{{ $nov->id }}"
                           class="block rounded-[20px] border border-[#DDDDE0] w-full bg-white rounded-[4px] overflow-hidden cursor-pointer group max-[991px]:h-[500px] max-[767px]:h-[480px] max-[639px]:h-auto">

                            <div class="w-full h-[263px] overflow-hidden max-[991px]:h-[260px] max-[767px]:h-[240px] max-[639px]:h-[220px]">
                                <img src="{{ Storage::url($nov->image) }}"
                                     alt="{{ $nov->title }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                            </div>

                            <div class="flex flex-col  px-[16px] pb-[12px] justify-between h-[243px] max-[991px]:h-[240px] max-[767px]:h-[240px] max-[639px]:h-auto max-[639px]:p-4">
                                <div>
                                    <p class="text-[#AD0369] font-inter text-[14px] font-bold leading-[22px] mb-[10px] mt-[18px] max-[767px]:text-[15px] max-[639px]:text-[14px] max-[639px]:mt-0">
                                        {{ $nov->novcategories->first()->title ?? 'Novedad' }}
                                    </p>

                                    <h3 class="text-black  group-hover:text-[#AD0369] font-montserrat text-[24px] font-medium leading-[120%] mb-[16px] line-clamp-2 max-[991px]:text-[22px] max-[767px]:text-[20px] max-[639px]:text-[18px] max-[639px]:mb-3">
                                        {{ $nov->title }}
                                    </h3>

                                    <p class=" text-black  font-inter text-[15px] opacity-80 font-normal leading-[25px] line-clamp-3 max-[767px]:text-[15px] max-[639px]:text-[14px]">
                                        {{ preg_replace('/(&nbsp;)+$/', '', strip_tags($nov->description)) }}
                                    </p>
                                </div>

                                <span class="flex items-center justify-between text-[16px] font-normal leading-[150%] uppercase mt-[18px] max-[767px]:text-[15px] max-[639px]:text-[14px] text-[#B2B2B2] group-hover:text-[#AD0369] transition-colors duration-300">
                                    Leer más
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" class="transition-transform duration-300 group-hover:translate-x-1">
                                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination novedades-dest-pagination"></div>
            </div>
        </div>
        @else
        <p class="text-gray-500 text-center py-8">No hay novedades destacadas disponibles.</p>
        @endif

    </div>

    <style>
        /* === Novedades responsive (max 1239px) === */
        @media (max-width: 1239px) {
            .novedades-dest-title {
                font-size: 26px !important;
            }
        }
        @media (max-width: 767px) {
            .novedades-dest-title {
                font-size: 22px !important;
            }
        }

        /* === Swiper === */
        .novedadesDestacadasSwiper {
            overflow: hidden;
            position: relative;
        }

        /* 992px+: ancho fijo por CSS (slidesPerView auto) */
        .novedadesDestacadasSwiper .swiper-slide {
            width: 392px;
            flex-shrink: 0;
        }

        /* 640–991px: slidesPerView auto, ancho relativo */
        @media (min-width: 640px) and (max-width: 991px) {
            .novedadesDestacadasSwiper .swiper-slide {
                width: 82vw;
            }
        }

        /* <640px: slidesPerView 1, Swiper controla el ancho — no override */
        @media (max-width: 639px) {
            .novedadesDestacadasSwiper .swiper-slide {
                width: 100%;
            }
        }

        /* Dots: solo mobile */
        .novedadesDestacadasSwiper .swiper-pagination { display: none; }
        @media (max-width: 639px) {
            .novedadesDestacadasSwiper { padding-bottom: 36px !important; }
            .novedadesDestacadasSwiper .swiper-pagination { display: block; }
        }
        .novedadesDestacadasSwiper .swiper-pagination-bullet {
            width: 8px; height: 8px;
            background: #DDDDE0; opacity: 1;
            transition: all 0.3s ease;
            border-radius: 9999px;
        }
        .novedadesDestacadasSwiper .swiper-pagination-bullet-active {
            width: 24px;
            background: #AD0369;
            border-radius: 4px;
        }

        .novedadesDestacadasSwiper .swiper-button-next,
        .novedadesDestacadasSwiper .swiper-button-prev {
            color: rgba(255,255,255,1);
            width: 60px;
            height: 60px;
            opacity: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
            transition: all 0.3s ease;
            z-index: 10;
        }

        @media (max-width: 767px) {
            .novedadesDestacadasSwiper .swiper-button-next,
            .novedadesDestacadasSwiper .swiper-button-prev {
                width: 50px;
                height: 50px;
            }
        }

        @media (max-width: 639px) {
            .novedadesDestacadasSwiper .swiper-button-next,
            .novedadesDestacadasSwiper .swiper-button-prev {
                width: 40px;
                height: 40px;
            }
        }

        .novedadesDestacadasSwiper:hover .swiper-button-next,
        .novedadesDestacadasSwiper:hover .swiper-button-prev {
            opacity: 1;
        }

        .novedadesDestacadasSwiper .swiper-button-next:hover,
        .novedadesDestacadasSwiper .swiper-button-prev:hover {
            transform: scale(1.1);
        }

        .novedadesDestacadasSwiper .swiper-button-disabled {
            opacity: 0.35 !important;
            pointer-events: none;
        }
    </style>

    <script>
    (function() {
        let novedadesDestacadasSwiperInstance = null;

        function initNovedadesDestacadasSwiper() {
            if (novedadesDestacadasSwiperInstance) {
                novedadesDestacadasSwiperInstance.destroy(true, true);
                novedadesDestacadasSwiperInstance = null;
            }

            const swiperElement = document.querySelector('.novedadesDestacadasSwiper');
            if (!swiperElement) return;

            setTimeout(() => {
                novedadesDestacadasSwiperInstance = new Swiper('.novedadesDestacadasSwiper', {
                    slidesPerView: 'auto',
                    spaceBetween: 24,
                    speed: 400,
                    loop: true,
                    grabCursor: true,
                    observer: true,
                    observeParents: true,
                    slideToClickedSlide: false,
                    watchOverflow: true,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    pagination: {
                        el: swiperElement.querySelector('.swiper-pagination'),
                        clickable: true,
                    },
                    on: {
                        slideChangeTransitionEnd: function() { this.pagination.update(); },
                        loopFix: function() { this.pagination.update(); },
                    },
                    navigation: {
                        nextEl: '.novedades-destacadas-next',
                        prevEl: '.novedades-destacadas-prev',
                    },
                    breakpoints: {
                        0: {
                            spaceBetween: 12,
                            slidesPerView: 1,
                        },
                        640: {
                            spaceBetween: 16,
                            slidesPerView: 'auto',
                        },
                        768: {
                            spaceBetween: 20,
                            slidesPerView: 'auto',
                        },
                        1024: {
                            spaceBetween: 24,
                            slidesPerView: 'auto',
                        },
                    }
                });
            }, 150);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initNovedadesDestacadasSwiper);
        } else {
            initNovedadesDestacadasSwiper();
        }

        document.addEventListener('livewire:navigated', initNovedadesDestacadasSwiper);

        document.addEventListener('livewire:navigating', () => {
            if (novedadesDestacadasSwiperInstance) {
                novedadesDestacadasSwiperInstance.destroy(true, true);
                novedadesDestacadasSwiperInstance = null;
            }
        });
    })();
    </script>
</section>
