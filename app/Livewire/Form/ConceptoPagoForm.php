<?php

namespace App\Livewire\Form;

use Livewire\Form;

class ConceptoPagoForm extends Form
{
    public $id = null;
    public $nombre = '';
    public $tipo_concepto = '';
    public $es_fijo = 0;
    public $valor_por_defecto = '';
    public $es_porcentaje = 0;
    public $estado = 1;

    public function rules()
    {
        return [
            'form.nombre' => 'required|string|max:150',
            'form.tipo_concepto' => 'required|in:ingreso,deduccion',
            'form.valor_por_defecto' => 'nullable|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'form.nombre.required' => 'El nombre es obligatorio.',
            'form.tipo_concepto.required' => 'Debe seleccionar si es ingreso o deducción.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->nombre = '';
        $this->tipo_concepto = '';
        $this->es_fijo = 0;
        $this->valor_por_defecto = '';
        $this->es_porcentaje = 0;
        $this->estado = 1;
    }

    public function fillForm($concepto)
    {
        $this->id = $concepto->id;
        $this->nombre = $concepto->nombre;
        $this->tipo_concepto = $concepto->tipo_concepto;
        $this->es_fijo = $concepto->es_fijo;
        $this->valor_por_defecto = $concepto->valor_por_defecto;
        $this->es_porcentaje = $concepto->es_porcentaje;
        $this->estado = $concepto->estado;
    }
}
