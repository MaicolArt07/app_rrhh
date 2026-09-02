<?php

namespace App\Livewire;

use App\Models\AnticipoPrestamo;
use App\Models\Asistencia;
use App\Models\Contrato;
use App\Models\Departamento;
use App\Models\DocumentoEmpleado;
use App\Models\Empleado;
use App\Models\Planilla;
use Illuminate\Support\Carbon;
use Livewire\Component;

class LivewireHome extends Component
{
    public function render()
    {
        $hoy = now();
        $inicioMes = $hoy->copy()->startOfMonth();
        $finMes = $hoy->copy()->endOfMonth();
        $limiteVencimiento = $hoy->copy()->addDays(30);

        $empleadosActivos = Empleado::where('estado', true)->count();

        $nominaDelMes = Planilla::whereBetween('fecha_inicio', [$inicioMes, $finMes])->sum('total_planilla');

        $asistenciasMes = Asistencia::whereBetween('fecha', [$inicioMes, $hoy])->get();
        $ausentes = $asistenciasMes->filter(fn ($a) => $a->estadoAsistencia?->codigo === 'ausente')->count();
        $ausentismo = $asistenciasMes->count() > 0 ? round(($ausentes / $asistenciasMes->count()) * 100, 1) : 0;

        $contratosPorVencer = Contrato::with('empleado.persona')
            ->where('estado', true)
            ->whereNotNull('fecha_fin')
            ->whereBetween('fecha_fin', [$hoy, $limiteVencimiento])
            ->orderBy('fecha_fin')
            ->limit(5)
            ->get();

        $documentosPorVencer = DocumentoEmpleado::with('empleado.persona')
            ->where('estado', true)
            ->whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [$hoy, $limiteVencimiento])
            ->orderBy('fecha_vencimiento')
            ->limit(5)
            ->get();

        $anticiposActivos = AnticipoPrestamo::where('estado_anticipo', 'activo')->count();
        $anticiposMontoPendiente = AnticipoPrestamo::where('estado_anticipo', 'activo')->sum('saldo_pendiente');

        // Nómina de los últimos 6 meses
        $nominaLabels = [];
        $nominaData = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = $hoy->copy()->subMonths($i);
            $nominaLabels[] = ucfirst($mes->translatedFormat('M Y'));
            $nominaData[] = (float) Planilla::whereBetween('fecha_inicio', [$mes->copy()->startOfMonth(), $mes->copy()->endOfMonth()])
                ->sum('total_planilla');
        }

        // Empleados activos por departamento
        $empleadosPorDepartamento = Departamento::withCount(['empleados' => function ($q) {
                $q->where('estado', true);
            }])
            ->having('empleados_count', '>', 0)
            ->orderByDesc('empleados_count')
            ->get()
            ->map(fn ($d) => ['label' => $d->nombre, 'total' => $d->empleados_count]);

        // Asistencia de los últimos 7 días
        $asistenciaLabels = [];
        $asistenciaPresentes = [];
        $asistenciaAusentes = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = $hoy->copy()->subDays($i);
            $registros = Asistencia::whereDate('fecha', $dia)->get();
            $asistenciaLabels[] = ucfirst($dia->translatedFormat('D d/m'));
            $asistenciaPresentes[] = $registros->filter(fn ($a) => $a->estadoAsistencia?->codigo !== 'ausente')->count();
            $asistenciaAusentes[] = $registros->filter(fn ($a) => $a->estadoAsistencia?->codigo === 'ausente')->count();
        }

        return view('livewire.inicio.index', [
            'empleadosActivos' => $empleadosActivos,
            'nominaDelMes' => $nominaDelMes,
            'ausentismo' => $ausentismo,
            'contratosPorVencer' => $contratosPorVencer,
            'documentosPorVencer' => $documentosPorVencer,
            'anticiposActivos' => $anticiposActivos,
            'anticiposMontoPendiente' => $anticiposMontoPendiente,
            'nominaLabels' => $nominaLabels,
            'nominaData' => $nominaData,
            'empleadosPorDepartamento' => $empleadosPorDepartamento,
            'asistenciaLabels' => $asistenciaLabels,
            'asistenciaPresentes' => $asistenciaPresentes,
            'asistenciaAusentes' => $asistenciaAusentes,
        ]);
    }
}
