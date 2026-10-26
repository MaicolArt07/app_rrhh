<?php

namespace App\Livewire;

use App\Models\Empleado;
use App\Models\ImportacionBiometrico;
use App\Services\Asistencia\AsistenciaService;
use App\Services\Asistencia\LectorReporteBiometrico;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

class LivewireImportarBiometrico extends Component
{
    use WithFileUploads;

    public $archivo;
    public $nombreArchivo = '';

    /** Resultado de LectorReporteBiometrico::leer() */
    public $reporte = null;

    /** ID del reloj → id del empleado ('' = no vincular) */
    public $vinculos = [];

    /** ID del reloj → cómo se obtuvo el vínculo: vinculado | sugerido | '' */
    public $origenVinculo = [];

    public $mostrarSinMarcas = false;
    public $resultado = null;

    public function updatedArchivo()
    {
        $this->reset(['reporte', 'vinculos', 'origenVinculo', 'resultado']);
        $this->validate(['archivo' => 'required|file|mimes:xls,xlsx|max:10240'], [
            'archivo.mimes' => 'El archivo debe ser el reporte del reloj en formato .xls o .xlsx.',
        ]);

        try {
            $this->reporte = app(LectorReporteBiometrico::class)->leer($this->archivo->getRealPath());
        } catch (\Throwable $e) {
            $this->addError('archivo', $e->getMessage());

            return;
        }

        $this->nombreArchivo = $this->archivo->getClientOriginalName();
        $this->prellenarVinculos();
    }

    private function prellenarVinculos(): void
    {
        $servicio = app(AsistenciaService::class);
        $empleados = Empleado::with('persona')->get();
        $porCodigo = $empleados->whereNotNull('codigo_biometrico')->keyBy('codigo_biometrico');

        foreach ($this->reporte['empleados'] as $bloque) {
            $codigo = $bloque['codigo'];

            if ($empleado = $porCodigo[$codigo] ?? null) {
                $this->vinculos[$codigo] = $empleado->id;
                $this->origenVinculo[$codigo] = 'vinculado';
            } elseif ($empleado = $servicio->sugerirEmpleado($bloque['nombre'], $empleados->whereNull('codigo_biometrico'))) {
                $this->vinculos[$codigo] = $empleado->id;
                $this->origenVinculo[$codigo] = 'sugerido';
            } else {
                $this->vinculos[$codigo] = '';
                $this->origenVinculo[$codigo] = '';
            }
        }
    }

    public function importar()
    {
        abort_unless(auth()->user()->can('Importar Asistencia'), 403);

        if (! $this->reporte) {
            return;
        }

        $vinculos = collect($this->vinculos)->map(fn ($id) => $id ? (int) $id : null);
        $duplicados = $vinculos->filter()->duplicates();

        if ($duplicados->isNotEmpty()) {
            $this->addError('vinculos', 'Un mismo empleado está vinculado a más de un ID del reloj. Revise las filas marcadas.');

            return;
        }

        try {
            $importacion = app(AsistenciaService::class)->importar($this->reporte, $vinculos->all(), $this->nombreArchivo, auth()->id());
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', type: 'error', message: 'No se pudo importar el reporte: '.$e->getMessage());

            return;
        }

        $this->resultado = [
            'empleados' => $importacion->total_empleados,
            'no_vinculados' => $importacion->total_no_vinculados,
            'mes' => $importacion->periodo_inicio->format('Y-m'),
        ];
        $this->reset(['archivo', 'reporte', 'vinculos', 'origenVinculo']);
        $this->dispatch('toast', type: 'success', message: 'Reporte importado correctamente.');
    }

    public function cancelar()
    {
        $this->reset(['archivo', 'reporte', 'vinculos', 'origenVinculo', 'resultado', 'nombreArchivo']);
    }

    public function render()
    {
        $bloques = collect($this->reporte['empleados'] ?? [])->map(function ($bloque) {
            $bloque['dias_con_marca'] = collect($bloque['dias'])->filter(fn ($crudo) => $crudo !== '')->count();

            return $bloque;
        });

        $duplicados = collect($this->vinculos)->filter()->duplicates()->values()->all();

        return view('livewire.asistencia-biometrico.importar', [
            'bloques' => $this->mostrarSinMarcas
                ? $bloques
                : $bloques->filter(fn ($b) => $b['dias_con_marca'] > 0 || ($this->vinculos[$b['codigo']] ?? '') !== ''),
            'totalBloques' => $bloques->count(),
            'totalVinculados' => collect($this->vinculos)->filter()->count(),
            'sinVincularConMarcas' => $bloques->filter(fn ($b) => $b['dias_con_marca'] > 0 && ! ($this->vinculos[$b['codigo']] ?? ''))->count(),
            'duplicados' => $duplicados,
            'empleados' => $this->reporte
                ? Empleado::with('persona')->get()->sortBy(fn ($e) => $e->persona?->nombreCompleto())
                : collect(),
            'periodo' => $this->reporte
                ? Carbon::parse($this->reporte['periodo_inicio'])->format('d/m/Y').' al '.Carbon::parse($this->reporte['periodo_fin'])->format('d/m/Y')
                : null,
            'importaciones' => ImportacionBiometrico::with('usuario')->latest()->limit(10)->get(),
        ]);
    }
}
