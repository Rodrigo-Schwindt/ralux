<div x-data="{ open: @entangle('open'), isLogin: true }"
     @open-login-modal.window="open = true; isLogin = true"
     class="relative z-[120] inline-block">
    <a @click.prevent="open = true; isLogin = true"
       class="hidden lg:inline-flex items-center justify-center h-[44px] px-[26px] py-[11px]
              border border-black rounded-[22px]
              text-black text-[16px] font-normal leading-[150%] text-center
              cursor-pointer transition-colors hover:bg-black hover:text-white">
        ZONA PRIVADA
    </a>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.98]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-1 scale-[0.98]"
         @click.outside="open = false"
         @click.self="open = false"
         @keydown.escape.window="open = false"
         class="login-modal-panel absolute top-full right-0 mt-3 z-[101]"
         :style="'width: ' + (isLogin ? '367px' : '420px') + '; max-width: calc(100vw - 2rem);'"
         x-cloak>
        <div class="client-auth-card w-full shadow-2xl max-h-[85vh] overflow-y-auto">
            <h2 class="text-[24px] max-[480px]:text-[30px] text-[#222] font-inter font-semibold leading-[1.1] mb-8"
                x-text="isLogin ? 'Iniciar sesión' : 'Crear cuenta'"></h2>

            <form x-show="isLogin" wire:submit.prevent="login" class="space-y-5">
                <div>
                    <label class="client-auth-label">Mail</label>
                    <input type="text" wire:model.defer="username" class="client-auth-input" placeholder="marianor">
                    @error('username') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="client-auth-label">Contraseña</label>
                    <input type="password" wire:model.defer="password" class="client-auth-input" placeholder="*****">
                    @error('password') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="client-auth-submit ">
                    INGRESAR
                </button>

                <div class="pt-7 mt-2 border-t border-[#CECECE] text-center">
                    <p class="text-[#2e2e2e] text-[16px] max-[480px]:text-[16px]">¿No tenés usuario?</p>
                    <button type="button" @click="isLogin = false" class="mt-1 text-[#222] text-[18px] max-[480px]:text-[18px] leading-none font-bold underline cursor-pointer">
                        Registrate
                    </button>
                </div>
            </form>

            <form x-show="!isLogin" wire:submit.prevent="register" class="space-y-4" x-cloak>
                <div>
                    <label class="client-auth-label">Nombre completo *</label>
                    <input type="text" wire:model.defer="nombre" class="client-auth-input">
                    @error('nombre') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="client-auth-label">Nombre de usuario *</label>
                    <input type="text" wire:model.defer="usuario" class="client-auth-input" placeholder="marianor">
                    @error('usuario') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror
                    <p class="text-[12px] text-gray-400 mt-1">Con este nombre podrás iniciar sesión</p>
                </div>

                <div>
                    <label class="client-auth-label">Correo electrónico *</label>
                    <input type="email" wire:model.defer="email" class="client-auth-input">
                    @error('email') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="client-auth-label">CUIL</label>
                    <input type="text" wire:model.defer="cuil" class="client-auth-input">
                    @error('cuil') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="client-auth-label">CUIT</label>
                    <input type="text" wire:model.defer="cuit" class="client-auth-input">
                    @error('cuit') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="client-auth-label">Teléfono</label>
                    <input type="text" wire:model.defer="telefono" class="client-auth-input">
                    @error('telefono') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="client-auth-label">Domicilio</label>
                    <input type="text" wire:model.defer="domicilio" class="client-auth-input">
                    @error('domicilio') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="client-auth-label">Localidad</label>
                    <input type="text" wire:model.defer="localidad" class="client-auth-input">
                    @error('localidad') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="client-auth-label">Provincia</label>
                    <input type="text" wire:model.defer="provincia" class="client-auth-input">
                    @error('provincia') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="client-auth-label">Contraseña *</label>
                    <input type="password" wire:model.defer="reg_password" class="client-auth-input">
                </div>
                <div>
                    <label class="client-auth-label">Confirmar *</label>
                    <input type="password" wire:model.defer="reg_password_confirmation" class="client-auth-input">
                </div>
                @error('reg_password') <span class="text-red-500 text-[12px] mt-1 block">{{ $message }}</span> @enderror

                <button type="submit" class="client-auth-submit">
                    REGISTRARME
                </button>

                <div class="pt-4 border-t border-[#CECECE] text-center">
                    <button type="button" @click="isLogin = true" class="text-[#222] text-[16px] font-semibold underline cursor-pointer">
                        Volver al inicio de sesión
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .client-auth-card {
            background: #fff;
            border-radius: 20px;
            padding: 22px 22px 24px;
            border: 1px solid #DEDFE0;
            
        }

        .client-auth-label {
            display: block;
            color: #222;
            font-size: 16px;
            line-height: 1.3;
            margin-bottom: 10px;
        }

        .client-auth-input {
            width: 100%;
            height: 45px;
            border-radius: 9999px;
            border: 1px solid #DEDFE0;
            background: #efefef;
            padding: 0 16px;
            color: #2b2b2b;
            font-size: 15px;
            outline: none;
            transition: all 0.2s ease;
        }

        .client-auth-input::placeholder { color: #a6a6a6; }
        .client-auth-input:focus {
            border-color: #ad0369;
            box-shadow: 0 0 0 2px rgba(173, 3, 105, 0.12);
            background: #f6f6f6;
        }

        .client-auth-submit {
           display: flex;
width: 100%;
height: 41px;
padding: 11px 26px;
justify-content: center;
align-items: center;
border-radius: 22px;
background: var(--Fucsia, #AD0369);
color: #FFF;
text-align: center;
font-size: 16px;
font-style: normal;
font-weight: 400;
line-height: 150%; 

        }

        .client-auth-submit:hover {
            filter: brightness(1.03);
            transform: translateY(-1px);
        }

        @media (max-width: 480px) {
            .client-auth-submit { font-size: 20px; }
        }

        @media (max-width: 1023px) {
            .login-modal-panel {
                position: fixed !important;
                inset: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                max-width: none !important;
                margin: 0 !important;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(0, 0, 0, 0.55);
                z-index: 200 !important;
            }
            .login-modal-panel .client-auth-card {
                width: 92vw;
                max-width: 400px;
            }
            .login-modal-panel .client-auth-input {
                width: 100%;
            }
            .login-modal-panel .client-auth-submit {
                width: 100%;
            }
        }

        [x-cloak] { display: none !important; }
    </style>
</div>
