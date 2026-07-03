<?php

namespace App\Livewire\Zona;

use App\Models\Precio;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\IOFactory;

#[Layout('layouts.zone')]
class PrecioPreview extends Component
{
    public Precio $precio;

    public array $headers = [];

    public array $rows = [];

    public string $extension = '';

    public function mount($id)
    {
        $this->precio = Precio::where('publicado', true)->findOrFail($id);
        $this->extension = strtolower(pathinfo($this->precio->archivo, PATHINFO_EXTENSION));

        abort_unless(in_array($this->extension, ['xls', 'xlsx'], true), 404);
        abort_unless(Storage::disk('public')->exists($this->precio->archivo), 404);

        $this->cargarExcel();
    }

    private function cargarExcel(): void
    {
        $path = Storage::disk('public')->path($this->precio->archivo);
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $data = $sheet->rangeToArray("A1:{$highestColumn}{$highestRow}", null, true, false, false);

        $this->headers = array_map(fn ($value) => $this->formatearCelda($value), array_shift($data) ?? []);
        $this->rows = collect($data)
            ->filter(fn ($row) => collect($row)->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty())
            ->map(fn ($row) => array_map(fn ($value) => $this->formatearCelda($value), $row))
            ->values()
            ->all();

        $spreadsheet->disconnectWorksheets();
    }

    private function formatearCelda($value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_float($value)) {
            return rtrim(rtrim(number_format($value, 2, ',', '.'), '0'), ',');
        }

        return trim((string) $value);
    }

    public function descargar()
    {
        return Storage::disk('public')->download($this->precio->archivo);
    }

    public function render()
    {
        return view('livewire.zona.precio-preview');
    }
}
