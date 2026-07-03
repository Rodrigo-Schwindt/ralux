<section class="max-w-[1224px] mx-auto px-4 lg:px-0"
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 50)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 -translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0">

    <div class=" pt-[44px]">
        <div class="max-w-[1224px] mx-auto px-4 lg:px-0">
            <nav class="flex items-center gap-1 text-[13px] font-inter">
                <a wire:navigate href="/" class="text-black hover:text-black transition-colors font-bold">Inicio</a>
                <span class="text-black/60">/</span>
                <span class="text-black/80 ">Calidad</span>
            </nav>
        </div>
    </div>

    @if($servicio)

    <div class="grid grid-cols-1 md:grid-cols-2 gap-[33px] items-start pt-[78px] max-md:pt-8 ">

        <div class="md:hidden w-full h-[260px] max-[480px]:h-[200px] rounded-xl overflow-hidden opacity-0 animate-[fadeInRight_0.8s_ease-out_0.4s_forwards]">
            @if($servicio->image)
                <img src="{{ asset('storage/' . $servicio->image) }}"
                     alt="{{ $servicio->title }}"
                     class="w-full h-full object-cover">
            @endif
        </div>

        <div class="w-full max-md:text-center opacity-0 animate-[fadeInLeft_0.8s_ease-out_0.2s_forwards]">
            <h1 class="text-[#222] font-inter text-[32px] font-bold leading-[120%] max-md:text-[26px] max-[480px]:text-[22px] mb-[27px]">
                {{ $servicio->title }}
            </h1>

            @if($servicio->description_1)
                <div class="text-[#222] opacity-90 font-inter text-[16px] leading-[160%] max-md:text-[14px] [&_strong]:font-bold [&_b]:font-bold">
                    {!! str_replace('&nbsp;', ' ', $servicio->description_1) !!}
                </div>
            @endif
        </div>

        <div class="hidden md:block w-full h-[600px] rounded-xl overflow-hidden opacity-0 animate-[fadeInRight_0.8s_ease-out_0.4s_forwards]">
            @if($servicio->image)
                <img src="{{ asset('storage/' . $servicio->image) }}"
                     alt="{{ $servicio->title }}"
                     class="w-full h-full object-cover">
            @endif
        </div>

    </div>

    @if($servicio->downloads->count() > 0)
    <div class="pb-[114px] max-md:pb-10 opacity-0 animate-[fadeIn_0.8s_ease-out_0.6s_forwards] pt-[80px]">

        <h2 class="text-[#222] font-inter text-[24px] font-semibold mb-4">Descargas</h2>

        <div class="flex flex-col gap-3">
            @foreach($servicio->downloads as $dl)
            <a href="{{ Storage::url($dl->file) }}"
               target="_blank"
               download
               class="group inline-flex items-center gap-4 bg-[#F4F4F4]  pr-[20px] rounded-[30px] border border-[#DEDFE0] bg-[#F5F5F5] transition-colors   max-w-[600px] w-full">

                <div class="flex-shrink-0 bg-white w-[80px] h-[80px] flex items-center rounded-l-[30px] justify-end px-[1px]">
                    @if($dl->image)
                        <img src="{{ asset('storage/' . $dl->image) }}"
                             alt="logo"
                             class="w-[66px] h-full object-contain">
                    @else
                        <svg class="w-8 h-8 text-[#D81B60]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    @endif
                </div>

                <div class="flex-1 min-w-0 ">
                    <p class="text-[#222] font-inter text-[16px] font-semibold leading-tight truncate">
                        {{ $dl->title ?: 'Documento' }}
                    </p>
                    @php
                        $ext = strtoupper(pathinfo($dl->file, PATHINFO_EXTENSION));
                        $fileSize = '';
                        if ($dl->file && Storage::disk('public')->exists($dl->file)) {
                            $bytes = Storage::disk('public')->size($dl->file);
                            $fileSize = $bytes >= 1048576
                                ? round($bytes / 1048576, 1) . ' MB'
                                : round($bytes / 1024) . ' KB';
                        }
                    @endphp
                    @if($ext)
                        <p class="text-[#888] font-inter text-[14px] mt-0.5">
                            {{ $ext }}{{ $fileSize ? ' - ' . $fileSize : '' }}
                        </p>
                    @endif
                </div>

                <div class="flex-shrink-0 text-[#D81B60] group-hover:text-[#C2185B] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>

            </a>
            @endforeach
        </div>
    </div>
    @endif

    @else
        <p class="text-gray-500 py-12">No hay contenido cargado.</p>
    @endif

    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0); }
        }
    </style>

</section>
