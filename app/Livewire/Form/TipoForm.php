<?php

namespace App\Livewire\Form;

use Illuminate\Validation\Rule;
use Livewire\Form;

class TipoForm extends Form
{
    public $id = null;
    public $categoria = '';
    public $codigo = '';
    public $nombre = '';
    public $valor_referencia = '';
    public $orden = 0;
    public $estado = 1;

    public function rules()
    {
        return [
            'form.categoria' => 'required|string|max:100',
            'form.codigo' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tipos', 'codigo')
                    ->where(fn ($query) => $query->where('categoria', $this->categoria))
                    ->ignore($this->id),
            ],
            'form.nombre' => 'required|string|max:150',
            'form.valor_referencia' => 'nullable|numeric',
            'form.orden' => 'nullable|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'form.categoria.required' => 'La categoría es obligatoria.',
            'form.codigo.required' => 'El código es obligatorio.',
            'form.nombre.required' => 'El nombre es obligatorio.',
            'form.valor_referencia.numeric' => 'El valor de referencia debe ser numérico.',
        ];
    }

    public function resetForm()
    {
        $this->id = null;
        $this->categoria = '';
        $this->codigo = '';
        $this->nombre = '';
        $this->valor_referencia = '';
        $this->orden = 0;
        $this->estado = 1;
    }

    public function fillForm($tipo)
    {
        $this->id = $tipo->id;
        $this->categoria = $tipo->categoria;
        $this->codigo = $tipo->codigo;
        $this->nombre = $tipo->nombre;
        $this->valor_referencia = $tipo->valor_referencia;
        $this->orden = $tipo->orden;
        $this->estado = $tipo->estado;
    }
}
