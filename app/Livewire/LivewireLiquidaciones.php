<?php

namespace App\Livewire;

use App\Livewire\Form\LiquidacionForm;
use App\Models\Empleado;
use App\Models\Liquidacion;
use App\Models\SaldoVacacion;
use App\Models\Tipo;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireLiquidaciones extends Component
{
    use WithPagination;

    public LiquidacionForm $form;

    public string $search = '';
    public $modalVisible = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Liquidacion::with(['empleado.persona', 'tipoLiquidacion']);

        if (!empty($this->search)) {
            $query->whereHas('empleado', function ($q) {
                $q->where('codigo_empleado', 'like', "%{$this->search}%")
                    ->orWhereHas('persona', function ($p) {
                        $p->where('nombres', 'like', "%{$this->search}%")
                            ->orWhere('apellidos', 'like', "%{$this->search}%");
                    });
            });
        }

        return view('livewire.liquidaciones.index', [
            'liquidaciones' => $query->orderByDesc('id')->paginate(10),
            'empleados' => Empleado::with('persona')->where('estado', true)->get(),
            'tiposLiquidacion' => Tipo::categoria('tipo_liquidacion')->activos()->orderBy('orden')->get(),
        ]);
    }

    public function openModal()
    {
        $this->form->resetForm();
        $this->modalVisible = true;
    }

    public function calcular()
    {
        if (!$this->form->id_empleado || !$this->form->fecha_liquidacion) {
            return;
        }

        $empleado = Empleado::find($this->form->id_empleado);
        if (!$empleado) {
            return;
        }

        $fechaLiquidacion = Carbon::parse($this->form->fecha_liquidacion);
        $fechaIngreso = $empleado->fecha_ingreso;

        $anios = round($fechaIngreso->diffInDays($fechaLiquidacion) / 365.25, 2);
        $salarioPromedio = (float) $empleado->sueldo_actual;

        $inicioPeriodo = $fechaIngreso->greaterThan($fechaLiquidacion->copy()->startOfYear())
            ? $fechaIngreso
            : $fechaLiquidacion->copy()->startOfYear();
        $mesesEnAnio = min(12, round($inicioPeriodo->diffInDays($fechaLiquidacion) / 30, 2));

        $saldoVacaciones = SaldoVacacion::where('id_empleado', $empleado->id)
            ->whereHas('gestion', fn ($q) => $q->where('anio', $fechaLiquidacion->year))
            ->first();

        $this->form->tiempo_laborado_anios = $anios;
        $this->form->salario_promedio = $salarioPromedio;
        $this->form->monto_indemnizacion = round($salarioPromedio * $anios, 2);
        $this->form->monto_vacaciones_pendientes = $saldoVacaciones
            ? round($saldoVacaciones->dias_disponibles * ($salarioPromedio / 30), 2)
            : 0;
        $this->form->monto_aguinaldo_proporcional = round($salarioPromedio * ($mesesEnAnio / 12), 2);
        $this->form->monto_bono14_proporcional = round($salarioPromedio * ($mesesEnAnio / 12), 2);
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());

        try {
            $total = round(
                $this->form->monto_indemnizacion
                + $this->form->monto_vacaciones_pendientes
                + $this->form->monto_aguinaldo_proporcional
                + $this->form->monto_bono14_proporcional,
                2
            );

            Liquidacion::create(array_merge($this->form->toArray(), ['total_liquidacion' => $total]));

            $this->dispatch('toast', type: 'success', message: 'Liquidación registrada correctamente.');
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo registrar la liquidación. Inténtalo nuevamente.');
        }
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->form->resetForm();
        $this->modalVisible = false;
    }
}
