<?php

namespace App\Livewire;

use App\Livewire\Form\AreaForm;
use App\Models\Area;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireAreas extends Component
{
    use WithPagination;

    public AreaForm $form;

    public $search = '';
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
        $areas = null;

        if ($this->buscado) {
            $query = Area::query();

            if (!empty($this->search)) {
                $query->where('nombre', 'like', "%{$this->search}%");
            }

            $areas = $query->withCount('cargos', 'empleados')->orderBy('nombre')->paginate(10);
        }

        return view('livewire.areas.index', [
            'areas' => $areas,
            'areaDetalle' => $this->viewingId
                ? Area::withCount('cargos', 'empleados')->find($this->viewingId)
                : null,
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(Area::findOrFail($id));
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
                Area::findOrFail($this->form->id)->update($this->form->toArray());
                $message = 'Área actualizada correctamente.';
            } else {
                Area::create($this->form->toArray());
                $message = 'Área registrada correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el área. Inténtalo nuevamente.');
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
            $area = Area::findOrFail($id);
            $area->estado = !$area->estado;
            $area->save();

            $message = $area->estado
                ? 'El área fue activada correctamente.'
                : 'El área fue desactivada correctamente.';

            $this->dispatch('toast', type: $area->estado ? 'success' : 'warning', message: $message);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del área.');
        }
    }
}
