<?php

namespace App\Livewire;

use App\Livewire\Form\TurnoForm;
use App\Models\Turno;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireTurnos extends Component
{
    use WithPagination;

    public TurnoForm $form;

    public $modalVisible = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
    }

    public function render()
    {
        return view('livewire.turnos.index', [
            'turnos' => Turno::orderBy('nombre')->paginate(10),
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(Turno::findOrFail($id));
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
                Turno::findOrFail($this->form->id)->update($this->form->toArray());
                $message = 'Turno actualizado correctamente.';
            } else {
                Turno::create($this->form->toArray());
                $message = 'Turno registrado correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el turno. Inténtalo nuevamente.');
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
            $turno = Turno::findOrFail($id);
            $turno->estado = !$turno->estado;
            $turno->save();

            $this->dispatch('toast', type: $turno->estado ? 'success' : 'warning', message: 'Estado del turno actualizado.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del turno.');
        }
    }
}
