<?php

namespace App\Livewire;

use App\Livewire\Form\TipoForm;
use App\Models\Tipo;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireTipos extends Component
{
    use WithPagination;

    public TipoForm $form;

    public $search = '';
    public $categoriaFiltro = '';
    public $modalVisible = false;

    protected $paginationTheme = 'tailwind';

    protected $queryString = ['categoriaFiltro'];

    public function mount()
    {
        $this->form->resetForm();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoriaFiltro()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Tipo::query();

        if (!empty($this->categoriaFiltro)) {
            $query->where('categoria', $this->categoriaFiltro);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('codigo', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.tipos.index', [
            'tipos' => $query->orderBy('categoria')->orderBy('orden')->paginate(15),
            'categorias' => Tipo::query()->select('categoria')->distinct()->orderBy('categoria')->pluck('categoria'),
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(Tipo::findOrFail($id));
        } else {
            $this->form->resetForm();
            if ($this->categoriaFiltro) {
                $this->form->categoria = $this->categoriaFiltro;
            }
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());

        try {
            if ($this->form->id) {
                Tipo::findOrFail($this->form->id)->update($this->form->toArray());
                $message = 'Tipo actualizado correctamente.';
            } else {
                Tipo::create($this->form->toArray());
                $message = 'Tipo registrado correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el tipo. Inténtalo nuevamente.');
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
            $tipo = Tipo::findOrFail($id);
            $tipo->estado = !$tipo->estado;
            $tipo->save();

            $message = $tipo->estado
                ? 'El tipo fue activado correctamente.'
                : 'El tipo fue desactivado correctamente.';

            $this->dispatch('toast', type: $tipo->estado ? 'success' : 'warning', message: $message);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del tipo.');
        }
    }
}
