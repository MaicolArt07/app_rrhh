<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Person;
use App\Livewire\Form\PersonForm;
use App\Models\Country;

class LivewirePerson extends Component
{
    use WithPagination;

    public PersonForm $form;

    public $search = '';
    public $modalVisible = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
    }

    public function render()
    {
        $query = Person::query();
        $countries = Country::where('state', 1)->get();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('lastname', 'like', "%{$this->search}%")
                  ->orWhere('ci', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.person.index', [
            'persons' => $query->paginate(10),
            'countries' => $countries,
        ]);
    }

    public function openModal($id = null)
    {
        if ($id) {
            $person = Person::findOrFail($id);
            $this->form->fillForm($person);
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
            $person = Person::findOrFail($this->form->id);
            $person->update($this->form->toArray());
              $message = 'Persona actualizada correctamente.';

        } else {
            Person::create($this->form->toArray());
            $message = 'Persona registrada correctamente.';
        }

        // session()->flash('message', 'Persona guardada correctamente.');
        $this->dispatch('toast', type: 'success', message: $message);

        $this->modalVisible = false;
        } catch (\Throwable $e) {

        $this->dispatch(
            'toast',
            type: 'error',
            message: 'No se pudo guardar la persona. Inténtalo nuevamente.'
        );

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
        $person = Person::findOrFail($id);
        $person->state = $person->state ? 0 : 1; // Alterna entre activo/inactivo
        $person->save();
         $message = $person->state
            ? 'La persona fue activada correctamente.'
            : 'La persona fue desactivada correctamente.';

        // 🔹 Toast UX con tipo dinámico
        $this->dispatch(
            'toast',
            type: $person->state ? 'success' : 'warning',
            message: $message
        );

        // $msg = $person->state ? 'Persona activada correctamente.' : 'Persona desactivada correctamente.';
        // session()->flash('message', $msg);
        } catch (\Throwable $e) {

        $this->dispatch(
            'toast',
            type: 'error',
            message: 'No se pudo actualizar el estado de la persona.'
        );

    }
    }

}
