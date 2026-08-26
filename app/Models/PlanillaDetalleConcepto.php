<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanillaDetalleConcepto extends Model
{
    protected $table = 'planilla_detalle_conceptos';

    protected $fillable = [
        'id_planilla_detalle',
        'id_concepto_pago',
        'monto',
        'observacion',
        'estado',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function planillaDetalle()
    {
        return $this->belongsTo(PlanillaDetalle::class, 'id_planilla_detalle');
    }

    public function conceptoPago()
    {
        return $this->belongsTo(ConceptoPago::class, 'id_concepto_pago');
    }
}
