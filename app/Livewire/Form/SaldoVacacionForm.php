<?php

namespace App\Livewire\Form;

use Illuminate\Validation\Rule;
use Livewire\Form;

class SaldoVacacionForm extends Form
{
    public $id = null;
    public $id_empleado = '';
    public $id_gestion = '';
    public $dias_generados = 0;
    public $dias_tomados = 0;

    public function rules()
    {
        return [
            'form.id_empleado' => [
                'required',
                'exists:empleados,id',
                Rule::unique('saldos_vacaciones', 'id_empleado')
                    ->where(fn ($q) => $q->where('id_gestion', $this->id_gestion))
                    ->ignore($this->id),
            ],
            'form.id_gestion' => 'required|exists:gestiones,id',
            'form.dias_generados' => 'required|numeric|min:0',
            'form.dias_tomados' => 'required|numeric|min:0|lte:form.dias_generados',
        ];
    }

    public function messages()
    {
        return [
            'form.id_empleado.required' => 'Debe seleccionar un empleado.',
            'form.id_empleado.unique' => 'Ya existe un saldo para este empleado en esa gestión.',
            'form.id_gestion.required' => 'Debe seleccionar una gestión.',
            'form.dias_generados.required' => 'Los días generados son obligatorios.',
            'form.dias_tomados.lte' => 'Los días tomados no pueden superar los días generados.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->id_empleado = '';
        $this->id_gestion = '';
        $this->dias_generados = 0;
        $this->dias_tomados = 0;
    }

    public function fillForm($saldo)
    {
        $this->id = $saldo->id;
        $this->id_empleado = $saldo->id_empleado;
        $this->id_gestion = $saldo->id_gestion;
        $this->dias_generados = $saldo->dias_generados;
        $this->dias_tomados = $saldo->dias_tomados;
    }
}
