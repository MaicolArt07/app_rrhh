<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PlanillaDetalle extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $table = 'planilla_detalles';

    protected $fillable = [
        'id_planilla',
        'id_empleado',
        'id_cargo',
        'sueldo_base',
        'dias_trabajados',
        'horas_extra',
        'total_ingresos',
        'total_deducciones',
        'sueldo_liquido',
        'total_pagado',
        'estado_pago',
        'fecha_pago',
        'metodo_pago',
        'estado',
    ];

    protected $casts = [
        'sueldo_base' => 'decimal:2',
        'dias_trabajados' => 'decimal:2',
        'horas_extra' => 'decimal:2',
        'total_ingresos' => 'decimal:2',
        'total_deducciones' => 'decimal:2',
        'sueldo_liquido' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'fecha_pago' => 'date',
        'estado' => 'boolean',
    ];

    public function planilla()
    {
        return $this->belongsTo(Planilla::class, 'id_planilla');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo');
    }

    public function conceptos()
    {
        return $this->hasMany(PlanillaDetalleConcepto::class, 'id_planilla_detalle');
    }

    public function horasExtraDetalle()
    {
        return $this->hasMany(HoraExtraDetalle::class, 'id_planilla_detalle');
    }

    public function anticipoPagos()
    {
        return $this->hasMany(AnticipoPago::class, 'id_planilla_detalle');
    }
}
