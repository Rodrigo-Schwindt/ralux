<section class="bg-white mt-[80px] max-lg:mt-[60px] overflow-hidden" x-data="{ shown: false }" x-intersect.once.threshold.0.2="shown = true" > @php $nosotros = \App\Models\Nosotros::first(); @endphp

    @if($nosotros)
    <div class="nosotros-outer flex flex-col min-[1200px]:flex-row w-full min-[1200px]:h-[600px] h-[600px] gap-0 min-[1200px]:gap-0">
        
        <div 
            class="
                w-full min-[1200px]:w-1/2 
                h-[600px] 
                max-[1199px]:h-[400px] max-md:h-[300px] max-[767px]:h-[280px] 
                flex-shrink-0 
                bg-[#111] 
                transition-all duration-1000 ease-out transform
            "
            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-10'"
        >
            @if($nosotros?->image_home)
            <img 
                src="{{ Storage::url($nosotros->image_home) }}"
                alt="{{ $nosotros->title_home }}"
                class="
                    w-full h-full object-cover object-center
                "
            >
            @endif
        </div>
    
        <div 
            class="
                w-full min-[1200px]:w-1/2 
                text-[#111] flex flex-col
                transition-all duration-1000 delay-300 ease-out transform bg-[#222]
            "
            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-10'"
        >
            <div class="
                flex flex-col justify-between h-full 
                min-[1200px]:mt-[75px] min-[1200px]:ml-[64px]
                max-[1199px]:py-[65px] max-[1199px]:px-[32px] 
                max-md:py-[30px] max-md:px-[20px]
            ">
                
                <div class="nosotros-text-wrapper min-[1200px]:w-[301px] w-[80%] max-[600px]:text-center">
                    <h2 class="
                        text-white text-[32px] font-bold leading-[120%]
                    ">
                        {{ $nosotros->title_home }}
                    </h2>
    
                    <div class="
                        text-white text-[15px] font-normal leading-[150%]
                        min-[1200px]:w-[480px] 
                        mt-[41px] max-md:text-center max-md:text-[15px] opacity-80
                    ">
                        {!! str_replace('&nbsp;', ' ', $nosotros->description_home) !!}
                    </div>
                </div>
    
                <div class="
                    mt-auto 
                    max-[1199px]:mt-[30px] 
                    max-md:mt-[24px] 
                    flex min-[1200px]:justify-start justify-center
                ">
                    <a 
                        wire:navigate 
                        href="/nosotros"
                        class="
                            nosotros-btn mb-[75px] inline-flex h-[44px] py-[11px] px-[26px] justify-center items-center rounded-[22px] bg-[#AD0369] text-white text-center text-[16px] font-normal leading-[150%] transition-colors duration-300 hover:bg-[#D81B60]
                        "
                    >
                        Más info
                    </a>
                </div>
    
            </div>
        </div>
    
    </div>
    @endif
    
    <style>
        /* === Nosotros responsive (max 1199px) === */
        @media (max-width: 1199px) {
            .nosotros-outer {
                height: auto !important;
            }
            .nosotros-btn {
                margin-bottom: 36px !important;
            }
        }

        @media (max-width: 767px) {
            .nosotros-text-wrapper {
                width: 100% !important;
            }
            .nosotros-btn {
                margin-bottom: 20px !important;
            }
        }

        .richedit-reset * {
            all: unset;
            font-family: inherit;
            font-size: inherit;
            color: inherit;
            line-height: inherit;
            display: revert;
        }
    
        .richedit-reset p {
            margin-bottom: 1rem;
            display: block;
        }
    </style>
    </section>