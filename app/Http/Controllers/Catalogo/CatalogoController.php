<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CatalogoController extends Controller
{
    public function index(Request $request)
{
    $query = Catalogo::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('orden', 'like', "%{$search}%");
        });
    }

    $sortField = $request->get('sortField', 'orden');
    $sortDirection = $request->get('sortDirection', 'asc');
    $query->orderBy($sortField, $sortDirection);

    $catalogos = $query->paginate(10);

    if ($request->ajax()) {
        return response()->json([
            'html' => view('livewire.catalogos.partials.table', compact('catalogos'))->render(),
            'pagination' => view('livewire.catalogos.partials.pagination', compact('catalogos'))->render(),
        ]);
    }

    return view('livewire.catalogos.index', compact('catalogos'));
}


    public function create()
    {
        return view('livewire.catalogos.create');
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'image_1' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image_2' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'orden' => 'nullable|string|max:10',
            'pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $catalogo = new Catalogo();
        $catalogo->title = $validated['title'];
        $catalogo->subtitle = $validated['subtitle'] ?? null;
        $catalogo->descripcion = $validated['descripcion'] ?? null;
        $catalogo->orden = $validated['orden'] ?? null;
        $catalogo->visible = $request->has('visible') ? 1 : 0;

        if ($request->hasFile('image_1')) {
            $catalogo->image_1 = $request->file('image_1')->store('catalogos', 'public');
        }

        if ($request->hasFile('image_2')) {
            $catalogo->image_2 = $request->file('image_2')->store('catalogos', 'public');
        }

        if ($request->hasFile('pdf')) {
            $catalogo->pdf = $request->file('pdf')->store('catalogos/pdfs', 'public');
        }

        $catalogo->save();

        return redirect()->route('catalogos.index')->with('success', 'Catálogo creado exitosamente');
    }

    public function edit(Catalogo $catalogo)
    {
        return view('livewire.catalogos.edit', compact('catalogo'));
    }

    public function update(Request $request, Catalogo $catalogo)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'image_1' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image_2' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'orden' => 'nullable|string|max:10',
            'pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $catalogo->title = $validated['title'];
        $catalogo->subtitle = $validated['subtitle'] ?? null;
        $catalogo->descripcion = $validated['descripcion'] ?? null;
        $catalogo->orden = $validated['orden'] ?? null;
        $catalogo->visible = $request->has('visible') ? 1 : 0;

        if ($request->hasFile('image_1')) {
            if ($catalogo->image_1) {
                Storage::disk('public')->delete($catalogo->image_1);
            }
            $catalogo->image_1 = $request->file('image_1')->store('catalogos', 'public');
        }

        if ($request->hasFile('image_2')) {
            if ($catalogo->image_2) {
                Storage::disk('public')->delete($catalogo->image_2);
            }
            $catalogo->image_2 = $request->file('image_2')->store('catalogos', 'public');
        }

        if ($request->hasFile('pdf')) {
            if ($catalogo->pdf) {
                Storage::disk('public')->delete($catalogo->pdf);
            }
            $catalogo->pdf = $request->file('pdf')->store('catalogos/pdfs', 'public');
        }

        $catalogo->save();

        return redirect()->route('catalogos.index')->with('success', 'Catálogo actualizado exitosamente');
    }

    public function destroy(Catalogo $catalogo)
    {
        if ($catalogo->image_1) {
            Storage::disk('public')->delete($catalogo->image_1);
        }

        if ($catalogo->image_2) {
            Storage::disk('public')->delete($catalogo->image_2);
        }

        if ($catalogo->pdf) {
            Storage::disk('public')->delete($catalogo->pdf);
        }

        $catalogo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catálogo eliminado exitosamente'
        ]);
    }
}