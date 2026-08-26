<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoraExtraDetalle extends Model
{
    protected $table = 'horas_extra_detalle';

    protected $fillable = [
        'id_empleado',
        'id_asistencia',
        'id_gestion',
        'id_mes',
        'fecha',
        'id_tipo_hora_extra',
        'horas',
        'valor_hora_base',
        'monto_calculado',
        'id_planilla_detalle',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'horas' => 'decimal:2',
        'valor_hora_base' => 'decimal:2',
        'monto_calculado' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function asistencia()
    {
        return $this->belongsTo(Asistencia::class, 'id_asistencia');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }

    public function mes()
    {
        return $this->belongsTo(Mes::class, 'id_mes');
    }

    public function tipoHoraExtra()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo_hora_extra');
    }

    public function planillaDetalle()
    {
        return $this->belongsTo(PlanillaDetalle::class, 'id_planilla_detalle');
    }
}
