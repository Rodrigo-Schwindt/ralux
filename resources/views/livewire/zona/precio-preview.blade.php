<div class="w-full max-w-[1224px] mx-auto px-4 lg:px-0"
     x-data="{ show: false }"
     x-init="setTimeout(() => show = true, 50)"
     x-show="show"
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 transform -translate-y-4"
     x-transition:enter-end="opacity-100 transform translate-y-0">

    <div class="pt-[44px]">
        <nav class="flex items-center gap-1 text-[13px] font-inter">
            <a wire:navigate href="/zona-privada/productos" class="text-black hover:text-black transition-colors font-bold">Inicio</a>
            <span class="text-black/60">/</span>
            <a wire:navigate href="{{ route('cliente.precios') }}" class="text-black/80 hover:text-black transition-colors">Lista de precios</a>
            <span class="text-black/60">/</span>
            <span class="text-black/80">Vista online</span>
        </nav>
    </div>

    <div class="mt-[58px] pb-[127px]">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5 mb-8">
            <div>
                <p class="text-[#AD0369] font-inter text-[13px] font-semibold uppercase tracking-wide">Vista online</p>
                <h1 class="mt-2 text-black font-inter text-[28px] lg:text-[36px] font-semibold leading-tight">
                    {{ $precio->title }}
                </h1>
                <p class="mt-2 text-black/50 font-inter text-[14px]">
                    {{ strtoupper($extension) }} convertido a tabla para consulta rapida.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('cliente.precios') }}"
                   class="h-[44px] px-6 rounded-[22px] border border-[#AD0369] bg-white text-[#AD0369] text-center font-inter text-[14px] uppercase flex items-center justify-center hover:bg-[#AD0369] hover:text-white transition-colors">
                    Volver
                </a>
                <button wire:click="descargar"
                        class="h-[44px] px-6 rounded-[22px] bg-[#AD0369] text-white text-center font-inter text-[14px] uppercase flex items-center justify-center hover:bg-[#AD0369]/90 transition-colors">
                    Descargar
                </button>
            </div>
        </div>

        <div class="bg-white border border-[#E5E5E5] rounded-[20px] overflow-hidden">
            <div class="overflow-auto max-h-[70vh]">
                <table class="min-w-full border-collapse text-left font-inter text-[13px]">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-[#F5F5F5] text-[#222]">
                            @foreach($headers as $header)
                                <th class="px-4 py-3 font-semibold border-b border-r border-[#E5E5E5] whitespace-nowrap">
                                    {{ $header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr class="odd:bg-white even:bg-[#FAFAFA] hover:bg-[#AD0369]/5 transition-colors">
                                @foreach($headers as $index => $header)
                                    <td class="px-4 py-3 text-black/70 border-b border-r border-[#E5E5E5] align-top">
                                        {{ $row[$index] ?? '' }}
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ max(count($headers), 1) }}" class="px-4 py-16 text-center text-black/40">
                                    No hay datos para mostrar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
