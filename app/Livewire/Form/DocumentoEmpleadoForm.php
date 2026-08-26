<?php

namespace App\Livewire\Form;

use Livewire\Form;

class DocumentoEmpleadoForm extends Form
{
    public $id = null;
    public $id_empleado = '';
    public $id_tipo_documento_empleado = '';
    public $numero_documento = '';
    public $fecha_emision = '';
    public $fecha_vencimiento = '';
    public $estado = 1;

    public function rules()
    {
        return [
            'form.id_empleado' => 'required|exists:empleados,id',
            'form.id_tipo_documento_empleado' => 'required|exists:tipos,id',
            'form.numero_documento' => 'nullable|string|max:100',
            'form.fecha_emision' => 'nullable|date',
            'form.fecha_vencimiento' => 'nullable|date|after:form.fecha_emision',
        ];
    }

    public function messages()
    {
        return [
            'form.id_empleado.required' => 'Debe seleccionar un empleado.',
            'form.id_tipo_documento_empleado.required' => 'Debe seleccionar un tipo de documento.',
            'form.fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a la fecha de emisión.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->id_empleado = '';
        $this->id_tipo_documento_empleado = '';
        $this->numero_documento = '';
        $this->fecha_emision = '';
        $this->fecha_vencimiento = '';
        $this->estado = 1;
    }

    public function fillForm($documento)
    {
        $this->id = $documento->id;
        $this->id_empleado = $documento->id_empleado;
        $this->id_tipo_documento_empleado = $documento->id_tipo_documento_empleado;
        $this->numero_documento = $documento->numero_documento;
        $this->fecha_emision = $documento->fecha_emision?->format('Y-m-d');
        $this->fecha_vencimiento = $documento->fecha_vencimiento?->format('Y-m-d');
        $this->estado = $documento->estado;
    }
}
