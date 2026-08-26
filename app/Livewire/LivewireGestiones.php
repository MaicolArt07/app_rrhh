<?php

namespace App\Livewire;

use App\Livewire\Form\GestionForm;
use App\Models\Gestion;
use App\Models\Mes;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireGestiones extends Component
{
    use WithPagination;

    public GestionForm $form;

    public $tab = 'gestiones';
    public $modalVisible = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
    }

    public function updatedFormAnio()
    {
        $this->form->actualizarFechasPorAnio();
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.gestiones.index', [
            'gestiones' => Gestion::orderByDesc('anio')->paginate(10, ['*'], 'gestionesPage'),
            'meses' => Mes::orderBy('numero')->get(),
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(Gestion::findOrFail($id));
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
                Gestion::findOrFail($this->form->id)->update($this->form->toArray());
                $message = 'Gestión actualizada correctamente.';
            } else {
                Gestion::create($this->form->toArray());
                $message = 'Gestión registrada correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar la gestión. Inténtalo nuevamente.');
        }
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->form->resetForm();
        $this->modalVisible = false;
    }

    public function toggleGestion($id)
    {
        try {
            $gestion = Gestion::findOrFail($id);
            $gestion->estado = !$gestion->estado;
            $gestion->save();

            $message = $gestion->estado
                ? 'La gestión fue reabierta correctamente.'
                : 'La gestión fue cerrada correctamente.';

            $this->dispatch('toast', type: $gestion->estado ? 'success' : 'warning', message: $message);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado de la gestión.');
        }
    }

    public function toggleMes($id)
    {
        try {
            $mes = Mes::findOrFail($id);
            $mes->estado = !$mes->estado;
            $mes->save();

            $this->dispatch('toast', type: $mes->estado ? 'success' : 'warning', message: 'Estado del mes actualizado.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del mes.');
        }
    }
}
