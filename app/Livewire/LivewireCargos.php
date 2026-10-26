<?php

namespace App\Livewire;

use App\Livewire\Form\CargoForm;
use App\Models\Area;
use App\Models\Cargo;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireCargos extends Component
{
    use WithPagination;

    public CargoForm $form;

    public $search = '';
    public $areaFiltro = '';
    public $modalVisible = false;
    public bool $buscado = false;
    public ?int $viewingId = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
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
        $this->reset('search', 'areaFiltro');
        $this->buscado = false;
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

    public function render()
    {
        $cargos = null;

        if ($this->buscado) {
            $query = Cargo::query()->with('area');

            if (!empty($this->areaFiltro)) {
                $query->where('id_area', $this->areaFiltro);
            }

            if (!empty($this->search)) {
                $query->where('nombre', 'like', "%{$this->search}%");
            }

            $cargos = $query->withCount('empleados')->orderBy('nombre')->paginate(10);
        }

        return view('livewire.cargos.index', [
            'cargos' => $cargos,
            'areas' => Area::where('estado', true)->orderBy('nombre')->get(),
            'cargoDetalle' => $this->viewingId
                ? Cargo::with('area')->withCount('empleados')->find($this->viewingId)
                : null,
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(Cargo::findOrFail($id));
        } else {
            $this->form->resetForm();
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());

        try {
            if ($this->form->id) {
                Cargo::findOrFail($this->form->id)->update($this->form->toArray());
                $message = 'Cargo actualizado correctamente.';
            } else {
                Cargo::create($this->form->toArray());
                $message = 'Cargo registrado correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el cargo. Inténtalo nuevamente.');
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
            $cargo = Cargo::findOrFail($id);
            $cargo->estado = !$cargo->estado;
            $cargo->save();

            $message = $cargo->estado
                ? 'El cargo fue activado correctamente.'
                : 'El cargo fue desactivado correctamente.';

            $this->dispatch('toast', type: $cargo->estado ? 'success' : 'warning', message: $message);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del cargo.');
        }
    }
}
