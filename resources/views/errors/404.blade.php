@component('layouts.public', [
    'pageTitleOverride' => 'Pagina no encontrada | Ralux',
    'metaDescriptionOverride' => 'La pagina solicitada no existe. Encontra productos Ralux, catalogos o contactanos para recibir ayuda.',
    'metaKeywordsOverride' => 'Ralux, autopartes, productos Ralux, catalogos Ralux, contacto Ralux',
    'robots' => 'noindex, follow',
])
    <section class="bg-[#222] py-[24px]">
        <div class="max-w-[1224px] mx-auto px-4 lg:px-0">
            <div class="flex items-center gap-2 text-white/70 text-[13px] font-medium">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Inicio</a>
                <span>/</span>
                <span class="text-white">404</span>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="max-w-[1224px] mx-auto px-4 lg:px-0 py-16 lg:py-24">
            <div class="grid lg:grid-cols-[1fr_420px] gap-10 lg:gap-16 items-center">
                <div>
                    <span class="inline-flex items-center gap-2 text-[#ad0369] text-[13px] font-bold uppercase tracking-[0.08em] mb-5">
                        <span class="w-8 h-[2px] bg-[#ad0369]"></span>
                        Error 404
                    </span>

                    <h1 class="text-[#111] text-[38px] sm:text-[48px] lg:text-[64px] font-bold leading-[1.02] max-w-[760px]">
                        No encontramos esta pagina.
                    </h1>

                    <p class="text-slate-600 text-[17px] sm:text-[18px] leading-8 mt-6 max-w-[680px]">
                        El enlace puede haber cambiado o el producto ya no estar disponible. Podes volver al catalogo, buscar por codigo o escribirnos para que te ayudemos.
                    </p>

                    <form action="{{ route('productos') }}" method="GET" class="mt-8 max-w-[620px]">
                        <label for="busqueda-404" class="sr-only">Buscar productos</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-1">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.1-5.4a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                                </svg>
                                <input
                                    id="busqueda-404"
                                    type="search"
                                    name="busqueda"
                                    placeholder="Codigo Ralux, descripcion o equivalencia"
                                    class="w-full h-[52px] rounded-md border border-slate-300 bg-white pl-12 pr-4 text-[15px] text-slate-800 outline-none focus:border-[#ad0369] focus:ring-2 focus:ring-[#ad0369]/15 transition">
                            </div>

                            <button type="submit"
                                    class="h-[52px] px-6 rounded-md bg-[#ad0369] text-white text-[15px] font-semibold hover:bg-[#92035a] transition-colors">
                                Buscar
                            </button>
                        </div>
                    </form>

                    <div class="flex flex-wrap gap-3 mt-7">
                        <a href="{{ route('productos') }}"
                           class="inline-flex items-center gap-2 h-[46px] px-5 rounded-md bg-[#222] text-white text-[14px] font-semibold hover:bg-black transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Ver productos
                        </a>

                        <a href="{{ url('/catalogos') }}"
                           class="inline-flex items-center gap-2 h-[46px] px-5 rounded-md border border-slate-300 text-[#222] text-[14px] font-semibold hover:border-[#ad0369] hover:text-[#ad0369] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                            </svg>
                            Catalogos
                        </a>

                        <a href="{{ url('/contacto') }}"
                           class="inline-flex items-center gap-2 h-[46px] px-5 rounded-md border border-slate-300 text-[#222] text-[14px] font-semibold hover:border-[#ad0369] hover:text-[#ad0369] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Contacto
                        </a>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-md p-7 lg:p-8">
                    <div class="w-16 h-16 rounded-md bg-[#ad0369] flex items-center justify-center mb-7">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 9.75h.008v.008H9.75V9.75zm4.5 0h.008v.008h-.008V9.75zM9 15h6m-3-13a10 10 0 100 20 10 10 0 000-20z"/>
                        </svg>
                    </div>

                    <p class="text-[72px] lg:text-[92px] leading-none font-bold text-[#222]">404</p>
                    <p class="text-slate-900 text-[20px] font-semibold mt-5">Pagina no disponible</p>
                    <p class="text-slate-600 text-[15px] leading-7 mt-3">
                        Mantuvimos este enlace con navegacion util para que puedas continuar y encontrar rapidamente la informacion que buscabas.
                    </p>

                    <div class="mt-8 pt-6 border-t border-slate-200 grid gap-4">
                        <div class="flex items-start gap-3">
                            <span class="mt-1 w-2 h-2 rounded-full bg-[#ad0369] flex-shrink-0"></span>
                            <p class="text-slate-700 text-[14px] leading-6">Busca por codigo Ralux, descripcion o equivalencia.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-1 w-2 h-2 rounded-full bg-[#ad0369] flex-shrink-0"></span>
                            <p class="text-slate-700 text-[14px] leading-6">Explora catalogos y productos desde accesos directos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endcomponent
