<div
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 50)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 transform -translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0">

    <div>
    <div class=" pt-[44px]">
        <div class="max-w-[1224px] mx-auto px-4 lg:px-0">
            <nav class="flex items-center gap-1 text-[13px] font-inter">
                <a wire:navigate href="/" class="text-black hover:text-black transition-colors font-bold">Inicio</a>
                <span class="text-black/60">/</span>
                <span class="text-black/80 ">Contacto</span>
            </nav>
        </div>
    </div>
        <div class="max-w-[1224px] mx-auto max-[1199px]:px-4  flex items-center mt-[78px] max-[1199px]:mt-[40px] max-[639px]:mt-8 justify-between  transition-all duration-700 ease-out"
             :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">

            <h2 class="text-[#222] text-[32px] font-bold leading-[120%]">
                Contacto
            </h2>

        </div>
        <div class="max-w-[1224px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-[24px] max-[1199px]:flex max-[1199px]:flex-col max-[1199px]:px-4 max-[1199px]:gap-8">
            <div class="lg:col-span-4 max-[1199px]:order-2">
                <div class="max-[1199px]:w-full mt-[24px] animate-fadeInLeft">
                    @if($contact?->direction_adm)
                    <div class="flex items-start mb-5 sm:mb-[20px] w-[405px] max-[1199px]:w-full max-[639px]:mb-4 transition-transform duration-200">
