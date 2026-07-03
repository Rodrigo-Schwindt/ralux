<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Contact;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class FichaTecnicaController extends Controller
{
    public function download(int $id)
    {
        $pdf = $this->buildPdf($id);
        $filename = $this->buildFilename($pdf['producto']->codigo_ralux);

        return $pdf['instance']->download($filename);
    }

    public function view(int $id)
    {
        $pdf = $this->buildPdf($id);
        $filename = $this->buildFilename($pdf['producto']->codigo_ralux);

        return $pdf['instance']->stream($filename);
    }

    private function buildFilename(string $codigo): string
    {
        $safe = str_replace(['/', '\\'], '-', $codigo);
        return 'ficha-tecnica-' . $safe . '.pdf';
    }

    private function buildPdf(int $id): array
    {
        $producto = Producto::with(['tipo', 'imagenes', 'marcas', 'modelos.marca', 'codigosOM.marca'])
            ->where('visible', true)
            ->findOrFail($id);

        $contact = Contact::first();

        $logoBase64 = $this->toBase64($contact?->icono_1);
        $imagenBase64 = $this->toBase64(
            $producto->imagenes->firstWhere('principal', true)?->ruta
            ?? $producto->imagenes->first()?->ruta
        );
        $diagramaBase64 = $this->toBase64($producto->imagen_diagrama);
        $diagramaOrientativoBase64 = $this->toBase64($producto->diagrama_orientativo);

        $pdf = Pdf::loadView('pdf.ficha-tecnica', [
            'producto'                  => $producto,
            'logoBase64'                => $logoBase64,
            'imagenBase64'              => $imagenBase64,
            'diagramaBase64'            => $diagramaBase64,
            'diagramaOrientativoBase64' => $diagramaOrientativoBase64,
            'fecha'                     => now()->format('d/m/Y'),
        ])->setPaper('a4', 'portrait');

        return [
            'instance' => $pdf,
            'producto' => $producto,
        ];
    }

    private function toBase64(?string $ruta): ?string
    {
        if (!$ruta) return null;

        $ruta = ltrim($ruta, '/');
        if (str_starts_with($ruta, 'storage/')) {
            $ruta = substr($ruta, strlen('storage/'));
        }

        $path = Storage::disk('public')->path($ruta);
        if (!file_exists($path)) {
            $path = public_path($ruta);
        }

        if (!file_exists($path)) return null;

        $mime = mime_content_type($path);
        // DomPDF only supports raster images; skip SVG
        if (str_contains($mime, 'svg')) return null;

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }
}
