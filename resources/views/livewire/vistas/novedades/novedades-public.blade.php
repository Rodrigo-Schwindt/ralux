@php
    use Carbon\Carbon;
@endphp

<div
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 50)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 transform -translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
>

@if($banner && $banner->image_banner)
    <section class="relative w-full h-[380px] max-[1199px]:h-[320px] max-[767px]:h-[280px] max-[639px]:h-[240px]">
        <img src="{{ asset('storage/' . $banner->image_banner) }}" 
             alt="{{ $banner->title }}"
             class="w-full h-full object-cover grayscale-[100%] contrast-155">

        <div class="absolute top-[114px] left-0 right-0 z-30 max-[1199px]:top-[90px] max-[1199px]:px-4 max-[767px]:top-[70px] max-[639px]:top-[60px]">
            <div class="max-w-[1224px] mx-auto">
                <nav class="text-white font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] flex items-center gap-1">
                    <a wire:navigate href="{{ url('/') }}" class="text-white font-montserrat text-[12px] max-[639px]:text-[11px] font-bold leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Inicio</a>
                    <span class="text-white font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">›</span>
                    <span class="text-white font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Novedades</span>
                </nav>
            </div>
        </div>

        <div class="absolute inset-0 bg-[rgba(170,65,65,0.5)]"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-black/20 to-black/20"></div>

        <div class="absolute inset-0 flex top-[245px] max-w-[1224px] mx-auto max-[1199px]:top-[195px] max-[1199px]:px-4 max-[767px]:top-[170px] max-[639px]:top-[150px]">
            <h1 class="text-white font-inter text-[40px] font-bold leading-normal max-[1199px]:text-[36px] max-[767px]:text-[32px] max-[639px]:text-[24px] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">
                Novedades
            </h1>
        </div>
    </section>
@endif

    <section class="bg-white pt-[80px] pb-[120px] max-[1199px]:pt-16 max-[1199px]:pb-20 max-[1199px]:px-4 max-[639px]:pt-12 max-[639px]:pb-16">
        <div class="max-w-[1224px] mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px] max-[1199px]:grid-cols-2 max-[767px]:grid-cols-1 max-[1199px]:gap-5">
                @foreach ($novedades as $item)
                <a href="/novedades/{{ $item->id }}"
                   class="block rounded-[20px] border border-[#DDDDE0] w-full bg-white overflow-hidden cursor-pointer group max-[991px]:h-[500px] max-[767px]:h-[480px] max-[639px]:h-auto">

                    <div class="w-full h-[263px] overflow-hidden max-[991px]:h-[260px] max-[767px]:h-[240px] max-[639px]:h-[220px]">
                        <img src="{{ Storage::url($item->image) }}"
                             alt="{{ $item->title }}"
                             loading="lazy"
                             class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                    </div>

                    <div class="flex flex-col px-[16px] pb-[12px] justify-between h-[243px] max-[991px]:h-[240px] max-[767px]:h-[240px] max-[639px]:h-auto max-[639px]:p-4">
                        <div>
                            <p class="text-[#AD0369] font-inter text-[14px] font-bold leading-[22px] mb-[10px] mt-[18px] max-[767px]:text-[15px] max-[639px]:text-[14px] max-[639px]:mt-0">
                                {{ $item->novcategories->first()->title ?? 'Novedad' }}
                            </p>

                            <h3 class="text-black group-hover:text-[#AD0369] font-montserrat text-[24px] font-medium leading-[120%] mb-[16px] line-clamp-2 max-[991px]:text-[22px] max-[767px]:text-[20px] max-[639px]:text-[18px] max-[639px]:mb-3">
                                {{ $item->title }}
                            </h3>

                            <p class="text-black font-inter text-[15px] opacity-80 font-normal leading-[25px] line-clamp-3 max-[767px]:text-[15px] max-[639px]:text-[14px]">
                                {{ preg_replace('/(&nbsp;)+$/', '', strip_tags($item->description)) }}
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
                @endforeach
            </div>
        </div>
    </section>

</div>