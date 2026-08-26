<?php

namespace App\Livewire;

use App\Livewire\Form\FeriadoForm;
use App\Models\Feriado;
use App\Models\Gestion;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireFeriados extends Component
{
    use WithPagination;

    public FeriadoForm $form;

    public $gestionFiltro = '';
    public $modalVisible = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
        $this->gestionFiltro = Gestion::where('anio', now()->year)->value('id') ?? '';
    }

    public function updatingGestionFiltro()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Feriado::query()->with('gestion');

        if (!empty($this->gestionFiltro)) {
            $query->where('id_gestion', $this->gestionFiltro);
        }

        return view('livewire.feriados.index', [
            'feriados' => $query->orderBy('fecha')->paginate(15),
            'gestiones' => Gestion::orderByDesc('anio')->get(),
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(Feriado::findOrFail($id));
        } else {
            $this->form->resetForm();
            if ($this->gestionFiltro) {
                $this->form->id_gestion = $this->gestionFiltro;
            }
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());

        try {
            if ($this->form->id) {
                Feriado::findOrFail($this->form->id)->update($this->form->toArray());
                $message = 'Feriado actualizado correctamente.';
            } else {
                Feriado::create($this->form->toArray());
                $message = 'Feriado registrado correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el feriado. Inténtalo nuevamente.');
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
            $feriado = Feriado::findOrFail($id);
            $feriado->estado = !$feriado->estado;
            $feriado->save();

            $this->dispatch('toast', type: $feriado->estado ? 'success' : 'warning', message: 'Estado del feriado actualizado.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del feriado.');
        }
    }
}
