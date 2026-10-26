<?php

namespace App\Livewire;

use App\Livewire\Form\PersonaForm;
use App\Models\Persona;
use App\Models\Tipo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class LivewirePersonas extends Component
{
    use WithPagination;
    use WithFileUploads;

    public PersonaForm $form;

    public $search = '';
    public $modalVisible = false;
    public $foto;
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
        $personas = null;

        if ($this->buscado) {
            $query = Persona::query()->with('tipoDocumento');

            if (!empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('primer_nombre', 'like', "%{$this->search}%")
                      ->orWhere('primer_apellido', 'like', "%{$this->search}%")
                      ->orWhere('numero_documento', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                });
            }

            $personas = $query->orderByDesc('id')->paginate(10);
        }

        return view('livewire.personas.index', [
            'personas' => $personas,
            'personaDetalle' => $this->viewingId
                ? Persona::with(['tipoDocumento', 'empleado.cargo', 'empleado.area'])->find($this->viewingId)
                : null,
            'tiposDocumento' => Tipo::categoria('tipo_documento')->activos()->orderBy('orden')->get(),
        ]);
    }

    public function openModal($id = null)
    {
        $this->foto = null;

        if ($id) {
            $this->form->fillForm(Persona::findOrFail($id));
        } else {
            $this->form->resetForm();
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());
        $this->validate(['foto' => 'nullable|image|max:2048'], [], ['foto' => 'foto']);

        try {
            if ($this->form->id) {
                $persona = Persona::findOrFail($this->form->id);
                $persona->update($this->form->toArray());
                $message = 'Persona actualizada correctamente.';
            } else {
                $persona = Persona::create($this->form->toArray());
                $message = 'Persona registrada correctamente.';
            }

            if ($this->foto) {
                $persona->addMedia($this->foto->getRealPath())
                    ->usingFileName($this->foto->getClientOriginalName())
                    ->toMediaCollection('foto');
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar la persona. Inténtalo nuevamente.');
        }
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->form->resetForm();
        $this->foto = null;
        $this->modalVisible = false;
    }

    public function delete($id)
    {
        try {
            $persona = Persona::findOrFail($id);
            $persona->estado = !$persona->estado;
            $persona->save();

            $message = $persona->estado
                ? 'La persona fue activada correctamente.'
                : 'La persona fue desactivada correctamente.';

            $this->dispatch('toast', type: $persona->estado ? 'success' : 'warning', message: $message);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado de la persona.');
        }
    }
}
