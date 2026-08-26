<?php

namespace App\Livewire;

use App\Exports\ActivityLogExport;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class LivewireConfiguracion extends Component
{
    public function render()
    {
        $carpeta = config('backup.backup.name');
        $backups = collect(Storage::disk('local')->allFiles($carpeta))
            ->filter(fn ($f) => str_ends_with($f, '.zip'))
            ->map(fn ($f) => [
                'nombre' => basename($f),
                'tamano' => round(Storage::disk('local')->size($f) / 1024 / 1024, 2),
                'fecha' => \Illuminate\Support\Carbon::createFromTimestamp(Storage::disk('local')->lastModified($f)),
            ])
            ->sortByDesc('fecha')
            ->values();

        return view('livewire.configuracion.index', [
            'appName' => config('app.name'),
            'appEnv' => config('app.env'),
            'dbConnection' => config('database.default'),
            'backups' => $backups,
        ]);
    }

    public function generarRespaldo()
    {
        try {
            Artisan::call('backup:run', ['--only-db' => false]);
            $this->dispatch('toast', type: 'success', message: 'Respaldo generado correctamente.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo generar el respaldo: ' . $e->getMessage());
        }
    }

    public function exportarHistorial()
    {
        return Excel::download(new ActivityLogExport(), 'historial-actividad-' . now()->format('Y-m-d') . '.xlsx');
    }
}
