<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoVacacion extends Model
{
    protected $table = 'saldos_vacaciones';

    protected $fillable = [
        'id_empleado',
        'id_gestion',
        'dias_generados',
        'dias_tomados',
        'dias_disponibles',
        'fecha_ultimo_calculo',
        'estado',
    ];

    protected $casts = [
        'dias_generados' => 'decimal:2',
        'dias_tomados' => 'decimal:2',
        'dias_disponibles' => 'decimal:2',
        'fecha_ultimo_calculo' => 'date',
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
}
