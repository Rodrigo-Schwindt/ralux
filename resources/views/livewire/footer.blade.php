<footer class="bg-[#222] relative text-white lg:h-[436px] flex flex-col">

    <div
        x-data="{ show: false, title: '', message: '', type: 'success', timer: null }"
        x-on:toast.window="
            title = $event.detail.title ?? ($event.detail.type === 'success' ? 'Listo' : 'Atencion');
            message = $event.detail.message;
            type = $event.detail.type ?? 'success';
            show = true;
            clearTimeout(timer);
            timer = setTimeout(() => show = false, 4200);
        "
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-[0.98]"
        class="fixed top-5 right-5 z-[9999] w-[calc(100vw-2.5rem)] max-w-[380px] rounded-2xl border bg-white/95 shadow-[0_18px_50px_rgba(0,0,0,0.18)] backdrop-blur-md overflow-hidden"
        :class="type === 'success' ? 'border-[#EF338C]/30' : 'border-red-300'"
    >
        <div class="flex items-start gap-3 p-4">
            <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full"
                 :class="type === 'success' ? 'bg-[#AD0369]/10 text-[#AD0369]' : 'bg-red-100 text-red-600'">
                <svg x-show="type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg x-show="type !== 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-[14px] font-semibold text-[#222]" x-text="title"></p>
                <p class="text-[13px] text-[#585858] mt-0.5" x-text="message"></p>
            </div>
            <button type="button" class="text-[#777] hover:text-[#111] cursor-pointer" @click="show=false" aria-label="Cerrar toast">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="h-[3px] w-full"
             :class="type === 'success' ? 'bg-[#AD0369]/20' : 'bg-red-100'">
            <div class="h-full animate-toast-progress"
                 :class="type === 'success' ? 'bg-gradient-to-r from-[#AD0369] to-[#FD359D]' : 'bg-red-500'"></div>
        </div>
    </div>

    <div class="flex-1 py-8 lg:py-0 overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-0">
            <div class="flex flex-col lg:flex-row lg:max-w-[1224px] lg:mx-auto lg:mt-[61px] gap-8 lg:gap-0"
                 x-data="{ animate: false }"
                 x-init="setTimeout(() => animate = true, 100)"
                 x-show="animate"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                
                @if($contactData && ($contactData->icono_1 || $contactData->icono_3))
                <div class="text-center lg:text-left w-full lg:w-auto">
                    @if($contactData->icono_3)
                    <a wire:navigate href="{{ url('/') }}" class="inline-block">
                        <img src="{{ Storage::url($contactData->icono_3) }}" 
                             alt="Logo"
                             class="w-fit h-[95px] object-contain cursor-pointer mx-auto lg:mx-0">
                    </a>
                    @endif
                </div>
                @endif
                
                <div class="text-center lg:text-left lg:pl-[145px] flex flex-col sm:flex-row gap-8 sm:gap-12 lg:gap-0 w-full lg:w-auto">
                        <div class="mx-auto lg:mx-0 w-full sm:w-auto">
                            <h3 class="text-white font-inter text-[22px] sm:text-[22px] font-bold leading-normal mb-4 lg:mb-[24px]">Secciones</h3>
                            <ul class="text-white font-inter text-[14px] sm:text-[17px] font-normal leading-normal space-y-2 lg:space-y-0 opacity-75">
                                <li><a wire:navigate href="/nosotros" class="hover:underline block lg:mb-[9px]">Nosotros</a></li>
                                <li><a wire:navigate href="/productos" class="hover:underline block lg:mb-[9px]">Productos</a></li>
                                <li><a wire:navigate href="/catalogos" class="hover:underline block ">Catálogo</a></li>
                            </ul>
                        </div>
                    
                        <ul class="text-white font-inter text-[14px] sm:text-[17px] font-normal leading-normal space-y-2 lg:space-y-0 opacity-75 lg:gap-[9px] lg:mt-[56px] w-full sm:w-fit lg:ml-[71px] mx-auto lg:mx-0 text-center sm:text-left">
                            <li><a wire:navigate href="/servicios" class="hover:underline block lg:mb-[9px]">Calidad</a></li>
                            <li><a wire:navigate href="/novedades" class="hover:underline block lg:mb-[9px]">Novedades</a></li>
                            <li><a wire:navigate href="/contacto" class="hover:underline block ">Contacto</a></li>

                        </ul>
                </div>

                <div class="text-center lg:text-left lg:pl-[38px] w-full lg:w-auto z-5 relative">
                        <h3 class="text-white font-inter text-[20px] sm:text-[21px] font-semibold leading-normal mb-4 lg:mb-[23px]">Suscribite al Newsletter</h3>

                        <form wire:submit.prevent="subscribe" class="w-full max-w-[288px] mx-auto lg:mx-0">
                            <div class="relative flex items-center w-[270px] h-[45px] rounded-[20px] border border-[#E5E5E5] p-2 mb-4 lg:mb-[28px]">
                                <input 
                                    type="email"
                                    wire:model="newsletterEmail"
                                    placeholder="Email"
                                    class="bg-transparent text-white placeholder-white/90 text-[14px] w-full focus:outline-none pl-[8px] z-5 relative"
                                />
                                <button 
                                    type="submit" 
                                    class="absolute z-10 right-3 text-[#AD0369] cursor-pointer transition-transform duration-200 hover:translate-x-[3px] flex items-center justify-center"
                                    aria-label="Suscribirse">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" class="pointer-events-none">
                                        <path d="M5 12H19M19 12L12 5M19 12L12 19"
                                              stroke="currentColor" stroke-width="2"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        
                            @error('newsletterEmail')
                                <p class="text-red-400 text-sm mt-2 text-center lg:text-left">{{ $message }}</p>
                            @enderror
                        </form>

                        @if($hasSocialMedia)
                        <div class="flex items-center gap-3 justify-center lg:justify-start ">
                            @if($contactData->insta)
                            <a href="{{ $contactData->insta }}" target="_blank" class="hover:opacity-70 transition-opacity" aria-label="Instagram">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                                </svg>
                            </a>
                            @endif
                            @if($contactData->facebook)
                            <a href="{{ $contactData->facebook }}" target="_blank" class="hover:opacity-70 transition-opacity" aria-label="Facebook">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                </svg>
                            </a>
                            @endif
                            @if($contactData->linkedin)
                            <a href="{{ $contactData->linkedin }}" target="_blank" class="hover:opacity-70 transition-opacity" aria-label="LinkedIn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                                    <rect x="2" y="9" width="4" height="12"/>
                                    <circle cx="4" cy="4" r="2"/>
                                </svg>
                            </a>
                            @endif
                            @if($contactData->youtube)
                            <a href="{{ $contactData->youtube }}" target="_blank" class="hover:opacity-70 transition-opacity" aria-label="YouTube">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.54C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/>
                                    <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                        @endif
                </div>

                <div class="text-center lg:text-left lg:ml-[31px]  w-full lg:w-auto mt-8 lg:mt-0 max-[650px]:mt-[0px]">
                    <h3 class="text-white font-inter text-[18px] sm:text-[20px] font-semibold leading-normal mb-4 lg:mb-[19px]">
                        Contacto
                    </h3>
                
                    <div class="text-white font-montserrat text-[14px] flex flex-col gap-4 lg:gap-[17px] items-center lg:items-start mx-auto">

                        @if($contactData?->direction_adm)
                        <div class="flex items-start text-[#AD0369] max-w-[348px] w-full justify-center lg:justify-start">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                                <path d="M16.6663 8.33329C16.6663 13.3333 9.99967 18.3333 9.99967 18.3333C9.99967 18.3333 3.33301 13.3333 3.33301 8.33329C3.33301 6.56518 4.03539 4.86949 5.28563 3.61925C6.53587 2.36901 8.23156 1.66663 9.99967 1.66663C11.7678 1.66663 13.4635 2.36901 14.7137 3.61925C15.964 4.86949 16.6663 6.56518 16.6663 8.33329Z" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 10.8334C11.3807 10.8334 12.5 9.71409 12.5 8.33337C12.5 6.95266 11.3807 5.83337 10 5.83337C8.61929 5.83337 7.5 6.95266 7.5 8.33337C7.5 9.71409 8.61929 10.8334 10 10.8334Z" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contactData->direction_adm) }}" target="_blank" class="text-white font-montserrat text-[14px] sm:text-[16px] font-normal leading-[150%] ml-3 break-words">
                                {{ $contactData->direction_adm }}
                            </a>
                        </div>
                        @endif
                        
                        @if($contactData?->phone_amd)
                        <div class="flex items-center max-w-[318px] w-full justify-center lg:justify-start">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                                <path d="M3.33301 3.33334H6.66634L8.33301 7.50001L6.24967 8.75001C7.12399 10.5217 8.47797 11.8757 10.2497 12.75L11.4997 10.6667L15.6663 12.3333V15.6667C15.6663 16.1087 15.4907 16.5326 15.1782 16.8452C14.8656 17.1577 14.4417 17.3333 13.9997 17.3333C10.7434 17.1217 7.67399 15.6992 5.40263 13.4278C3.13126 11.1565 1.70878 8.08707 1.49967 4.83334C1.49967 4.39131 1.67527 3.96739 1.98783 3.65483C2.30039 3.34227 2.72431 3.16667 3.16634 3.16667L3.33301 3.33334Z" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <a href="tel:{{ $contactData->phone_amd }}" class="text-white font-montserrat text-[14px] sm:text-[16px] font-normal leading-[150%] ml-3">
                                {{ $contactData->phone_amd }}
                            </a>
                        </div>
                        @endif

                        @if($contactData?->phone_sale)
                        <div class="flex items-center max-w-[318px] w-full justify-center lg:justify-start">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                                <path d="M3.33301 3.33334H6.66634L8.33301 7.50001L6.24967 8.75001C7.12399 10.5217 8.47797 11.8757 10.2497 12.75L11.4997 10.6667L15.6663 12.3333V15.6667C15.6663 16.1087 15.4907 16.5326 15.1782 16.8452C14.8656 17.1577 14.4417 17.3333 13.9997 17.3333C10.7434 17.1217 7.67399 15.6992 5.40263 13.4278C3.13126 11.1565 1.70878 8.08707 1.49967 4.83334C1.49967 4.39131 1.67527 3.96739 1.98783 3.65483C2.30039 3.34227 2.72431 3.16667 3.16634 3.16667L3.33301 3.33334Z" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <a href="tel:{{ $contactData->phone_sale }}" class="text-white font-montserrat text-[14px] sm:text-[16px] font-normal leading-[150%] ml-3">
                                {{ $contactData->phone_sale }}
                            </a>
                        </div>
                        @endif

                        @if($contactData?->maps_adm)
                        <div class="flex items-center max-w-[318px] w-full justify-center lg:justify-start">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                                <path d="M3.33301 3.33334H6.66634L8.33301 7.50001L6.24967 8.75001C7.12399 10.5217 8.47797 11.8757 10.2497 12.75L11.4997 10.6667L15.6663 12.3333V15.6667C15.6663 16.1087 15.4907 16.5326 15.1782 16.8452C14.8656 17.1577 14.4417 17.3333 13.9997 17.3333C10.7434 17.1217 7.67399 15.6992 5.40263 13.4278C3.13126 11.1565 1.70878 8.08707 1.49967 4.83334C1.49967 4.39131 1.67527 3.96739 1.98783 3.65483C2.30039 3.34227 2.72431 3.16667 3.16634 3.16667L3.33301 3.33334Z" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <a href="tel:{{ $contactData->maps_adm }}" class="text-white font-montserrat text-[14px] sm:text-[16px] font-normal leading-[150%] ml-3">
                                {{ $contactData->maps_adm }}
                            </a>
                        </div>
                        @endif

                        @if($contactData?->maps_sale)
                        <div class="flex items-center max-w-[318px] w-full justify-center lg:justify-start">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
                                <path d="M3.33301 3.33334H6.66634L8.33301 7.50001L6.24967 8.75001C7.12399 10.5217 8.47797 11.8757 10.2497 12.75L11.4997 10.6667L15.6663 12.3333V15.6667C15.6663 16.1087 15.4907 16.5326 15.1782 16.8452C14.8656 17.1577 14.4417 17.3333 13.9997 17.3333C10.7434 17.1217 7.67399 15.6992 5.40263 13.4278C3.13126 11.1565 1.70878 8.08707 1.49967 4.83334C1.49967 4.39131 1.67527 3.96739 1.98783 3.65483C2.30039 3.34227 2.72431 3.16667 3.16634 3.16667L3.33301 3.33334Z" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <a href="tel:{{ $contactData->maps_sale }}" class="text-white font-montserrat text-[14px] sm:text-[16px] font-normal leading-[150%] ml-3">
                                {{ $contactData->maps_sale }}
                            </a>
                        </div>
                        @endif
                        
                        @if($contactData?->mail_adm)
                        <div class="flex items-center max-w-[318px] w-full justify-center lg:justify-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" class="flex-shrink-0">
                                <path d="M16.667 3.33334H3.33366C2.41318 3.33334 1.66699 4.07954 1.66699 5.00001V15C1.66699 15.9205 2.41318 16.6667 3.33366 16.6667H16.667C17.5875 16.6667 18.3337 15.9205 18.3337 15V5.00001C18.3337 4.07954 17.5875 3.33334 16.667 3.33334Z" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.3337 5.83334L10.8587 10.5833C10.6014 10.7445 10.3039 10.83 10.0003 10.83C9.69673 10.83 9.39926 10.7445 9.14199 10.5833L1.66699 5.83334" stroke="#AD0369" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <a href="mailto:{{ $contactData->mail_adm }}" class="text-white font-montserrat text-[14px] sm:text-[16px] font-normal leading-[150%] ml-3 break-all">
                                {{ $contactData->mail_adm }}
                            </a>
                        </div>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6 lg:py-0 lg:h-[64px] bg-black/5 flex items-center border-t border-white/10 lg:border-0"
         x-data="{ animate: false }"
         x-init="setTimeout(() => animate = true, 300)"
         x-show="animate"
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-0">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-3 lg:gap-0 text-center lg:text-left lg:max-w-[1224px] lg:mx-auto w-full">
                <div class="text-white font-montserrat text-[12px] sm:text-[14px] opacity-80 z-5 relative">
                    © Copyright 2026 <strong>Ralux</strong>. Todos los derechos reservados
                </div>
                <div class="text-white font-karla text-[11px] sm:text-[12px] lg:text-sm opacity-80 z-5 relative">
                    <a href="https://osole.com.ar" target="_blank" rel="noopener noreferrer">
                        By <strong>Osole</strong>
                    </a>                            
                </div>
            </div>
        </div>
    </div>

    

    <style>
        .animate-toast-progress {
            animation: toastProgress 4.2s linear forwards;
            transform-origin: left center;
        }

        @keyframes toastProgress {
            from { transform: scaleX(1); }
            to { transform: scaleX(0); }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</footer>
