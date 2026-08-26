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

    public $modalVisible = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
    }

    public function render()
    {
        return view('livewire.conceptos-pago.index', [
            'conceptos' => ConceptoPago::orderBy('tipo_concepto')->orderBy('nombre')->paginate(10),
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
