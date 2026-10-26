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

    public string $search = '';
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

    public function buscar()
    {
        $this->resetPage();
        $this->buscado = true;
    }

    public function limpiarFiltros()
    {
        $this->reset('search');
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
        $turnos = null;

        if ($this->buscado) {
            $query = Turno::query();

            if (!empty($this->search)) {
                $query->where('nombre', 'like', "%{$this->search}%");
            }

            $turnos = $query->orderBy('nombre')->paginate(10);
        }

        return view('livewire.turnos.index', [
            'turnos' => $turnos,
            'turnoDetalle' => $this->viewingId ? Turno::find($this->viewingId) : null,
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
