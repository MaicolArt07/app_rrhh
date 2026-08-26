<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencias';

    protected $fillable = [
        'id_empleado',
        'id_gestion',
        'id_mes',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'hora_entrada_marcada_manual',
        'latitud',
        'longitud',
        'id_estado_asistencia',
        'horas_trabajadas',
        'horas_extra',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_entrada' => 'datetime',
        'hora_salida' => 'datetime',
        'hora_entrada_marcada_manual' => 'boolean',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
        'horas_trabajadas' => 'decimal:2',
        'horas_extra' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }

    public function mes()
    {
        return $this->belongsTo(Mes::class, 'id_mes');
    }

    public function estadoAsistencia()
    {
        return $this->belongsTo(Tipo::class, 'id_estado_asistencia');
    }

    public function horasExtraDetalle()
    {
        return $this->hasMany(HoraExtraDetalle::class, 'id_asistencia');
    }
}
