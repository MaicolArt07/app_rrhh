<?php

namespace App\Livewire;

use App\Livewire\Form\HoraExtraDetalleForm;
use App\Models\Empleado;
use App\Models\Gestion;
use App\Models\HoraExtraDetalle;
use App\Models\Mes;
use App\Models\Tipo;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireHorasExtra extends Component
{
    use WithPagination;

    public HoraExtraDetalleForm $form;

    public $mesFiltro = '';
    public string $search = '';
    public $modalVisible = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
        $this->mesFiltro = (int) now()->month;
    }

    public function updatingMesFiltro()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedFormIdEmpleado($value)
    {
        if ($value && !$this->form->valor_hora_base) {
            $empleado = Empleado::find($value);
            if ($empleado) {
                $this->form->valor_hora_base = round($empleado->sueldo_actual / 30 / 8, 2);
            }
        }
    }

    public function render()
    {
        $query = HoraExtraDetalle::query()->with(['empleado.persona', 'tipoHoraExtra']);

        if (!empty($this->mesFiltro)) {
            $query->whereHas('mes', fn ($m) => $m->where('numero', $this->mesFiltro));
        }

        if (!empty($this->search)) {
            $query->whereHas('empleado', function ($q) {
                $q->where('codigo_empleado', 'like', "%{$this->search}%")
                    ->orWhereHas('persona', function ($p) {
                        $p->where('nombres', 'like', "%{$this->search}%")
                            ->orWhere('apellidos', 'like', "%{$this->search}%");
                    });
            });
        }

        return view('livewire.horas-extra.index', [
            'detalles' => $query->orderByDesc('fecha')->paginate(15),
            'empleados' => Empleado::with('persona')->where('estado', true)->get(),
            'meses' => Mes::orderBy('numero')->get(),
            'tiposHoraExtra' => Tipo::categoria('tipo_hora_extra')->activos()->orderBy('orden')->get(),
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(HoraExtraDetalle::findOrFail($id));
        } else {
            $this->form->resetForm();
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());

        try {
            $fecha = Carbon::parse($this->form->fecha);
            $gestion = Gestion::firstOrCreate(
                ['anio' => $fecha->year],
                ['fecha_inicio' => $fecha->copy()->startOfYear(), 'fecha_fin' => $fecha->copy()->endOfYear(), 'estado' => true]
            );
            $mes = Mes::where('numero', $fecha->month)->first();
            $tipo = Tipo::findOrFail($this->form->id_tipo_hora_extra);

            $montoCalculado = round($this->form->horas * $this->form->valor_hora_base * ($tipo->valor_referencia ?? 1), 2);

            $data = array_merge($this->form->toArray(), [
                'id_gestion' => $gestion->id,
                'id_mes' => $mes?->id,
                'monto_calculado' => $montoCalculado,
            ]);

            if ($this->form->id) {
                HoraExtraDetalle::findOrFail($this->form->id)->update($data);
                $message = 'Hora extra actualizada correctamente.';
            } else {
                HoraExtraDetalle::create($data);
                $message = 'Hora extra registrada correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar la hora extra. Inténtalo nuevamente.');
        }
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->form->resetForm();
        $this->modalVisible = false;
    }

    public function delete($id)
    {
        try {
            $detalle = HoraExtraDetalle::findOrFail($id);
            $detalle->estado = !$detalle->estado;
            $detalle->save();

            $this->dispatch('toast', type: $detalle->estado ? 'success' : 'warning', message: 'Estado actualizado.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado.');
        }
    }
}
