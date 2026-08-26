<?php

namespace App\Livewire\Form;

use Livewire\Form;

class ContratoForm extends Form
{
    public $id = null;
    public $id_empleado = '';
    public $id_tipo_contrato = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $observaciones = '';
    public $estado = 1;

    public function rules()
    {
        return [
            'form.id_empleado' => 'required|exists:empleados,id',
            'form.id_tipo_contrato' => 'required|exists:tipos,id',
            'form.fecha_inicio' => 'required|date',
            'form.fecha_fin' => 'nullable|date|after:form.fecha_inicio',
            'form.observaciones' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'form.id_empleado.required' => 'Debe seleccionar un empleado.',
            'form.id_tipo_contrato.required' => 'Debe seleccionar un tipo de contrato.',
            'form.fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'form.fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->id_empleado = '';
        $this->id_tipo_contrato = '';
        $this->fecha_inicio = '';
        $this->fecha_fin = '';
        $this->observaciones = '';
        $this->estado = 1;
    }

    public function fillForm($contrato)
    {
        $this->id = $contrato->id;
        $this->id_empleado = $contrato->id_empleado;
        $this->id_tipo_contrato = $contrato->id_tipo_contrato;
        $this->fecha_inicio = $contrato->fecha_inicio?->format('Y-m-d');
        $this->fecha_fin = $contrato->fecha_fin?->format('Y-m-d');
        $this->observaciones = $contrato->observaciones;
        $this->estado = $contrato->estado;
    }
}
