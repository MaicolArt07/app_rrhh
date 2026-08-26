<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnticipoPago extends Model
{
    protected $table = 'anticipo_pagos';

    protected $fillable = [
        'id_anticipo',
        'id_planilla_detalle',
        'monto_pagado',
        'fecha_pago',
        'estado',
    ];

    protected $casts = [
        'monto_pagado' => 'decimal:2',
        'fecha_pago' => 'date',
        'estado' => 'boolean',
    ];

    public function anticipo()
    {
        return $this->belongsTo(AnticipoPrestamo::class, 'id_anticipo');
    }

    public function planillaDetalle()
    {
        return $this->belongsTo(PlanillaDetalle::class, 'id_planilla_detalle');
    }
}
