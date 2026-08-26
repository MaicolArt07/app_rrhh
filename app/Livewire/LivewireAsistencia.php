<?php

namespace App\Livewire;

use App\Livewire\Form\AsistenciaForm;
use App\Models\Asistencia;
use App\Models\Empleado;
use App\Models\Gestion;
use App\Models\Mes;
use App\Models\Tipo;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireAsistencia extends Component
{
    use WithPagination;

    public AsistenciaForm $form;

    public $search = '';
    public $mesFiltro = '';
    public $modalVisible = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
        $this->mesFiltro = (int) now()->month;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMesFiltro()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Asistencia::query()->with(['empleado.persona', 'estadoAsistencia']);

        if (!empty($this->mesFiltro)) {
            $query->whereHas('mes', fn ($m) => $m->where('numero', $this->mesFiltro));
        }

        if (!empty($this->search)) {
            $query->whereHas('empleado', function ($q) {
                $q->where('codigo_empleado', 'like', "%{$this->search}%")
                  ->orWhereHas('persona', function ($p) {
                      $p->where('primer_nombre', 'like', "%{$this->search}%")
                        ->orWhere('primer_apellido', 'like', "%{$this->search}%");
                  });
            });
        }

        return view('livewire.asistencia.index', [
            'asistencias' => $query->orderByDesc('fecha')->paginate(15),
            'empleados' => Empleado::with('persona')->where('estado', true)->get(),
            'meses' => Mes::orderBy('numero')->get(),
            'estadosAsistencia' => Tipo::categoria('estado_asistencia')->activos()->orderBy('orden')->get(),
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(Asistencia::findOrFail($id));
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

            $horasTrabajadas = 0;
            $horasExtra = 0;
            if ($this->form->hora_entrada && $this->form->hora_salida) {
                $entrada = Carbon::parse($this->form->hora_entrada);
                $salida = Carbon::parse($this->form->hora_salida);
                $horasTrabajadas = round($entrada->diffInMinutes($salida) / 60, 2);
                $horasExtra = $horasTrabajadas > 8 ? round($horasTrabajadas - 8, 2) : 0;
            }

            $data = array_merge($this->form->toArray(), [
                'id_gestion' => $gestion->id,
                'id_mes' => $mes?->id,
                'hora_entrada' => $this->form->hora_entrada ? "{$this->form->fecha} {$this->form->hora_entrada}" : null,
                'hora_salida' => $this->form->hora_salida ? "{$this->form->fecha} {$this->form->hora_salida}" : null,
                'horas_trabajadas' => $horasTrabajadas,
                'horas_extra' => $horasExtra,
            ]);

            if ($this->form->id) {
                Asistencia::findOrFail($this->form->id)->update($data);
                $message = 'Asistencia actualizada correctamente.';
            } else {
                Asistencia::create($data);
                $message = 'Asistencia registrada correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar la asistencia. Verifica que no exista ya un registro para ese empleado en esa fecha.');
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
            $asistencia = Asistencia::findOrFail($id);
            $asistencia->estado = !$asistencia->estado;
            $asistencia->save();

            $this->dispatch('toast', type: $asistencia->estado ? 'success' : 'warning', message: 'Estado de la asistencia actualizado.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado de la asistencia.');
        }
    }
}
