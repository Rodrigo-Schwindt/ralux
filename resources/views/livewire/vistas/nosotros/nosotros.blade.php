<div x-data="{ show: false, shownSection2: false }" x-init="setTimeout(() => show = true, 50)" x-show="show" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="w-full overflow-hidden">
    <div class=" pt-[44px]">
        <div class="max-w-[1224px] mx-auto px-4 lg:px-0">
            <nav class="flex items-center gap-1 text-[13px] font-inter">
                <a wire:navigate href="/" class="text-black hover:text-black transition-colors font-bold">Inicio</a>
                <span class="text-black/60">/</span>
                <span class="text-black/80 ">Productos</span>
            </nav>
        </div>
    </div>

    
    <section class="relative mt-[40px] md:mt-[60px] min-[1200px]:mt-[78px] mb-[60px] min-[1200px]:mb-[80px] px-4 min-[1200px]:px-0">
        <div class="max-w-[1224px] h-auto min-[1200px]:h-[600px] mx-auto grid grid-cols-1 min-[1200px]:grid-cols-2 gap-[32px] md:gap-[45px] min-[1200px]:gap-[55px] items-start">
    
            <div class="w-full h-[250px] md:h-[600px] min-[1200px]:h-[600px] animate-scale-in group overflow-hidden rounded-[20px]">
                <img src="{{ Storage::url($nosotros->image) }}"
                     alt="{{ $nosotros->title }}"
                     class="w-full h-full object-cover rounded-[6px] transition-transform duration-700 group-hover:scale-105">
            </div>
    
            <div class="flex flex-col w-full min-[1200px]:w-[538px] animate-slide-in-left">
                <h1 class="text-[#111010] font-inter text-[24px] md:text-[28px] min-[1200px]:text-[32px] font-semibold leading-normal tracking-[-0.01em] mt-[16px] md:mt-[27px] min-[1200px]:mt-[28px] mb-[28px] min-[1200px]:mb-[28px]">
                    {{ $nosotros->title }}
                </h1>
    
                <div class="[&>*]:text-black [&>*]:font-inter [&>*]:text-[15px] md:[&>*]:text-[16px] [&>*]:font-normal [&>*]:leading-[22px] [&>*]:m-0 [&>*]:mb-4 opacity-90">
                    {!!  $nosotros->description !!}
                </div>
            </div>
    
        </div>
    </section>
    
    <section class="px-4 min-[1200px]:px-0 bg-[#F8F8F8]  pb-[75px]"
             x-intersect.once="shownSection2 = true">
    
        <h2 class="max-w-[1224px] mx-auto text-[#111010] font-inter text-[26px] md:text-[28px] min-[1200px]:text-[32px] font-semibold leading-normal pt-[10px] min-[1200px]:pt-[50px] pb-[30px] min-[1200px]:pb-[25px] text-center min-[1200px]:text-left transition-opacity duration-700"
            :class="shownSection2 ? 'opacity-100' : 'opacity-0'">
            ¿Por qué elegirnos?
        </h2>
    
        <div class="max-w-[1224px] mx-auto grid grid-cols-1 md:grid-cols-3 gap-[24px] min-h-[288px]">
    
            <div class="px-[25px] pt-[61px] min-h-[392px] flex flex-col bg-white rounded-[20px] items-center animate-card-1 transition-all duration-300">
    <div class='w-[60px] h-[60px] rounded-[100px] bg-white flex items-center justify-center'>
        @if($nosotros->image_1)
            <img src="{{ asset('storage/' . $nosotros->image_1) }}" class="h-full w-full object-contain">
        @endif
    </div>
    <h3 class="text-[#1D1D1B] text-center font-inter text-[20px] font-bold leading-[120%] mb-[20px] mt-[32px]">
        {{ $nosotros->title_1 }}
    </h3>
    <div class="w-full flex flex-col items-center text-center text-black font-inter text-[16px] font-normal leading-[150%] [&_p]:text-center [&_p]:mx-auto">
        {!! str_replace('&nbsp;', ' ', $nosotros->description_1) !!}
    </div>
</div>

<div class="px-[25px] pt-[61px] min-h-[392px] flex flex-col items-center bg-white rounded-[20px] animate-card-2 transition-all duration-300">
    <div class='w-[60px] h-[60px] rounded-[100px] bg-white flex items-center justify-center'>
        @if($nosotros->image_2)
            <img src="{{ asset('storage/' . $nosotros->image_2) }}" class="h-full w-full object-contain">
        @endif
    </div>
    <h3 class="text-[#1D1D1B] text-center font-inter text-[20px] font-bold leading-[120%] mb-[20px] mt-[32px]">
        {{ $nosotros->title_2 }}
    </h3>
    <div class="w-full flex flex-col items-center text-center text-black font-inter text-[16px] font-normal leading-[150%] [&_p]:text-center [&_p]:mx-auto">
        {!! str_replace('&nbsp;', ' ', $nosotros->description_2) !!}
    </div>
</div>

<div class="px-[25px] pt-[61px] min-h-[392px] flex flex-col items-center rounded-[20px] bg-white animate-card-3 transition-all duration-300">
    <div class='w-[60px] h-[60px] rounded-[100px] bg-white flex items-center justify-center'>
        @if($nosotros->image_3)
            <img src="{{ asset('storage/' . $nosotros->image_3) }}" class="h-full w-full object-contain">
        @endif
    </div>
    <h3 class="text-[#1D1D1B] text-center font-inter text-[20px] font-bold leading-[120%] mb-[20px] mt-[32px]">
        {{ $nosotros->title_3 }}
    </h3>
    <div class="w-full flex flex-col items-center text-center text-black font-inter text-[16px] font-normal leading-[150%] [&_p]:text-center [&_p]:mx-auto">
        {!! str_replace('&nbsp;', ' ', $nosotros->description_3) !!}
    </div>
</div>
    
        </div>
    </section>
    <style> @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    
    .animate-slide-in-left { animation: slideInLeft 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
    .animate-slide-in-right { animation: slideInRight 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
    .animate-scale-in { animation: scaleIn 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
    
    .animate-card-1 { animation: fadeInUp 0.7s ease-out 0.1s forwards; opacity: 0; }
    .animate-card-2 { animation: fadeInUp 0.7s ease-out 0.2s forwards; opacity: 0; }
    .animate-card-3 { animation: fadeInUp 0.7s ease-out 0.3s forwards; opacity: 0; }
    </style>
    
    </div>