<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" class="mt-2" viewBox="0 0 24 24" fill="none">
  <path d="M20 10C20 16 12 22 12 22C12 22 4 16 4 10C4 7.87827 4.84285 5.84344 6.34315 4.34315C7.84344 2.84285 9.87827 2 12 2C14.1217 2 16.1566 2.84285 17.6569 4.34315C19.1571 5.84344 20 7.87827 20 10Z" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contact->direction_adm) }}" target="_blank" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[15px] max-[639px]:text-[14px] max-[639px]:ml-2">
                            {{ $contact->direction_adm }}
                        </a>
                    </div>
                    @endif
            

                    
                    @php
                        $departamentos = [
                            'Comercial'  => ['phone' => $contact?->phone_amd,  'mail' => $contact?->mail_comercial],
                            'Técnico'    => ['phone' => $contact?->phone_sale, 'mail' => $contact?->mail_tecnico],
                            'Compras'    => ['phone' => $contact?->maps_adm,   'mail' => $contact?->mail_compras],
                            'Administración'  => ['phone' => $contact?->maps_sale,  'mail' => $contact?->mail_logistica],
                        ];
                    @endphp

                    @foreach($departamentos as $nombre => $datos)
                        @if($datos['phone'] || $datos['mail'])
                        <div class="mb-5 sm:mb-[20px] max-[639px]:mb-4">
                            <h3 class="text-[#AD0369] font-inter text-[16px] font-semibold leading-normal tracking-[-0.01em] mb-2 max-[1199px]:text-[15px] max-[639px]:text-[14px]">
                                {{ $nombre }}
                            </h3>

                            @if($datos['phone'])
                            <div class="flex items-center mb-2 transition-transform duration-200">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M22.0004 16.92V19.92C22.0016 20.1985 21.9445 20.4742 21.8329 20.7293C21.7214 20.9845 21.5577 21.2136 21.3525 21.4019C21.1473 21.5901 20.905 21.7335 20.6412 21.8227C20.3773 21.9119 20.0978 21.9451 19.8204 21.92C16.7433 21.5856 13.7874 20.5341 11.1904 18.85C8.77425 17.3147 6.72576 15.2662 5.19042 12.85C3.5004 10.2412 2.44866 7.271 2.12042 4.18C2.09543 3.90347 2.1283 3.62476 2.21692 3.36163C2.30555 3.09849 2.44799 2.85669 2.63519 2.65162C2.82238 2.44655 3.05023 2.28271 3.30421 2.17052C3.5582 2.05834 3.83276 2.00026 4.11042 2H7.11042C7.59573 1.99523 8.06621 2.16708 8.43418 2.48353C8.80215 2.79999 9.0425 3.23945 9.11042 3.72C9.23704 4.68007 9.47187 5.62273 9.81042 6.53C9.94497 6.88793 9.97408 7.27692 9.89433 7.65088C9.81457 8.02485 9.62928 8.36811 9.36042 8.64L8.09042 9.91C9.51398 12.4135 11.5869 14.4864 14.0904 15.91L15.3604 14.64C15.6323 14.3711 15.9756 14.1859 16.3495 14.1061C16.7235 14.0263 17.1125 14.0555 17.4704 14.19C18.3777 14.5286 19.3204 14.7634 20.2804 14.89C20.7662 14.9585 21.2098 15.2032 21.527 15.5775C21.8441 15.9518 22.0126 16.4296 22.0004 16.92Z" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                                <a href="tel:{{ $datos['phone'] }}" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[15px] max-[639px]:text-[14px] max-[639px]:ml-2">
                                    {{ $datos['phone'] }}
                                </a>
                            </div>
                            @endif

                            @if($datos['mail'])
                            <div class="flex items-center transition-transform duration-200">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M20 4H4C2.89543 4 2 4.89543 2 6V18C2 19.1046 2.89543 20 4 20H20C21.1046 20 22 19.1046 22 18V6C22 4.89543 21.1046 4 20 4Z" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M22 7L13.03 12.7C12.7213 12.8934 12.3643 12.996 12 12.996C11.6357 12.996 11.2787 12.8934 10.97 12.7L2 7" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                                <a href="mailto:{{ $datos['mail'] }}" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[15px] max-[639px]:text-[14px] max-[639px]:ml-2 break-all">
                                    {{ $datos['mail'] }}
                                </a>
                            </div>
                            @endif
                        </div>
                        @endif
                    @endforeach


                </div>
            </div>

            <div class="lg:col-span-8 mt-[10px] max-[1199px]:order-1">
                <div class="bg-white animate-fadeInRight">
                    <form wire:submit.prevent="submit" class="space-y-6 pt-[15px] max-[1199px]:space-y-4 max-[639px]:space-y-3 max-[639px]:pt-2">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 max-[1199px]:grid-cols-1 max-[1199px]:gap-4">
                            <div class="transition-all duration-200">
                                <label for="name" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[15px] max-[639px]:text-[14px]">Nombre y apellido*</label>
                                <input type="text" id="name" wire:model.blur="name" class="px-5 mt-3 rounded-[20px] sm:mt-[16px] w-full h-[50px] max-[639px]:h-[44px] border border-[#E7E7E7] focus:border-[#E40044] focus:outline-none focus:ring-2 focus:ring-[#E40044]/20 transition-all duration-200 @error('name') border-red-500 @enderror max-[1199px]:mt-2 max-[639px]:px-4 max-[639px]:text-[14px]">
                                @error('name') <p class="mt-1 text-sm max-[639px]:text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="transition-all duration-200">
                                <label for="company" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[15px] max-[639px]:text-[14px]">Empresa*</label>
                                <input type="text" id="company" wire:model.blur="company" class="px-5 mt-3 rounded-[20px] sm:mt-[16px] w-full h-[50px] max-[639px]:h-[44px] border border-[#E7E7E7] focus:border-[#E40044] focus:outline-none focus:ring-2 focus:ring-[#E40044]/20 transition-all duration-200 @error('company') border-red-500 @enderror max-[1199px]:mt-2 max-[639px]:px-4 max-[639px]:text-[14px]">
                                @error('company') <p class="mt-1 text-sm max-[639px]:text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 max-[1199px]:grid-cols-1 max-[1199px]:gap-4">
                            <div class="transition-all duration-200">
                                <label for="email" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[15px] max-[639px]:text-[14px]">E-mail*</label>
                                <input type="email" id="email" wire:model.blur="email" class="px-5 mt-3 rounded-[20px] sm:mt-[16px] w-full h-[50px] max-[639px]:h-[44px] border border-[#E7E7E7] focus:border-[#E40044] focus:outline-none focus:ring-2 focus:ring-[#E40044]/20 transition-all duration-200 @error('email') border-red-500 @enderror max-[1199px]:mt-2 max-[639px]:px-4 max-[639px]:text-[14px]">
                                @error('email') <p class="mt-1 text-sm max-[639px]:text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="transition-all duration-200">
                                <label for="phone" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[15px] max-[639px]:text-[14px]">Celular*</label>
                                <input type="text" id="phone" wire:model.blur="phone" class="px-5 mt-3 rounded-[20px] sm:mt-[16px] w-full h-[50px] max-[639px]:h-[44px] border border-[#E7E7E7] focus:border-[#E40044] focus:outline-none focus:ring-2 focus:ring-[#E40044]/20 transition-all duration-200 @error('phone') border-red-500 @enderror max-[1199px]:mt-2 max-[639px]:px-4 max-[639px]:text-[14px]">
                                @error('phone') <p class="mt-1 text-sm max-[639px]:text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 max-[1199px]:grid-cols-1 max-[1199px]:gap-4">

                            <div class="transition-all duration-200">
                                <label for="message" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] flex mb-4 sm:mb-[16px] max-[1199px]:text-[15px] max-[639px]:text-[14px] max-[1199px]:mb-2">Mensaje*</label>
                                <textarea 
                                    id="message" 
                                    wire:model.blur="message" 
                                    rows="6" 
                                    class="px-5 py-3 w-full h-[140px] max-[639px]:h-[120px] rounded-[20px] border border-[#E7E7E7] focus:border-[#E40044] focus:outline-none focus:ring-2 focus:ring-[#E40044]/20 transition-all duration-200 resize-none @error('message') border-red-500 @enderror max-[639px]:px-4 max-[639px]:py-2 max-[639px]:text-[14px]"
                                ></textarea>
                                @error('message') <p class="mt-1 text-sm max-[639px]:text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex flex-col justify-end sm:flex-row sm:items-end sm:justify-between gap-4 sm:gap-0 max-[1199px]:flex-col max-[1199px]:gap-4 max-[639px]:gap-3">
                                <div class="flex flex-col justify-between w-full max-[1199px]:flex-col gap-[13px] max-[1199px]:gap-3">
                                    <p class="text-[#222] font-montserrat text-[18px] font-normal leading-[150%]  max-[1199px]:text-[15px] max-[639px]:text-[13px] max-[1199px]:self-start">*Campos obligatorios</p>

                                    <button 
                                        type="submit" 
                                        class="w-full max-[1199px]:w-full h-[44px] max-[639px]:h-[40px] flex w-[388px] h-[44px] py-[11px] px-[26px] justify-center items-center rounded-[22px] bg-[#AD0369] text-white text-center text-[16px] font-normal leading-[150%] cursor-pointer">
                                        ENVIAR CONSULTA
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>

        @if($contact?->frame_adm)
        <div class="mt-[98px] mb-[80px] sm:mt-[40px] max-w-[1224px] mx-auto max-[1199px]:mt-[60px] max-[1199px]:px-4 max-[639px]:mt-12 max-[639px]:mb-12">
            <div wire:ignore class="w-full h-[574px] sm:h-[484px] relative max-[1199px]:h-[400px] max-[767px]:h-[350px] max-[639px]:h-[300px] rounded-lg overflow-hidden animate-fadeIn [&_iframe]:grayscale [&_iframe]:w-full [&_iframe]:h-full [&_iframe]:border-0">
                {!! $contact->frame_adm !!}
            </div>
        </div>
        @endif
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-fadeInLeft {
            animation: fadeInLeft 0.6s ease-out forwards;
        }

        .animate-fadeInRight {
            animation: fadeInRight 0.6s ease-out forwards;
        }

        iframe {
            width: 100% !important;
            height: 100% !important;
            border: 0;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const iframe = document.querySelector('iframe');
            if (iframe) {
                iframe.style.pointerEvents = 'auto';
            }
        });
    </script>

</div>
