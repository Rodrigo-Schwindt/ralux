@extends('layouts.admin')

@section('content')

@php
use Illuminate\Support\Str;

function metadataName($section, $products, $novedades){
    if (str_starts_with($section,'producto-')) {
        $id = (int) str_replace('producto-','',$section);
        $p = $products->firstWhere('id',$id);
        return $p ? "Producto: {$p->codigo_ralux}" : "Producto #$id";
    }
    if (str_starts_with($section,'novedad-')) {
        $id = (int) str_replace('novedad-','',$section);
        $n = $novedades->firstWhere('id',$id);
        return $n ? "Novedad: {$n->title}" : "Novedad #$id";
    }

    $map = [
        'home'      => 'Inicio',
        'nosotros'  => 'Nosotros',
        'productos' => 'Productos',
        'novedades' => 'Novedades',
        'contacto'  => 'Contacto',
    ];

    return $map[$section] ?? $section;
}

$currentFormMode = old('formMode','create');
$metadataId = old('metadataId');
$initialType = old('metadataType','section');
$descVal = old('description','');
@endphp

<script>
const PRODUCTS_DATA = @json($products->map(fn($p) => ['id' => $p->id, 'label' => $p->codigo_ralux]));
const NOVEDADES_DATA = @json($novedades->map(fn($n) => ['id' => $n->id, 'label' => $n->title]));
</script>

