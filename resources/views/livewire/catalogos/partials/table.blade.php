{{-- resources/views/admin/catalogos/partials/table.blade.php --}}
@forelse($catalogos as $catalogo)
    <tr class="hover:bg-slate-50 transition">
        <td class="px-4 py-4 text-center font-semibold text-slate-900">{{ $catalogo->orden }}</td>

        <td class="px-4 py-4">
            <p class="font-medium text-slate-900">{{ $catalogo->title }}</p>
        </td>

        <td class="px-4 py-4 text-center">
            @if($catalogo->image_1)
                <div class="w-16 h-16 mx-auto rounded-md overflow-hidden bg-slate-200">
                    <img src="{{ Storage::url($catalogo->image_1) }}" class="w-full h-full object-cover" alt="Imagen 1">
                </div>
            @else
                <span class="text-slate-400 text-xs">Sin imagen</span>
            @endif
        </td>

        <td class="px-4 py-4 text-center">
            @if($catalogo->image_2)
                <div class="w-16 h-16 mx-auto rounded-md overflow-hidden bg-slate-200">
                    <img src="{{ Storage::url($catalogo->image_2) }}" class="w-full h-full object-cover" alt="Imagen 2">
                </div>
            @else
                <span class="text-slate-400 text-xs">Sin imagen</span>
            @endif
        </td>

        <td class="px-4 py-4 text-center">
            @if($catalogo->pdf)
                <a href="{{ Storage::url($catalogo->pdf) }}" target="_blank" class="text-blue-600 hover:text-blue-700">
                    <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </a>
            @else
                <span class="text-slate-400 text-xs">Sin PDF</span>
            @endif
        </td>

        <td class="px-4 py-4 text-center">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $catalogo->visible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $catalogo->visible ? 'Visible' : 'Oculto' }}
            </span>
        </td>

        <td class="text-center">
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('catalogos.edit', $catalogo->id) }}"
                   class="text-slate-500 hover:text-blue-600 transition cursor-pointer">
                    <svg class="w-[24px] h-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </a>

                <button class="delete-btn text-red-500 hover:text-red-600 transition cursor-pointer"
                        data-id="{{ $catalogo->id }}">
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
        <td colspan="7" class="px-6 py-10 text-center text-slate-500 text-sm">
            No hay catálogos disponibles
        </td>
    </tr>
@endforelse