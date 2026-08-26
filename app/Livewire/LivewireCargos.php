<?php

namespace App\Livewire;

use App\Livewire\Form\CargoForm;
use App\Models\Cargo;
use App\Models\Departamento;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireCargos extends Component
{
    use WithPagination;

    public CargoForm $form;

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

    public function render()
    {
        $query = Cargo::query()->with('departamento');

        if (!empty($this->departamentoFiltro)) {
            $query->where('id_departamento', $this->departamentoFiltro);
        }

        if (!empty($this->search)) {
            $query->where('nombre', 'like', "%{$this->search}%");
        }

        return view('livewire.cargos.index', [
            'cargos' => $query->withCount('empleados')->orderBy('nombre')->paginate(10),
            'departamentos' => Departamento::where('estado', true)->orderBy('nombre')->get(),
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
