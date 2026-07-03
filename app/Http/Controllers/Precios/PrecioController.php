<?php

namespace App\Http\Controllers\Precios;

use App\Exports\ListaPreciosProductosExport;
use App\Http\Controllers\Controller;
use App\Models\Precio;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PrecioController extends Controller
{
    public function index()
    {
        $precios = Precio::orderBy('created_at', 'desc')->get();
        $productosVisibles = Producto::where('visible', true)->count();
        $listaPublicada = $precios->firstWhere('publicado', true);

        return view('livewire.precios.index', compact('precios', 'productosVisibles', 'listaPublicada'));
    }

    public function create()
    {
        return view('livewire.precios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'archivo' => 'required|file|mimes:pdf,xls,xlsx|max:20480',
        ]);

        $precio = new Precio();
        $precio->title   = $request->title;
        $precio->archivo = $request->file('archivo')->store('precios', 'public');
        $precio->tipo = 'propia';
        $precio->publicado = $request->boolean('publicado');
        $precio->save();

        if ($precio->publicado) {
            $this->publicarSolo($precio);
        }

        return redirect()->route('precios.index')->with('success', 'Lista de precios propia cargada exitosamente');
    }

    public function generarDesdeProductos(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
        ]);

        $title = $request->filled('title')
            ? $request->title
            : 'Lista de precios - ' . now()->format('d/m/Y');

        $filename = 'precios/lista-precios-productos-' . now()->format('Y-m-d-His') . '.xlsx';

        Excel::store(new ListaPreciosProductosExport(), $filename, 'public');

        $precio = Precio::create([
            'title' => $title,
            'archivo' => $filename,
            'tipo' => 'generada',
            'publicado' => $request->boolean('publicado'),
            'generado_at' => now(),
        ]);

        if ($precio->publicado) {
            $this->publicarSolo($precio);
        }

        return redirect()->route('precios.index')->with('success', 'Lista de precios generada exitosamente');
    }

    public function edit($id)
    {
        $precio = Precio::findOrFail($id);
        return view('livewire.precios.edit', compact('precio'));
    }

    public function update(Request $request, $id)
    {
        $precio = Precio::findOrFail($id);

        $request->validate([
            'title'   => 'required|string|max:255',
            'archivo' => 'nullable|file|mimes:pdf,xls,xlsx|max:20480',
        ]);

        $precio->title = $request->title;

        if ($request->hasFile('archivo')) {
            Storage::disk('public')->delete($precio->archivo);
            $precio->archivo = $request->file('archivo')->store('precios', 'public');
            $precio->tipo = 'propia';
            $precio->generado_at = null;
        }

        $precio->publicado = $request->boolean('publicado');

        $precio->save();

        if ($precio->publicado) {
            $this->publicarSolo($precio);
        }

        return redirect()->route('precios.index')->with('success', 'Lista de precios actualizada exitosamente');
    }

    public function publicar($id)
    {
        $precio = Precio::findOrFail($id);
        $this->publicarSolo($precio);

        return redirect()->route('precios.index')->with('success', 'Lista publicada para clientes');
    }

    public function destroy($id)
    {
        $precio = Precio::findOrFail($id);
        Storage::disk('public')->delete($precio->archivo);
        $precio->delete();

        return response()->json(['success' => true, 'message' => 'Lista de precios eliminada exitosamente']);
    }

    private function publicarSolo(Precio $precio): void
    {
        Precio::where('id', '!=', $precio->id)->update(['publicado' => false]);
        $precio->forceFill(['publicado' => true])->save();
    }
}
