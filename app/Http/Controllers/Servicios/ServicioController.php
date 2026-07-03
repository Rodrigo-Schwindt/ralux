<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use App\Models\ServicioDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicioController extends Controller
{
    public function index()
    {
        $servicio = Servicio::with('downloads')->first();
        return view('livewire.servicios.admin', compact('servicio'));
    }

    public function save(Request $request)
    {
        $servicio = Servicio::first() ?? new Servicio();

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description_1' => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpg,png,jpeg,webp,svg|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($servicio->image && Storage::disk('public')->exists($servicio->image)) {
                Storage::disk('public')->delete($servicio->image);
            }
            $validated['image'] = $request->file('image')->store('servicios', 'public');
        }

        $servicio->fill($validated)->save();

        return redirect()->back()->with('success', 'Contenido actualizado correctamente');
    }

    public function deleteImage()
    {
        $servicio = Servicio::firstOrFail();

        if ($servicio->image && Storage::disk('public')->exists($servicio->image)) {
            Storage::disk('public')->delete($servicio->image);
        }

        $servicio->image = null;
        $servicio->save();

        return response()->json(['success' => true]);
    }

    public function addDownload(Request $request)
    {
        $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'image'       => 'nullable|image|mimes:jpg,png,jpeg,webp,svg|max:4096',
            'file'        => 'required|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $servicio = Servicio::firstOrFail();

        $data = [
            'servicio_id' => $servicio->id,
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'orden'       => ServicioDownload::where('servicio_id', $servicio->id)->count(),
        ];

        $data['file'] = $request->file('file')->store('servicios/downloads', 'public');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('servicios/logos', 'public');
        }

        ServicioDownload::create($data);

        return redirect()->back()->with('success', 'Descarga agregada correctamente');
    }

    public function deleteDownload($id)
    {
        $download = ServicioDownload::findOrFail($id);

        if ($download->file && Storage::disk('public')->exists($download->file)) {
            Storage::disk('public')->delete($download->file);
        }
        if ($download->image && Storage::disk('public')->exists($download->image)) {
            Storage::disk('public')->delete($download->image);
        }

        $download->delete();

        return redirect()->back()->with('success', 'Descarga eliminada correctamente');
    }
}
