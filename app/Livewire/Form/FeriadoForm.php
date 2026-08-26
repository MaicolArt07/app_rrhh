<?php

namespace App\Livewire\Form;

use Livewire\Form;

class FeriadoForm extends Form
{
    public $id = null;
    public $id_gestion = '';
    public $fecha = '';
    public $descripcion = '';
    public $aplica_todos = 1;
    public $estado = 1;

    public function rules()
    {
        return [
            'form.id_gestion' => 'required|exists:gestiones,id',
            'form.fecha' => 'required|date',
            'form.descripcion' => 'required|string|max:150',
        ];
    }

    public function messages()
    {
        return [
            'form.id_gestion.required' => 'Debe seleccionar una gestión.',
            'form.fecha.required' => 'La fecha es obligatoria.',
            'form.descripcion.required' => 'La descripción es obligatoria.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->id_gestion = '';
        $this->fecha = '';
        $this->descripcion = '';
        $this->aplica_todos = 1;
        $this->estado = 1;
    }

    public function fillForm($feriado)
    {
        $this->id = $feriado->id;
        $this->id_gestion = $feriado->id_gestion;
        $this->fecha = $feriado->fecha?->format('Y-m-d');
        $this->descripcion = $feriado->descripcion;
        $this->aplica_todos = $feriado->aplica_todos;
        $this->estado = $feriado->estado;
    }
}
