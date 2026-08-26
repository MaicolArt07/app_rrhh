<?php

namespace App\Livewire\Form;

use Livewire\Form;

class HoraExtraDetalleForm extends Form
{
    public $id = null;
    public $id_empleado = '';
    public $fecha = '';
    public $id_tipo_hora_extra = '';
    public $horas = '';
    public $valor_hora_base = '';
    public $estado = 1;

    public function rules()
    {
        return [
            'form.id_empleado' => 'required|exists:empleados,id',
            'form.fecha' => 'required|date',
            'form.id_tipo_hora_extra' => 'required|exists:tipos,id',
            'form.horas' => 'required|numeric|min:0.01|max:24',
            'form.valor_hora_base' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'form.id_empleado.required' => 'Debe seleccionar un empleado.',
            'form.fecha.required' => 'La fecha es obligatoria.',
            'form.id_tipo_hora_extra.required' => 'Debe seleccionar un tipo de hora extra.',
            'form.horas.required' => 'Las horas son obligatorias.',
            'form.valor_hora_base.required' => 'El valor hora base es obligatorio.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->id_empleado = '';
        $this->fecha = now()->format('Y-m-d');
        $this->id_tipo_hora_extra = '';
        $this->horas = '';
        $this->valor_hora_base = '';
        $this->estado = 1;
    }

    public function fillForm($detalle)
    {
        $this->id = $detalle->id;
        $this->id_empleado = $detalle->id_empleado;
        $this->fecha = $detalle->fecha?->format('Y-m-d');
        $this->id_tipo_hora_extra = $detalle->id_tipo_hora_extra;
        $this->horas = $detalle->horas;
        $this->valor_hora_base = $detalle->valor_hora_base;
        $this->estado = $detalle->estado;
    }
}
