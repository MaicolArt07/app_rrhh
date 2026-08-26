<?php

namespace App\Livewire;

use App\Livewire\Form\EmpleadoForm;
use App\Models\Cargo;
use App\Models\Departamento;
use App\Models\Empleado;
use App\Models\Persona;
use App\Models\Tipo;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireEmpleados extends Component
{
    use WithPagination;

    public EmpleadoForm $form;

    public $search = '';
    public $departamentoFiltro = '';
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

    public function updatingDepartamentoFiltro()
    {
        $this->resetPage();
    }

    public function updatedFormIdCargo($value)
    {
        if ($value) {
            $cargo = Cargo::find($value);
            if ($cargo) {
                $this->form->id_departamento = $cargo->id_departamento;
                if (!$this->form->sueldo_actual) {
                    $this->form->sueldo_actual = $cargo->sueldo_base;
                }
            }
        }
    }

    public function render()
    {
        $query = Empleado::query()->with(['persona', 'cargo', 'departamento', 'estadoEmpleado']);

        if (!empty($this->departamentoFiltro)) {
            $query->where('id_departamento', $this->departamentoFiltro);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('codigo_empleado', 'like', "%{$this->search}%")
                  ->orWhereHas('persona', function ($p) {
                      $p->where('primer_nombre', 'like', "%{$this->search}%")
                        ->orWhere('primer_apellido', 'like', "%{$this->search}%")
                        ->orWhere('numero_documento', 'like', "%{$this->search}%");
                  });
            });
        }

        return view('livewire.empleados.index', [
            'empleados' => $query->orderByDesc('id')->paginate(10),
            'departamentos' => Departamento::where('estado', true)->orderBy('nombre')->get(),
            'personasDisponibles' => Persona::where('estado', true)
                ->where(function ($q) {
                    $q->whereDoesntHave('empleado')
                      ->when($this->form->id_persona, fn ($qq) => $qq->orWhere('id', $this->form->id_persona));
                })
                ->orderBy('primer_nombre')
                ->get(),
            'cargos' => Cargo::where('estado', true)->orderBy('nombre')->get(),
            'estadosEmpleado' => Tipo::categoria('estado_empleado')->activos()->orderBy('orden')->get(),
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(Empleado::findOrFail($id));
        } else {
            $this->form->resetForm();
            $this->form->codigo_empleado = $this->generarCodigo();
            $estadoActivo = Tipo::categoria('estado_empleado')->where('codigo', 'activo')->first();
            $this->form->id_estado_empleado = $estadoActivo?->id;
        }

        $this->modalVisible = true;
    }

    protected function generarCodigo(): string
    {
        $ultimo = Empleado::withTrashed()->orderByDesc('id')->first();
        $siguiente = $ultimo ? $ultimo->id + 1 : 1;

        return 'EMP-' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());

        try {
            if ($this->form->id) {
                Empleado::findOrFail($this->form->id)->update($this->form->toArray());
                $message = 'Empleado actualizado correctamente.';
            } else {
                Empleado::create($this->form->toArray());
                $message = 'Empleado registrado correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el empleado. Inténtalo nuevamente.');
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
            $empleado = Empleado::findOrFail($id);
            $empleado->estado = !$empleado->estado;
            $empleado->save();

            $message = $empleado->estado
                ? 'El empleado fue activado correctamente.'
                : 'El empleado fue desactivado correctamente.';

            $this->dispatch('toast', type: $empleado->estado ? 'success' : 'warning', message: $message);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del empleado.');
        }
    }
}
