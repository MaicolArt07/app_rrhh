<?php

namespace App\Livewire\Form;

use Livewire\Form;

class TurnoForm extends Form
{
    public $id = null;
    public $nombre = '';
    public $hora_entrada = '';
    public $hora_salida = '';
    public $tolerancia_minutos = 0;
    public $estado = 1;

    public function rules()
    {
        return [
            'form.nombre' => 'required|string|max:100',
            'form.hora_entrada' => 'required',
            'form.hora_salida' => 'required',
            'form.tolerancia_minutos' => 'nullable|integer|min:0|max:120',
        ];
    }

    public function messages()
    {
        return [
            'form.nombre.required' => 'El nombre es obligatorio.',
            'form.hora_entrada.required' => 'La hora de entrada es obligatoria.',
            'form.hora_salida.required' => 'La hora de salida es obligatoria.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->nombre = '';
        $this->hora_entrada = '';
        $this->hora_salida = '';
        $this->tolerancia_minutos = 0;
        $this->estado = 1;
    }

    public function fillForm($turno)
    {
        $this->id = $turno->id;
        $this->nombre = $turno->nombre;
        $this->hora_entrada = $turno->hora_entrada;
        $this->hora_salida = $turno->hora_salida;
        $this->tolerancia_minutos = $turno->tolerancia_minutos;
        $this->estado = $turno->estado;
    }
}
