<?php

namespace App\Http\Controllers\Productos;

use App\Exports\ProductoExport;
use App\Exports\ProductoPlantillaExport;
use App\Http\Controllers\Controller;
use App\Imports\ProductoAdminImport;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\ProductoTipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductoController extends Controller
{
    public function index()
    {
        $page = request('page', 1);
        $search = request('search', '');
        $sortField = request('sortField', 'orden');
        $sortDirection = request('sortDirection', 'asc');
        $tipoId = (string) request('tipo_id', '');
        $visible = (string) request('visible', '');

        $productos = Producto::with(['tipo', 'imagenPrincipal'])
            ->when($search, function ($query) use ($search) {
                $searchNorm = preg_replace('/[-\/\s]+/', '', trim($search));

                $query->where(function ($q) use ($search, $searchNorm) {
                    $q->where('codigo_ralux', 'like', "%{$search}%")
                        ->orWhere('descripcion_es', 'like', "%{$search}%")
                        ->orWhere('descripcion_en', 'like', "%{$search}%");

                    if ($searchNorm !== '') {
                        $q->orWhereRaw(
                            "REPLACE(REPLACE(REPLACE(codigo_ralux, '-', ''), '/', ''), ' ', '') LIKE ?",
                            ["%{$searchNorm}%"]
                        );
                    }
                });
            })
            ->when($tipoId !== '', function ($query) use ($tipoId) {
                $query->where('producto_tipo_id', $tipoId);
            })
            ->when($visible === '1', function ($query) {
                $query->where('visible', true);
            })
            ->when($visible === '0', function ($query) {
                $query->where('visible', false);
            })
            ->when($visible === 'destacado', function ($query) {
                $query->where('destacado', true);
            })
            ->when($sortField === 'orden', function ($q) use ($sortDirection) {
                $q->orderByRaw("CASE WHEN (orden IS NULL OR orden = '') THEN 1 ELSE 0 END ASC")
                    ->orderBy('orden', $sortDirection);
            }, function ($q) use ($sortField, $sortDirection) {
                $q->orderBy($sortField, $sortDirection);
            })
            ->paginate(10, ['*'], 'page', $page);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('livewire.productos.partials.table', compact('productos'))->render(),
                'pagination' => view('livewire.productos.partials.pagination', compact('productos'))->render(),
                'currentPage' => $productos->currentPage(),
                'lastPage' => $productos->lastPage(),
                'firstItem' => $productos->firstItem() ?? 0,
                'lastItem' => $productos->lastItem() ?? 0,
                'total' => $productos->total(),
            ]);
        }

        $tipos = ProductoTipo::orderBy('descripcion_es')->get();

        return view('livewire.productos.index', compact('productos', 'tipos'));
    }

    public function exportar()
    {
        $filename = 'productos_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new ProductoExport(), $filename);
    }

    public function exportarPlantilla()
    {
        return Excel::download(new ProductoPlantillaExport(), 'productos_plantilla.xlsx');
    }

    public function descargarCopiaSeguridad()
    {
        try {
            $connectionName = config('database.default');
            $connection = config("database.connections.{$connectionName}");

            if (!$connection || empty($connection['driver'])) {
                throw new \RuntimeException('No se pudo detectar la configuracion de la base de datos.');
            }

            $driver = $connection['driver'];
            $timestamp = now()->format('Y-m-d_His');
            $tempDirectory = storage_path('app/backups-temp');

            if (!File::exists($tempDirectory)) {
                File::makeDirectory($tempDirectory, 0755, true);
            }

            return match ($driver) {
                'sqlite' => $this->descargarBackupSqlite($connection, $tempDirectory, $timestamp),
                'mysql', 'mariadb' => $this->descargarBackupMysql($connectionName, $tempDirectory, $timestamp),
                default => throw new \RuntimeException("El driver de base de datos '{$driver}' no tiene backup automatico configurado."),
            };
        } catch (\Throwable $e) {
            return redirect()
                ->route('productos.index')
                ->with('error', 'No se pudo generar la copia de seguridad: ' . $e->getMessage());
        }
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:10240',
            'modo' => 'nullable|in:crear_actualizar,solo_actualizar',
        ], [
            'archivo.required' => 'Selecciona un archivo para importar.',
            'archivo.mimes' => 'El archivo debe ser Excel (.xlsx o .xls).',
            'archivo.max' => 'El archivo no puede superar los 10 MB.',
            'modo.in' => 'El modo de importacion seleccionado no es valido.',
        ]);

        try {
            @ini_set('max_execution_time', '0');
            @set_time_limit(0);

            $modo = $request->input('modo', 'crear_actualizar');
            $import = new ProductoAdminImport($modo === 'solo_actualizar');

            Excel::import($import, $request->file('archivo'));

            return response()->json([
                'success' => true,
                'message' => $import->getMensajeResumen(),
                'resumen' => $import->getResumen(),
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errores = collect($e->failures())
                ->map(fn ($f) => "Fila {$f->row()}: " . implode(', ', $f->errors()))
                ->implode(' | ');

            return response()->json([
                'success' => false,
                'message' => $errores,
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al importar: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $producto = Producto::with('imagenes')->findOrFail($id);

        foreach (['pdf', 'sonido', 'video', 'imagen_diagrama', 'diagrama_orientativo'] as $field) {
            if ($producto->$field && Storage::disk('public')->exists($producto->$field)) {
                Storage::disk('public')->delete($producto->$field);
            }
        }

        foreach ($producto->imagenes as $imagen) {
            if (Storage::disk('public')->exists($imagen->ruta)) {
                Storage::disk('public')->delete($imagen->ruta);
            }
        }

        $producto->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado correctamente',
            ]);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente');
    }

    public function destroyImagen($productoId, $imagenId)
    {
        $imagen = ProductoImagen::where('producto_id', $productoId)->findOrFail($imagenId);

        if (Storage::disk('public')->exists($imagen->ruta)) {
            Storage::disk('public')->delete($imagen->ruta);
        }

        $wasPrincipal = $imagen->principal;
        $imagen->delete();

        if ($wasPrincipal) {
            $next = ProductoImagen::where('producto_id', $productoId)->orderBy('orden')->first();
            if ($next) {
                $next->update(['principal' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada correctamente',
        ]);
    }

    public function destroyArchivo($productoId, string $field)
    {
        $allowedFields = [
            'pdf',
            'sonido',
            'video',
            'imagen_diagrama',
            'diagrama_orientativo',
        ];

        if (!in_array($field, $allowedFields, true)) {
            abort(404);
        }

        $producto = Producto::findOrFail($productoId);
        $path = $producto->{$field};

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $producto->update([$field => null]);

        return response()->json([
            'success' => true,
            'message' => 'Archivo eliminado correctamente',
        ]);
    }

    public function setPrincipalImagen($productoId, $imagenId)
    {
        ProductoImagen::where('producto_id', $productoId)->update(['principal' => false]);
        ProductoImagen::where('producto_id', $productoId)
            ->where('id', $imagenId)
            ->update(['principal' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Imagen principal actualizada',
        ]);
    }

    private function descargarBackupSqlite(array $connection, string $tempDirectory, string $timestamp)
    {
        $databasePath = $connection['database'] ?? database_path('database.sqlite');

        if (!File::exists($databasePath)) {
            abort(500, 'No se encontro el archivo de la base de datos SQLite.');
        }

        $backupPath = $tempDirectory . DIRECTORY_SEPARATOR . "backup_bd_{$timestamp}.sqlite";
        File::copy($databasePath, $backupPath);

        return response()->download($backupPath, basename($backupPath))->deleteFileAfterSend(true);
    }

    private function descargarBackupMysql(string $connectionName, string $tempDirectory, string $timestamp)
    {
        $backupPath = $tempDirectory . DIRECTORY_SEPARATOR . "backup_bd_{$timestamp}.sql";
        File::put($backupPath, $this->generarDumpMysql($connectionName));

        return response()->download($backupPath, basename($backupPath))->deleteFileAfterSend(true);
    }

    private function generarDumpMysql(string $connectionName): string
    {
        $connection = DB::connection($connectionName);
        $databaseName = $connection->getDatabaseName();
        $pdo = $connection->getPdo();

        $dump = [];
        $dump[] = '-- Backup de base de datos generado por Ralux';
        $dump[] = '-- Fecha: ' . now()->toDateTimeString();
        $dump[] = '-- Base: ' . $databaseName;
        $dump[] = '';
        $dump[] = 'SET FOREIGN_KEY_CHECKS=0;';
        $dump[] = '';

        $tablesResult = $connection->select('SHOW FULL TABLES WHERE Table_type = ?', ['BASE TABLE']);
        if (empty($tablesResult)) {
            throw new \RuntimeException('No se encontraron tablas para exportar.');
        }

        foreach ($tablesResult as $tableResult) {
            $tableData = (array) $tableResult;
            $tableName = null;

            foreach ($tableData as $key => $value) {
                if ($key !== 'Table_type') {
                    $tableName = $value;
                    break;
                }
            }

            if (!$tableName) {
                continue;
            }

            $createTableResult = (array) $connection->selectOne('SHOW CREATE TABLE `' . str_replace('`', '``', $tableName) . '`');
            $createTableSql = $createTableResult['Create Table'] ?? array_values($createTableResult)[1] ?? null;

            if (!$createTableSql) {
                throw new \RuntimeException("No se pudo obtener la definicion de la tabla {$tableName}.");
            }

            $dump[] = '-- Estructura de tabla `' . $tableName . '`';
            $dump[] = 'DROP TABLE IF EXISTS `' . str_replace('`', '``', $tableName) . '`;';
            $dump[] = $createTableSql . ';';
            $dump[] = '';

            $columnsResult = $connection->select('SHOW COLUMNS FROM `' . str_replace('`', '``', $tableName) . '`');
            $columns = array_map(fn ($column) => $column->Field, $columnsResult);

            if (empty($columns)) {
                continue;
            }

            $columnList = implode(', ', array_map(
                fn ($column) => '`' . str_replace('`', '``', $column) . '`',
                $columns
            ));

            $rows = $connection->table($tableName)->get();
            $insertRows = [];

            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $values = [];

                foreach ($columns as $column) {
                    $values[] = $this->formatearValorSql($rowArray[$column] ?? null, $pdo);
                }

                $insertRows[] = '(' . implode(', ', $values) . ')';

                if (count($insertRows) === 200) {
                    $dump[] = 'INSERT INTO `' . str_replace('`', '``', $tableName) . '` (' . $columnList . ') VALUES';
                    $dump[] = implode(",\n", $insertRows) . ';';
                    $dump[] = '';
                    $insertRows = [];
                }
            }

            if (!empty($insertRows)) {
                $dump[] = 'INSERT INTO `' . str_replace('`', '``', $tableName) . '` (' . $columnList . ') VALUES';
                $dump[] = implode(",\n", $insertRows) . ';';
                $dump[] = '';
            }
        }

        $dump[] = 'SET FOREIGN_KEY_CHECKS=1;';
        $dump[] = '';

        return implode("\n", $dump);
    }

    private function formatearValorSql($value, \PDO $pdo): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return $pdo->quote((string) $value);
    }
}
