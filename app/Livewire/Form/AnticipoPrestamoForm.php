<?php

namespace App\Livewire\Form;

use Livewire\Form;

class AnticipoPrestamoForm extends Form
{
    public $id = null;
    public $id_empleado = '';
    public $id_tipo_anticipo = '';
    public $monto_total = '';
    public $numero_cuotas = 1;
    public $fecha_otorgado = '';
    public $observaciones = '';

    public function rules()
    {
        return [
            'form.id_empleado' => 'required|exists:empleados,id',
            'form.id_tipo_anticipo' => 'required|exists:tipos,id',
            'form.monto_total' => 'required|numeric|min:0.01',
            'form.numero_cuotas' => 'required|integer|min:1|max:48',
            'form.fecha_otorgado' => 'required|date',
            'form.observaciones' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'form.id_empleado.required' => 'Debe seleccionar un empleado.',
            'form.id_tipo_anticipo.required' => 'Debe seleccionar un tipo de anticipo.',
            'form.monto_total.required' => 'El monto total es obligatorio.',
            'form.numero_cuotas.required' => 'El número de cuotas es obligatorio.',
            'form.fecha_otorgado.required' => 'La fecha de otorgamiento es obligatoria.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->id_empleado = '';
        $this->id_tipo_anticipo = '';
        $this->monto_total = '';
        $this->numero_cuotas = 1;
        $this->fecha_otorgado = now()->format('Y-m-d');
        $this->observaciones = '';
    }
}
