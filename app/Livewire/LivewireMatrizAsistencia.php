<?php

namespace App\Livewire;

use App\Exports\MatrizAsistenciaExport;
use App\Models\Area;
use App\Models\Asistencia;
use App\Models\Empresa;
use App\Models\TipoAsistencia;
use App\Services\Asistencia\AsistenciaService;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class LivewireMatrizAsistencia extends Component
{
    #[Url]
    public $mes = '';

    #[Url(as: 'area')]
    public $areaFiltro = '';

    #[Url(as: 'empresa')]
    public $empresaFiltro = '';

    public $search = '';

    /** matriz | resumen | detalle | incompletos */
    #[Url]
    public $tab = 'matriz';

    public $celdaId = null;
    public $celdaTipo = '';
    public $celdaObservacion = '';

    public function mount()
    {
        if (! preg_match('/^\d{4}-\d{2}$/', (string) $this->mes)) {
            $ultima = Asistencia::where('origen', 'biometrico')->max('fecha');
            $this->mes = ($ultima ? Carbon::parse($ultima) : now())->format('Y-m');
        }
    }

    private function mesCarbon(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $this->mes.'-01')->startOfDay();
    }

    public function render(AsistenciaService $servicio)
    {
        $mes = $this->mesCarbon();
        $filas = $servicio->matriz($mes, $this->areaFiltro ? (int) $this->areaFiltro : null, trim($this->search) ?: null, $this->empresaFiltro ? (int) $this->empresaFiltro : null);

        return view('livewire.asistencia-biometrico.matriz', [
            'filas' => $filas,
            'dias' => $servicio->diasHabiles($mes),
            'tipos' => TipoAsistencia::porCodigo(),
            'areas' => Area::where('estado', true)->orderBy('nombre')->get(),
            'empresas' => Empresa::where('estado', true)->orderBy('nombre')->get(),
            'tituloMes' => ucfirst($mes->translatedFormat('F Y')),
            'totalesGenerales' => [
                'atrasos' => $filas->sum('totales.atrasos'),
                'minutos' => $filas->sum('totales.minutos'),
                'incompletos' => $filas->sum('totales.incompletos'),
                'sin_marcar' => $filas->sum('totales.sin_marcar'),
                'eventos' => $filas->sum('totales.eventos'),
                'multa' => $filas->sum('totales.multa'),
            ],
            'celda' => $this->celdaId ? Asistencia::with(['empleado.persona', 'tipoAsistencia', 'usuarioModifico'])->find($this->celdaId) : null,
            'puedeEditar' => auth()->user()->can('Editar Matriz Asistencia'),
        ]);
    }

    public function abrirCelda($id)
    {
        abort_unless(auth()->user()->can('Editar Matriz Asistencia'), 403);

        $asistencia = Asistencia::with('tipoAsistencia')->findOrFail($id);
        $this->celdaId = $asistencia->id;
        $this->celdaTipo = $asistencia->tipo_asignado_manual || $asistencia->tipoAsistencia?->codigo === TipoAsistencia::REMOTO
            ? $asistencia->tipoAsistencia?->codigo
            : '';
        $this->celdaObservacion = (string) $asistencia->observaciones;
    }

    public function guardarCelda(AsistenciaService $servicio)
    {
        abort_unless(auth()->user()->can('Editar Matriz Asistencia'), 403);

        $asistencia = Asistencia::findOrFail($this->celdaId);
        $permitidos = TipoAsistencia::porCodigo()->where('seleccionable_manual', true)->where('estado', true)->keys()->all();

        $this->validate([
            'celdaTipo' => ['nullable', 'in:'.implode(',', $permitidos)],
            'celdaObservacion' => ['nullable', 'string', 'max:500'],
        ]);

        $servicio->asignarTipo($asistencia, $this->celdaTipo ?: null, auth()->id(), $this->celdaObservacion);

        $this->cerrarCelda();
        $this->dispatch('toast', type: 'success', message: 'Día actualizado. Los totales y la multa se recalcularon.');
    }

    public function cerrarCelda()
    {
        $this->reset(['celdaId', 'celdaTipo', 'celdaObservacion']);
        $this->resetValidation();
    }

    /** Vuelve a clasificar el mes (p. ej. después de registrar feriados o permisos). */
    public function recalcular(AsistenciaService $servicio)
    {
        abort_unless(auth()->user()->can('Editar Matriz Asistencia'), 403);

        $mes = $this->mesCarbon();
        $servicio->recalcular($mes, $mes->copy()->endOfMonth());
        $this->dispatch('toast', type: 'success', message: 'Mes recalculado con los feriados, permisos y días remotos vigentes.');
    }

    public function exportar()
    {
        return Excel::download(
            new MatrizAsistenciaExport($this->mesCarbon(), $this->areaFiltro ? (int) $this->areaFiltro : null, trim($this->search) ?: null, $this->empresaFiltro ? (int) $this->empresaFiltro : null),
            'asistencia-'.$this->mes.'.xlsx'
        );
    }
}
