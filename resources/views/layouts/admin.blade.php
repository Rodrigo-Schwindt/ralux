@php
    use App\Models\Contact;
    $contact = Contact::first();
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <title>{{ $title ?? 'Panel Administrativo' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @livewireScriptConfig

    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>window.CKEDITOR_BASEPATH = 'https://cdn.ckeditor.com/4.22.1/standard/';</script>
    <script>document.addEventListener('DOMContentLoaded', function() { if (window.CKEDITOR) CKEDITOR.config.versionCheck = false; });</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --admin-bg: #ffffff;
            --admin-primary: #AD0369;
            --admin-secondary: #EF338C;
            --admin-accent: #FD359D;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn { animation: fadeIn 0.4s ease-out; }

        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f5f5f5; }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: var(--admin-secondary);
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: var(--admin-primary); }

        .nav-item {
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .active-link {
            background: linear-gradient(to right, var(--admin-primary), var(--admin-accent));
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(173, 3, 105, 0.25);
        }

        .submenu-item {
            padding-left: 48px;
            font-size: 0.85rem;
            color: #62748b;
        }

        .submenu-item:hover { color: var(--admin-secondary); }
        .submenu-active { color: var(--admin-primary); font-weight: 700; }
        .divider { border-top: 1px solid #f3d6e6; margin: 12px 0; }
        .admin-icon { color: var(--admin-secondary); }
    </style>
</head>
<body class="bg-white" x-data="{ openSidebar: true }">
    <aside
        x-show="openSidebar"
        class="w-72 bg-white border-r border-pink-100 shadow-xl flex flex-col fixed inset-y-0 left-0 z-40"
    >
        <div class="flex items-center justify-center py-4 px-6 border-b border-pink-50">
            @if($contact && $contact->icono_2)
                <a href="{{ url('/') }}" class="flex items-center justify-center">
                    <img src="{{ Storage::url($contact->icono_1) }}" class="h-[80px] w-auto object-contain" alt="Logo">
                </a>
            @else
                <div class="h-12 w-32 rounded-lg flex items-center justify-center text-white font-bold"
                     style="background: linear-gradient(to right, var(--admin-primary), var(--admin-accent));">
                    ADMIN
                </div>
            @endif
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto custom-scroll">
            <div x-data="{ open: {{ request()->routeIs('sliders.*', 'nosotros.home.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="nav-item w-full cursor-pointer text-gray-700 hover:bg-pink-50 justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 admin-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Inicio</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('sliders.index') }}" class="nav-item submenu-item {{ request()->routeIs('sliders.*') ? 'submenu-active' : '' }}">Sliders</a>
                    <a href="{{ route('nosotros.home.index') }}" class="nav-item submenu-item {{ request()->routeIs('nosotros.home.*') ? 'submenu-active' : '' }}">Nosotros Home</a>
                </div>
            </div>

            <a href="{{ route('nosotros.index') }}" class="nav-item {{ request()->routeIs('nosotros.index') ? 'active-link' : 'text-gray-700 hover:bg-pink-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('nosotros.index') ? '' : 'admin-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Nosotros
            </a>

            <div x-data="{ open: {{ request()->routeIs('vehiculo-tipo.*', 'marcas.*', 'modelos.*', 'producto-tipo.*', 'productos.*', 'equivalencias.*', 'codigo-om.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="nav-item w-full cursor-pointer text-gray-700 hover:bg-pink-50 justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 admin-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Productos</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('vehiculo-tipo.index') }}" class="nav-item submenu-item {{ request()->routeIs('vehiculo-tipo.*') ? 'submenu-active' : '' }}">Tipo de Vehiculo</a>
                    <a href="{{ route('marcas.index') }}" class="nav-item submenu-item {{ request()->routeIs('marcas.*') ? 'submenu-active' : '' }}">Marcas</a>
                    <a href="{{ route('modelos.index') }}" class="nav-item submenu-item {{ request()->routeIs('modelos.*') ? 'submenu-active' : '' }}">Modelos</a>
                    <a href="{{ route('producto-tipo.index') }}" class="nav-item submenu-item {{ request()->routeIs('producto-tipo.*') ? 'submenu-active' : '' }}">Tipo de Producto</a>
                    <a href="{{ route('productos.index') }}" class="nav-item submenu-item {{ request()->routeIs('productos.*') ? 'submenu-active' : '' }}">Productos</a>
                    <a href="{{ route('equivalencias.index') }}" class="nav-item submenu-item {{ request()->routeIs('equivalencias.*') ? 'submenu-active' : '' }}">Equivalencias</a>
                    <a href="{{ route('codigo-om.index') }}" class="nav-item submenu-item {{ request()->routeIs('codigo-om.*') ? 'submenu-active' : '' }}">Códigos OM</a>
                </div>
            </div>

            <a href="{{ route('catalogos.index') }}" class="nav-item {{ request()->routeIs('catalogos.*') ? 'active-link' : 'text-gray-700 hover:bg-pink-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('catalogos.*') ? '' : 'admin-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.483 9.246 5 7.5 5 4.462 5 2 6.79 2 9v11c0-2.21 2.462-4 5.5-4 1.746 0 3.332.483 4.5 1.253m0-13C13.168 5.483 14.754 5 16.5 5c3.038 0 5.5 1.79 5.5 4v11c0-2.21-2.462-4-5.5-4-1.746 0-3.332.483-4.5 1.253"/></svg>
                Catalogo
            </a>

            <a href="{{ route('servicios.admin') }}" class="nav-item {{ request()->routeIs('servicios.*') ? 'active-link' : 'text-gray-700 hover:bg-pink-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('servicios.*') ? '' : 'admin-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Calidad
            </a>

            <div x-data="{ open: {{ request()->routeIs('novedades.*', 'novcategories.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="nav-item w-full cursor-pointer text-gray-700 hover:bg-pink-50 justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 admin-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <span>Novedades</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    <a href="{{ route('novcategories.index') }}" class="nav-item submenu-item {{ request()->routeIs('novcategories.*') ? 'submenu-active' : '' }}">Cat. Novedades</a>
                    <a href="{{ route('novedades.index') }}" class="nav-item submenu-item {{ request()->routeIs('novedades.*') ? 'submenu-active' : '' }}">Ver Novedades</a>
                </div>
            </div>
                        <a href="{{ route('admin.contacto') }}" class="nav-item {{ request()->routeIs('admin.contacto') ? 'active-link' : 'text-gray-700 hover:bg-pink-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.contacto') ? '' : 'admin-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Contacto
            </a>

            <div class="divider"></div>

            <p class="px-4 pt-1 pb-2 text-[10px] font-bold uppercase tracking-widest text-pink-400"></p>

            <a href="{{ route('clientes.index') }}" class="nav-item {{ request()->routeIs('clientes.*') ? 'active-link' : 'text-gray-700 hover:bg-pink-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('clientes.*') ? '' : 'admin-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Clientes
            </a>
            <a href="{{ route('admin.carrito-config') }}" class="nav-item {{ request()->routeIs('admin.carrito-config*') ? 'active-link' : 'text-gray-700 hover:bg-pink-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.carrito-config*') ? '' : 'admin-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Config. Carrito
            </a>
            <a href="{{ route('admin.pedidos') }}" class="nav-item {{ request()->routeIs('admin.pedidos*') ? 'active-link' : 'text-gray-700 hover:bg-pink-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.pedidos*') ? '' : 'admin-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Pedidos
            </a>


            <a href="{{ route('precios.index') }}" class="nav-item {{ request()->routeIs('precios.*') ? 'active-link' : 'text-gray-700 hover:bg-pink-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('precios.*') ? '' : 'admin-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Listas de Precios
            </a>


            <div class="divider"></div>



            <a href="{{ route('admin.newsletter') }}" class="nav-item {{ request()->routeIs('admin.newsletter*') ? 'active-link' : 'text-gray-700 hover:bg-pink-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.newsletter*') ? '' : 'admin-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/></svg>
                Newsletter
            </a>

            <a href="{{ route('usuarios.index') }}" class="nav-item {{ request()->routeIs('usuarios.*') ? 'active-link' : 'text-gray-700 hover:bg-pink-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('usuarios.*') ? '' : 'admin-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Usuarios
            </a>

            <a href="{{ route('admin.metadata') }}" class="nav-item {{ request()->routeIs('admin.metadata') ? 'active-link' : 'text-gray-700 hover:bg-pink-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.metadata') ? '' : 'admin-icon' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Metadata
            </a>

            
        </nav>

        <div class="p-4 border-t border-pink-100 bg-pink-50/40">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full flex items-center cursor-pointer justify-center gap-3 px-4 py-3 text-sm font-bold rounded-xl text-white transition-all shadow-md"
                    style="background: linear-gradient(to right, var(--admin-primary), var(--admin-accent));"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Cerrar Sesion
                </button>
            </form>
        </div>
    </aside>

    <main class="ml-72 min-h-screen bg-white">
        <div class="p-8 animate-fadeIn">
            @yield('content')
            {{ $slot ?? '' }}
        </div>
    </main>

    @livewireScripts
</body>
</html>
