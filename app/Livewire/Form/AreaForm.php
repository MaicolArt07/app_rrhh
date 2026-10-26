<?php

namespace App\Livewire\Form;

use Livewire\Form;

class AreaForm extends Form
{
    public $id = null;
    public $nombre = '';
    public $descripcion = '';
    public $estado = 1;

    public function rules()
    {
        return [
            'form.nombre' => 'required|string|max:150',
            'form.descripcion' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'form.nombre.required' => 'El nombre es obligatorio.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->nombre = '';
        $this->descripcion = '';
        $this->estado = 1;
    }

    public function fillForm($area)
    {
        $this->id = $area->id;
        $this->nombre = $area->nombre;
        $this->descripcion = $area->descripcion;
        $this->estado = $area->estado;
    }
}
