<?php

namespace App\Livewire;

use App\Livewire\Form\EmpleadoForm;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Persona;
use App\Models\Tipo;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireEmpleados extends Component
{
    use WithPagination;

    public EmpleadoForm $form;

    public $search = '';
    public $areaFiltro = '';
    public $tipoContratoFiltro = '';
    public $soloActivos = true;
    public $modalVisible = false;
    public bool $buscado = false;
    public ?int $viewingId = null;

    /** Empresa fija cuando se entra desde «Personal <empresa>». */
    public ?int $empresaId = null;

    protected $paginationTheme = 'tailwind';

    /** @param  Empresa|string|null  $empresa  código de la ruta /personal/{empresa} */
    public function mount($empresa = null)
    {
        if ($empresa !== null && ! $empresa instanceof Empresa) {
            $empresa = Empresa::where('codigo', $empresa)->firstOrFail();
        }

        $this->empresaId = $empresa?->id;
        $this->form->resetForm();
    }

    public function updatingTipoContratoFiltro()
    {
        $this->resetPage();
    }

    public function updatingSoloActivos()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingAreaFiltro()
    {
        $this->resetPage();
    }

    public function buscar()
    {
        $this->resetPage();
        $this->buscado = true;
    }

    public function limpiarFiltros()
    {
        $this->reset('search', 'areaFiltro', 'tipoContratoFiltro', 'soloActivos', 'empresaId');
        $this->buscado = false;
    }

    public function updatingEmpresaId()
    {
        $this->resetPage();
    }

    public function verDetalle($id)
    {
        $this->viewingId = $id;
    }

    public function cerrarDetalle()
    {
        $this->viewingId = null;
    }

    public function editarDesdeDetalle($id)
    {
        $this->viewingId = null;
        $this->openModal($id);
    }

    public function updatedFormIdCargo($value)
    {
        if ($value) {
            $cargo = Cargo::find($value);
            if ($cargo) {
                $this->form->id_area = $cargo->id_area;
                if (!$this->form->sueldo_actual) {
                    $this->form->sueldo_actual = $cargo->sueldo_base;
                }
            }
        }
    }

    public function render()
    {
        $empleados = null;

        if ($this->buscado) {
            $query = Empleado::query()->with(['persona', 'cargo', 'area', 'empresa', 'estadoEmpleado', 'contratoVigente.tipoContrato']);

            if ($this->empresaId) {
                $query->where('id_empresa', $this->empresaId);
            }

            if (!empty($this->areaFiltro)) {
                $query->where('id_area', $this->areaFiltro);
            }

            if ($this->soloActivos) {
                $query->where('estado', true);
            }

            if (!empty($this->tipoContratoFiltro)) {
                $query->whereHas('contratoVigente', fn ($c) => $c->where('id_tipo_contrato', $this->tipoContratoFiltro));
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

            $empleados = $query->orderByDesc('id')->paginate(10);
        }

        return view('livewire.empleados.index', [
            'empleados' => $empleados,
            'empleadoDetalle' => $this->viewingId
                ? Empleado::with(['persona.tipoDocumento', 'cargo', 'area', 'empresa', 'estadoEmpleado', 'contratoVigente.tipoContrato'])->find($this->viewingId)
                : null,
            'areas' => Area::where('estado', true)->orderBy('nombre')->get(),
            'empresas' => Empresa::where('estado', true)->orderBy('nombre')->get(),
            'empresaActual' => $this->empresaId ? Empresa::find($this->empresaId) : null,
            'tiposContrato' => Tipo::categoria('tipo_contrato')->activos()->orderBy('orden')->get(),
            'resumen' => $this->resumenPorContrato(),
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

    /**
     * Empleados activos por tipo del contrato vigente (en la empresa elegida, si hay).
     *
     * @return array{total: int, tipos: array<string, int>}
     */
    protected function resumenPorContrato(): array
    {
        $empleados = Empleado::with('contratoVigente.tipoContrato')
            ->where('estado', true)
            ->when($this->empresaId, fn ($q) => $q->where('id_empresa', $this->empresaId))
            ->get();

        return [
            'total' => $empleados->count(),
            'tipos' => $empleados->groupBy(fn ($e) => $e->contratoVigente?->tipoContrato?->nombre ?? 'Sin contrato vigente')
                ->map->count()->sortKeys()->all(),
        ];
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(Empleado::findOrFail($id));
        } else {
            $this->form->resetForm();
            $this->form->id_empresa = $this->empresaId ?? '';
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
