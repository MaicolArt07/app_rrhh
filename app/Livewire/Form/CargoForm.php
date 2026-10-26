<?php

namespace App\Livewire\Form;

use Livewire\Form;

class CargoForm extends Form
{
    public $id = null;
    public $id_area = '';
    public $nombre = '';
    public $sueldo_base = '';
    public $descripcion_funciones = '';
    public $estado = 1;

    public function rules()
    {
        return [
            'form.id_area' => 'required|exists:areas,id',
            'form.nombre' => 'required|string|max:150',
            'form.sueldo_base' => 'required|numeric|min:0',
            'form.descripcion_funciones' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'form.id_area.required' => 'Debe seleccionar un área.',
            'form.nombre.required' => 'El nombre es obligatorio.',
            'form.sueldo_base.required' => 'El sueldo base es obligatorio.',
            'form.sueldo_base.numeric' => 'El sueldo base debe ser numérico.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->id_area = '';
        $this->nombre = '';
        $this->sueldo_base = '';
        $this->descripcion_funciones = '';
        $this->estado = 1;
    }

    public function fillForm($cargo)
    {
        $this->id = $cargo->id;
        $this->id_area = $cargo->id_area;
        $this->nombre = $cargo->nombre;
        $this->sueldo_base = $cargo->sueldo_base;
        $this->descripcion_funciones = $cargo->descripcion_funciones;
        $this->estado = $cargo->estado;
    }
}