<div class="animate-fadeIn space-y-6">

    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-md px-4 py-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-md px-4 py-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Revisá los campos marcados e intentá nuevamente.
        </div>
    @endif

    <div id="list" class="bg-white border border-slate-200 rounded-md shadow-sm">

        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Metadatos SEO</h1>
                <p class="text-sm text-slate-500 mt-0.5">Gestioná las etiquetas meta de cada página.</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('admin.metadata.generate-products') }}"
                      onsubmit="return confirm('Se van a crear las metadata faltantes de todos los productos. Las existentes no se modifican. ¿Continuar?')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-900 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0119 5m0 0h-5m5 0v5"/>
                        </svg>
                        Generar productos
                    </button>
                </form>
                <form method="GET" action="{{ route('admin.metadata') }}" class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar..."
                           class="pl-9 pr-3 py-2 w-56 text-sm border border-slate-300 rounded-md bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-slate-700 transition">
                </form>
                <button onclick="openCreate()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nuevo
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">Sección / Elemento</th>
                        <th class="px-5 py-3 text-left font-medium">Keywords</th>
                        <th class="px-5 py-3 text-left font-medium">Descripción</th>
                        <th class="px-5 py-3 text-right font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody id="metadataTableBody" class="divide-y divide-slate-100">
                    @forelse($items as $item)
                    <tr id="metadata-row-{{ $item->id }}" class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3">
                            <span class="font-medium text-slate-900">
                                {{ metadataName($item->section, $products, $novedades) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-500 max-w-[200px] truncate">
                            {{ Str::limit($item->keywords, 55) }}
                        </td>
                        <td class="px-5 py-3 text-slate-500 max-w-[260px] truncate">
                            {{ Str::limit($item->description, 65) }}
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <button onclick="editMeta(this)"
                                        data-id="{{ $item->id }}"
                                        data-section="{{ $item->section }}"
                                        data-keywords="{{ $item->keywords }}"
                                        data-description="{{ $item->description }}"
                                        class="text-slate-500 hover:text-blue-600 transition cursor-pointer">
                                    <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                                <button onclick="deleteMeta({{ $item->id }})"
                                        class="text-red-500 hover:text-red-600 transition cursor-pointer">
                                    <svg class="w-[26px] h-[26px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-slate-400 text-sm">
                            No hay metadatos registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-100 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <p class="text-xs text-slate-500">
                Mostrando
                <span id="metadataFirstItem">{{ $items->firstItem() ?? 0 }}</span>-<span id="metadataLastItem">{{ $items->lastItem() ?? 0 }}</span>
                de <span id="metadataTotal">{{ $items->total() }}</span>
                metadatos
            </p>

            @if($items->hasPages())
                <nav class="flex flex-wrap items-center gap-1" aria-label="Paginacion de metadatos">
                    <a href="{{ $items->url(1) }}"
                       class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-slate-200 px-3 text-xs font-medium transition {{ $items->onFirstPage() ? 'pointer-events-none text-slate-300 bg-slate-50' : 'text-slate-600 hover:border-blue-600 hover:text-blue-600' }}">
                        Primera
                    </a>

                    <a href="{{ $items->previousPageUrl() ?: '#' }}"
                       class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-slate-200 px-3 text-xs font-medium transition {{ $items->onFirstPage() ? 'pointer-events-none text-slate-300 bg-slate-50' : 'text-slate-600 hover:border-blue-600 hover:text-blue-600' }}">
                        Anterior
                    </a>

                    @foreach($items->getUrlRange(max(1, $items->currentPage() - 2), min($items->lastPage(), $items->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}"
                           aria-current="{{ $page === $items->currentPage() ? 'page' : 'false' }}"
                           class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-xs font-semibold transition {{ $page === $items->currentPage() ? 'border-blue-600 bg-blue-600 text-white shadow-sm' : 'border-slate-200 text-slate-600 hover:border-blue-600 hover:text-blue-600' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    <a href="{{ $items->nextPageUrl() ?: '#' }}"
                       class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-slate-200 px-3 text-xs font-medium transition {{ $items->hasMorePages() ? 'text-slate-600 hover:border-blue-600 hover:text-blue-600' : 'pointer-events-none text-slate-300 bg-slate-50' }}">
                        Siguiente
                    </a>

                    <a href="{{ $items->url($items->lastPage()) }}"
                       class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-slate-200 px-3 text-xs font-medium transition {{ $items->hasMorePages() ? 'text-slate-600 hover:border-blue-600 hover:text-blue-600' : 'pointer-events-none text-slate-300 bg-slate-50' }}">
                        Ultima
                    </a>
                </nav>
            @endif
        </div>
    </div>

    <div id="form" class="hidden bg-white border border-slate-200 rounded-md shadow-sm">

        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900" id="formTitle">Crear metadato</h2>
            <button type="button" onclick="closeForm()"
                    class="text-slate-400 hover:text-slate-600 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.metadata.save') }}" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="metadataId" id="metadataId" value="{{ old('metadataId') }}">
            <input type="hidden" name="formMode" id="formMode" value="{{ $currentFormMode }}">
            <input type="hidden" name="itemId" id="itemId" value="{{ old('itemId') }}">

            <div class="grid md:grid-cols-1 gap-5">

                <div class="space-y-4">

                    <div>
                        <label for="metadataType" class="block text-sm font-medium text-slate-700 mb-1.5">Tipo *</label>
                        <select name="metadataType" id="metadataType"
                                class="w-full text-sm border border-slate-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                            <option value="section" {{ $initialType==='section'?'selected':'' }}>Sección de página</option>
                            <option value="producto" {{ $initialType==='producto'?'selected':'' }}>Producto específico</option>
                            <option value="novedad" {{ $initialType==='novedad'?'selected':'' }}>Novedad específica</option>
                        </select>
                    </div>

                    <div id="sectionGroup">
                        <label for="section" class="block text-sm font-medium text-slate-700 mb-1.5">Sección *</label>
                        <select name="section" id="section"
                                class="w-full text-sm border border-slate-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                            <option value="">Seleccionar sección...</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec }}" {{ old('section') === $sec ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('-', ' ', $sec)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="itemGroup" class="hidden">
                        <label for="comboProductoInput" class="block text-sm font-medium text-slate-700 mb-1.5" id="itemLabel">Elemento *</label>

                        <div id="wrapProducto" class="hidden">
                            <div class="relative" id="comboProductoWrap">
                                <div class="relative">
                                    <input type="text" id="comboProductoInput"
                                           placeholder="Buscar por código..."
                                           autocomplete="off"
                                           class="w-full text-sm border border-slate-300 rounded-md pl-3 pr-8 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                                    <button type="button" id="comboProductoClear"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <div id="comboProductoDropdown"
                                     class="hidden absolute z-20 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-md shadow-lg overflow-y-auto"
                                     style="max-height:220px">
                                </div>
                            </div>
                            <p id="comboProductoEmpty" class="hidden text-xs text-slate-400 mt-1">Sin resultados.</p>
                        </div>

                        <div id="wrapNovedad" class="hidden">
                            <div class="relative" id="comboNovedadWrap">
                                <div class="relative">
                                    <input type="text" id="comboNovedadInput"
                                           placeholder="Buscar novedad..."
                                           autocomplete="off"
                                           class="w-full text-sm border border-slate-300 rounded-md pl-3 pr-8 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                                    <button type="button" id="comboNovedadClear"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <div id="comboNovedadDropdown"
                                     class="hidden absolute z-20 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-md shadow-lg overflow-y-auto"
                                     style="max-height:220px">
                                </div>
                            </div>
                            <p id="comboNovedadEmpty" class="hidden text-xs text-slate-400 mt-1">Sin resultados.</p>
                        </div>
                    </div>

                </div>

                <div class="space-y-4">

                    <div>
                        <label for="keywords" class="block text-sm font-medium text-slate-700 mb-1.5">Keywords *</label>
                        <textarea name="keywords" id="keywords" rows="3"
                                  placeholder="palabra1, palabra2, palabra3..."
                                  class="w-full text-sm border border-slate-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition resize-none">{{ old('keywords') }}</textarea>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="description" class="block text-sm font-medium text-slate-700">Descripción *</label>
                            <span class="text-xs text-slate-400"><span id="dCount">{{ Str::length($descVal) }}</span>/160</span>
                        </div>
                        <textarea name="description" id="description" rows="4"
                                  maxlength="160"
                                  placeholder="Descripción breve para motores de búsqueda..."
                                  class="w-full text-sm border border-slate-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition resize-none">{{ $descVal }}</textarea>
                    </div>

                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeForm()"
                        class="px-4 py-2 text-sm font-medium text-slate-700 border border-slate-300 rounded-md hover:bg-slate-50 transition cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition cursor-pointer">
                    Guardar metadato
                </button>
            </div>

        </form>
    </div>

</div>

<script>
const list       = document.getElementById('list');
const form       = document.getElementById('form');
const tSel       = document.getElementById('metadataType');
const sectionGroup = document.getElementById('sectionGroup');
const itemGroup  = document.getElementById('itemGroup');
const itemId     = document.getElementById('itemId');
const section    = document.getElementById('section');
const keywords   = document.getElementById('keywords');
const description = document.getElementById('description');
const dCount     = document.getElementById('dCount');
const formTitle  = document.getElementById('formTitle');
const metadataId = document.getElementById('metadataId');
const formMode   = document.getElementById('formMode');
const itemLabel  = document.getElementById('itemLabel');

function makeCombo(data, inputId, dropdownId, clearBtnId, emptyId) {
    const input    = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    const clearBtn = document.getElementById(clearBtnId);
    const empty    = document.getElementById(emptyId);
    let selectedId = null;

    function render(q) {
        const filtered = q
            ? data.filter(d => d.label.toLowerCase().includes(q.toLowerCase()))
            : data;

        dropdown.innerHTML = '';

        if (!filtered.length) {
            dropdown.classList.add('hidden');
            empty.classList.remove('hidden');
            return;
        }

        empty.classList.add('hidden');

        filtered.forEach(d => {
            const el = document.createElement('button');
            el.type = 'button';
            el.textContent = d.label;
            el.dataset.id = d.id;
            el.className = [
                'w-full text-left px-3 py-2 text-sm text-slate-700',
                'hover:bg-slate-50 transition',
                d.id == selectedId ? 'bg-blue-50 font-medium text-blue-600' : ''
            ].join(' ');
            el.addEventListener('mousedown', e => {
                e.preventDefault();
                select(d);
            });
            dropdown.appendChild(el);
        });

        dropdown.classList.remove('hidden');
    }

    function select(d) {
        selectedId = d.id;
        input.value = d.label;
        itemId.value = d.id;
        clearBtn.classList.remove('hidden');
        dropdown.classList.add('hidden');
        empty.classList.add('hidden');
    }

    function clear() {
        selectedId = null;
        input.value = '';
        itemId.value = '';
        clearBtn.classList.add('hidden');
        dropdown.classList.add('hidden');
        empty.classList.add('hidden');
    }

    function setById(id) {
        const d = data.find(x => x.id == id);
        if (d) select(d);
    }

    input.addEventListener('focus', () => render(input.value));
    input.addEventListener('input', () => {
        selectedId = null;
        itemId.value = '';
        clearBtn.classList.toggle('hidden', !input.value);
        render(input.value);
    });
    input.addEventListener('blur', () => {
        setTimeout(() => dropdown.classList.add('hidden'), 150);
    });
    clearBtn.addEventListener('click', clear);

    return { clear, setById };
}

const comboProducto = makeCombo(
    PRODUCTS_DATA,
    'comboProductoInput', 'comboProductoDropdown', 'comboProductoClear', 'comboProductoEmpty'
);
const comboNovedad = makeCombo(
    NOVEDADES_DATA,
    'comboNovedadInput', 'comboNovedadDropdown', 'comboNovedadClear', 'comboNovedadEmpty'
);

function updateUI() {
    const type = tSel.value;
    if (type === 'section') {
        sectionGroup.classList.remove('hidden');
        itemGroup.classList.add('hidden');
        itemId.value = '';
    } else {
        sectionGroup.classList.add('hidden');
        itemGroup.classList.remove('hidden');
        document.getElementById('wrapProducto').classList.toggle('hidden', type !== 'producto');
        document.getElementById('wrapNovedad').classList.toggle('hidden', type !== 'novedad');
        itemLabel.textContent = type === 'producto' ? 'Producto * (Codigo Ralux)' : 'Novedad *';
    }
}

tSel.addEventListener('change', updateUI);

function openCreate() {
    resetForm();
    formTitle.innerText = 'Crear metadato';
    list.classList.add('hidden');
    form.classList.remove('hidden');
}

function closeForm() {
    form.classList.add('hidden');
    list.classList.remove('hidden');
}

function resetForm() {
    metadataId.value = '';
    formMode.value = 'create';
    section.value = '';
    itemId.value = '';
    keywords.value = '';
    description.value = '';
    dCount.textContent = '0';
    tSel.value = 'section';
    comboProducto.clear();
    comboNovedad.clear();
    updateUI();
}

function editMeta(btn) {
    resetForm();
    formMode.value = 'edit';
    formTitle.innerText = 'Editar metadato';
    metadataId.value = btn.dataset.id;

    const sec = btn.dataset.section;
    keywords.value = btn.dataset.keywords;
    description.value = btn.dataset.description;
    dCount.innerText = btn.dataset.description.length;

    if (sec.startsWith('producto-')) {
        tSel.value = 'producto';
        updateUI();
        comboProducto.setById(sec.replace('producto-', ''));
    } else if (sec.startsWith('novedad-')) {
        tSel.value = 'novedad';
        updateUI();
        comboNovedad.setById(sec.replace('novedad-', ''));
    } else {
        tSel.value = 'section';
        section.value = sec;
        updateUI();
    }

    list.classList.add('hidden');
    form.classList.remove('hidden');
}

description.addEventListener('input', () => dCount.innerText = description.value.length);

function deleteMeta(id) {
    if (!confirm('¿Eliminar este metadato?')) return;
    const deleteUrl = @json(route('admin.metadata.delete', ['id' => '__ID__'], false)).replace('__ID__', id);
    const row = document.getElementById(`metadata-row-${id}`);

    if (row) {
        row.classList.add('opacity-50', 'pointer-events-none');
    }

    fetch(deleteUrl, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('No se pudo eliminar el metadato');
        }

        removeDeletedRow(id);
    })
    .catch(() => {
        if (row) {
            row.classList.remove('opacity-50', 'pointer-events-none');
        }

        alert('No se pudo eliminar el metadato. Intenta nuevamente.');
    });
}

function removeDeletedRow(id) {
    const row = document.getElementById(`metadata-row-${id}`);
    const tbody = document.getElementById('metadataTableBody');

    if (row) {
        row.remove();
    }

    const total = document.getElementById('metadataTotal');
    const last = document.getElementById('metadataLastItem');
    const first = document.getElementById('metadataFirstItem');

    const newTotal = total ? Math.max(parseInt(total.textContent || '0', 10) - 1, 0) : 0;

    if (total) {
        total.textContent = newTotal;
    }

    if (last) {
        last.textContent = Math.max(parseInt(last.textContent || '0', 10) - 1, 0);
    }

    if (first && total && parseInt(total.textContent || '0', 10) === 0) {
        first.textContent = '0';
    }

    const remainingRows = tbody ? tbody.querySelectorAll('tr[id^="metadata-row-"]').length : 0;

    if (remainingRows === 0 && newTotal > 0) {
        const url = new URL(window.location.href);
        const page = parseInt(url.searchParams.get('page') || '1', 10);

        if (page > 1) {
            url.searchParams.set('page', page - 1);
            window.location.href = url.toString();
            return;
        }

        window.location.reload();
        return;
    }

    if (tbody && remainingRows === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="px-5 py-10 text-center text-slate-400 text-sm">
                    No hay metadatos registrados.
                </td>
            </tr>
        `;
    }
}

updateUI();
</script>

@endsection
