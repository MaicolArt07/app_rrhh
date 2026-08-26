<?php

namespace App\Livewire;

use App\Livewire\Form\ContratoForm;
use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Tipo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class LivewireContratos extends Component
{
    use WithPagination;
    use WithFileUploads;

    public ContratoForm $form;

    public $search = '';
    public $modalVisible = false;
    public $documento;

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
        $query = Contrato::query()->with(['empleado.persona', 'tipoContrato']);

        if (!empty($this->search)) {
            $query->whereHas('empleado', function ($q) {
                $q->where('codigo_empleado', 'like', "%{$this->search}%")
                  ->orWhereHas('persona', function ($p) {
                      $p->where('primer_nombre', 'like', "%{$this->search}%")
                        ->orWhere('primer_apellido', 'like', "%{$this->search}%");
                  });
            });
        }

        return view('livewire.contratos.index', [
            'contratos' => $query->orderByDesc('fecha_inicio')->paginate(10),
            'empleados' => Empleado::with('persona')->where('estado', true)->get(),
            'tiposContrato' => Tipo::categoria('tipo_contrato')->activos()->orderBy('orden')->get(),
        ]);
    }

    public function openModal($id = null)
    {
        $this->documento = null;

        if ($id) {
            $this->form->fillForm(Contrato::findOrFail($id));
        } else {
            $this->form->resetForm();
        }

        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());
        $this->validate(['documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'], [], ['documento' => 'documento']);

        try {
            if ($this->form->id) {
                $contrato = Contrato::findOrFail($this->form->id);
                $contrato->update($this->form->toArray());
                $message = 'Contrato actualizado correctamente.';
            } else {
                $contrato = Contrato::create($this->form->toArray());
                $message = 'Contrato registrado correctamente.';
            }

            if ($this->documento) {
                $contrato->addMedia($this->documento->getRealPath())
                    ->usingFileName($this->documento->getClientOriginalName())
                    ->toMediaCollection('documento');
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el contrato. Inténtalo nuevamente.');
        }
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->form->resetForm();
        $this->documento = null;
        $this->modalVisible = false;
    }

    public function delete($id)
    {
        try {
            $contrato = Contrato::findOrFail($id);
            $contrato->estado = !$contrato->estado;
            $contrato->save();

            $message = $contrato->estado
                ? 'El contrato fue activado correctamente.'
                : 'El contrato fue desactivado correctamente.';

            $this->dispatch('toast', type: $contrato->estado ? 'success' : 'warning', message: $message);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del contrato.');
        }
    }
}
