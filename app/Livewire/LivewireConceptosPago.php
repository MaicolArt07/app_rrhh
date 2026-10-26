<?php

namespace App\Livewire;

use App\Livewire\Form\ConceptoPagoForm;
use App\Models\ConceptoPago;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireConceptosPago extends Component
{
    use WithPagination;

    public ConceptoPagoForm $form;

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
        $conceptos = null;

        if ($this->buscado) {
            $query = ConceptoPago::query();

            if (!empty($this->search)) {
                $query->where('nombre', 'like', "%{$this->search}%");
            }

            $conceptos = $query->orderBy('tipo_concepto')->orderBy('nombre')->paginate(10);
        }

        return view('livewire.conceptos-pago.index', [
            'conceptos' => $conceptos,
            'conceptoDetalle' => $this->viewingId
                ? ConceptoPago::withCount('planillaDetalleConceptos')->find($this->viewingId)
                : null,
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $this->form->fillForm(ConceptoPago::findOrFail($id));
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
                ConceptoPago::findOrFail($this->form->id)->update($this->form->toArray());
                $message = 'Concepto de pago actualizado correctamente.';
            } else {
                ConceptoPago::create($this->form->toArray());
                $message = 'Concepto de pago registrado correctamente.';
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el concepto de pago. Inténtalo nuevamente.');
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
            $concepto = ConceptoPago::findOrFail($id);
            $concepto->estado = !$concepto->estado;
            $concepto->save();

            $this->dispatch('toast', type: $concepto->estado ? 'success' : 'warning', message: 'Estado actualizado.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado.');
        }
    }
}
