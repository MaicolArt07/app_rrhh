<?php

namespace App\Livewire;

use App\Models\DiaRemoto;
use App\Models\Empleado;
use App\Services\Asistencia\AsistenciaService;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireDiasRemotos extends Component
{
    use WithPagination;

    public $mesFiltro = '';
    public $empleadoFiltro = '';
    public $mostrarAnulados = false;

    public $modalVisible = false;
    public $id_empleado = '';
    public $desde = '';
    public $hasta = '';
    /** Días de la semana a asignar dentro del rango (1 = lunes … 5 = viernes). */
    public $diasSemana = [1, 2, 3, 4, 5];
    public $observacion = '';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->mesFiltro = now()->format('Y-m');
    }

    public function updating($propiedad)
    {
        if (in_array($propiedad, ['mesFiltro', 'empleadoFiltro', 'mostrarAnulados'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = DiaRemoto::with(['empleado.persona', 'asignadoPor'])
            ->when(! $this->mostrarAnulados, fn ($q) => $q->where('estado', true))
            ->when($this->empleadoFiltro, fn ($q) => $q->where('id_empleado', $this->empleadoFiltro))
            ->when(preg_match('/^\d{4}-\d{2}$/', (string) $this->mesFiltro), function ($q) {
                $mes = Carbon::createFromFormat('Y-m-d', $this->mesFiltro.'-01');
                $q->whereBetween('fecha', [$mes->copy()->startOfMonth()->toDateString(), $mes->copy()->endOfMonth()->toDateString()]);
            });

        return view('livewire.asistencia-biometrico.dias-remotos', [
            'dias' => $query->orderBy('fecha')->paginate(20),
            'empleados' => Empleado::with('persona')->where('estado', true)->get()->sortBy(fn ($e) => $e->persona?->nombreCompleto()),
            'fechasPrevistas' => $this->fechasSeleccionadas(),
        ]);
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['id_empleado', 'observacion']);
        $this->diasSemana = [1, 2, 3, 4, 5];
        $this->desde = $this->hasta = now()->toDateString();
        $this->modalVisible = true;
    }

    public function closeModal()
    {
        $this->modalVisible = false;
    }

    /**
     * @return list<string>
     */
    private function fechasSeleccionadas(): array
    {
        if (! $this->desde || ! $this->hasta) {
            return [];
        }

        try {
            $desde = Carbon::parse($this->desde);
            $hasta = Carbon::parse($this->hasta);
        } catch (\Throwable) {
            return [];
        }

        if ($hasta->lt($desde) || $desde->diffInDays($hasta) > 366) {
            return [];
        }

        $fechas = [];
        for ($d = $desde->copy(); $d->lte($hasta); $d->addDay()) {
            if (! $d->isWeekend() && in_array($d->isoWeekday(), array_map('intval', $this->diasSemana))) {
                $fechas[] = $d->toDateString();
            }
        }

        return $fechas;
    }

    public function save(AsistenciaService $servicio)
    {
        abort_unless(auth()->user()->can('Asignar Dias Remotos'), 403);

        $this->validate([
            'id_empleado' => 'required|exists:empleados,id',
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
            'diasSemana' => 'required|array|min:1',
            'observacion' => 'nullable|string|max:255',
        ], [
            'id_empleado.required' => 'Debe seleccionar un empleado.',
            'hasta.after_or_equal' => 'La fecha final debe ser igual o posterior a la inicial.',
            'diasSemana.required' => 'Seleccione al menos un día de la semana.',
        ]);

        $fechas = $this->fechasSeleccionadas();

        if (! $fechas) {
            $this->addError('desde', 'El rango no contiene días hábiles para los días de la semana elegidos.');

            return;
        }

        $total = $servicio->asignarRemotos((int) $this->id_empleado, $fechas, $this->observacion, auth()->id());

        $this->modalVisible = false;
        $this->dispatch('toast', type: 'success', message: "Se asignaron {$total} día(s) remoto(s). La matriz ya refleja el cambio.");
    }

    public function anular($id)
    {
        abort_unless(auth()->user()->can('Asignar Dias Remotos'), 403);

        app(AsistenciaService::class)->anularRemoto(DiaRemoto::findOrFail($id));
        $this->dispatch('toast', type: 'warning', message: 'Día remoto anulado. El día se volvió a clasificar según sus marcaciones.');
    }
}
