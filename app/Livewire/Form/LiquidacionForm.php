<?php

namespace App\Livewire\Form;

use Livewire\Form;

class LiquidacionForm extends Form
{
    public $id = null;
    public $id_empleado = '';
    public $id_tipo_liquidacion = '';
    public $fecha_liquidacion = '';
    public $tiempo_laborado_anios = '';
    public $salario_promedio = '';
    public $monto_indemnizacion = 0;
    public $monto_vacaciones_pendientes = 0;
    public $monto_aguinaldo_proporcional = 0;
    public $monto_bono14_proporcional = 0;
    public $observaciones = '';

    public function rules()
    {
        return [
            'form.id_empleado' => 'required|exists:empleados,id',
            'form.id_tipo_liquidacion' => 'required|exists:tipos,id',
            'form.fecha_liquidacion' => 'required|date',
            'form.tiempo_laborado_anios' => 'required|numeric|min:0',
            'form.salario_promedio' => 'required|numeric|min:0',
            'form.monto_indemnizacion' => 'nullable|numeric|min:0',
            'form.monto_vacaciones_pendientes' => 'nullable|numeric|min:0',
            'form.monto_aguinaldo_proporcional' => 'nullable|numeric|min:0',
            'form.monto_bono14_proporcional' => 'nullable|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'form.id_empleado.required' => 'Debe seleccionar un empleado.',
            'form.id_tipo_liquidacion.required' => 'Debe seleccionar un tipo de liquidación.',
            'form.fecha_liquidacion.required' => 'La fecha de liquidación es obligatoria.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->id_empleado = '';
        $this->id_tipo_liquidacion = '';
        $this->fecha_liquidacion = now()->format('Y-m-d');
        $this->tiempo_laborado_anios = '';
        $this->salario_promedio = '';
        $this->monto_indemnizacion = 0;
        $this->monto_vacaciones_pendientes = 0;
        $this->monto_aguinaldo_proporcional = 0;
        $this->monto_bono14_proporcional = 0;
        $this->observaciones = '';
    }
}
