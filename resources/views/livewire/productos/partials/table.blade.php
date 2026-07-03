@forelse($productos as $producto)
    <tr class="hover:bg-slate-50 transition">
        <td class="px-4 py-4 text-center font-semibold text-slate-900">{{ $producto->orden }}</td>

        <td class="px-4 py-4">
            <div class="w-14 h-14 rounded-md overflow-hidden flex items-center justify-center flex-shrink-0">
                @if($producto->imagenPrincipal)
                    <img src="{{ Storage::url($producto->imagenPrincipal->ruta) }}"
                         alt="{{ $producto->imagenPrincipal->alt }}"
                         class="w-full h-full object-contain">
                @else
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                @endif
            </div>
        </td>

        <td class="px-4 py-4">
            <p class="font-mono text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded inline-block">{{ $producto->codigo_ralux }}</p>
            <p class="font-medium text-slate-900 mt-1">{!! $producto->descripcion_es !!}</p>
            <p class="text-xs text-slate-500">{{ $producto->descripcion_en }}</p>
        </td>

        <td class="px-4 py-4 text-end whitespace-nowrap">
            @if(!is_null($producto->precio))
                <p class="font-semibold text-slate-900">${{ number_format($producto->precio, 2, ',', '.') }}</p>
                @if((float) $producto->descuento > 0)
                    <p class="text-xs text-emerald-600">-{{ number_format($producto->descuento, 2, ',', '.') }}%</p>
                @endif
            @else
                <span class="text-xs text-slate-400">Sin precio</span>
            @endif
        </td>

        <td class="px-4 py-4">
            <span class="px-2 py-0.5 text-xs rounded-full bg-slate-100 text-slate-700">
                {{ $producto->tipo?->descripcion_es ?? '—' }}
            </span>
        </td>


        <td class="px-4 py-4 text-center">
            @if($producto->destacado)
                <span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700">Si</span>
            @else
                <span class="px-2 py-0.5 text-xs rounded-full bg-slate-100 text-slate-500">No</span>
            @endif
        </td>

        <td class="px-4 py-4 text-center">
            @if($producto->visible)
                <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">Visible</span>
            @else
                <span class="px-2 py-0.5 text-xs rounded-full bg-slate-100 text-slate-500">Oculto</span>
            @endif
        </td>

        <td class="text-center">
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('productos.edit', $producto->id) }}"
                   class="text-slate-500 hover:text-blue-600 transition cursor-pointer">
                    <svg class="w-[24px] h-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </a>
                <button class="delete-btn text-red-500 hover:text-red-600 transition cursor-pointer"
                        data-id="{{ $producto->id }}">
                    <svg class="w-[28px] h-[28px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-10 text-center text-slate-500 text-sm">
            No hay productos disponibles
        </td>
    </tr>
@endforelse
