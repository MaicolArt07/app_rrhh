<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptoPago extends Model
{
    protected $table = 'conceptos_pago';

    protected $fillable = [
        'nombre',
        'tipo_concepto',
        'es_fijo',
        'valor_por_defecto',
        'es_porcentaje',
        'estado',
    ];

    protected $casts = [
        'es_fijo' => 'boolean',
        'valor_por_defecto' => 'decimal:2',
        'es_porcentaje' => 'boolean',
        'estado' => 'boolean',
    ];

    public function planillaDetalleConceptos()
    {
        return $this->hasMany(PlanillaDetalleConcepto::class, 'id_concepto_pago');
    }
}
