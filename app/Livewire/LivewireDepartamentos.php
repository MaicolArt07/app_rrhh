<?php

namespace App\Livewire;

use App\Livewire\Form\DepartamentoForm;
use App\Models\Departamento;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireDepartamentos extends Component
{
    use WithPagination;

    public DepartamentoForm $form;

    public $search = '';
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
        $query = Departamento::query();

        if (!empty($this->search)) {
            $query->where('nombre', 'like', "%{$this->search}%");
        }

        return view('livewire.departamentos.index', [
            'departamentos' => $query->withCount('cargos', 'empleados')->orderBy('nombre')->paginate(10),
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(Departamento::findOrFail($id));
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
                Departamento::findOrFail($this->form->id)->update($this->form->toArray());
                $message = 'Departamento actualizado correctamente.';
            } else {
                Departamento::create($this->form->toArray());
                $message = 'Departamento registrado correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el departamento. Inténtalo nuevamente.');
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
            $departamento = Departamento::findOrFail($id);
            $departamento->estado = !$departamento->estado;
            $departamento->save();

            $message = $departamento->estado
                ? 'El departamento fue activado correctamente.'
                : 'El departamento fue desactivado correctamente.';

            $this->dispatch('toast', type: $departamento->estado ? 'success' : 'warning', message: $message);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del departamento.');
        }
    }
}
