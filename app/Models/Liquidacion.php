<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Liquidacion extends Model
{
    protected $table = 'liquidaciones';

    protected $fillable = [
        'id_empleado',
        'id_tipo_liquidacion',
        'fecha_liquidacion',
        'tiempo_laborado_anios',
        'salario_promedio',
        'monto_indemnizacion',
        'monto_vacaciones_pendientes',
        'monto_aguinaldo_proporcional',
        'monto_bono14_proporcional',
        'total_liquidacion',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha_liquidacion' => 'date',
        'tiempo_laborado_anios' => 'decimal:2',
        'salario_promedio' => 'decimal:2',
        'monto_indemnizacion' => 'decimal:2',
        'monto_vacaciones_pendientes' => 'decimal:2',
        'monto_aguinaldo_proporcional' => 'decimal:2',
        'monto_bono14_proporcional' => 'decimal:2',
        'total_liquidacion' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function tipoLiquidacion()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo_liquidacion');
    }
}
