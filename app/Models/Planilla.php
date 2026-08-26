<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planilla extends Model
{
    protected $table = 'planillas';

    protected $fillable = [
        'id_gestion',
        'id_mes',
        'fecha_inicio',
        'fecha_fin',
        'id_tipo_planilla',
        'id_estado_planilla',
        'total_planilla',
        'id_generado_por',
        'id_aprobado_por',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'total_planilla' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }

    public function mes()
    {
        return $this->belongsTo(Mes::class, 'id_mes');
    }

    public function tipoPlanilla()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo_planilla');
    }

    public function estadoPlanilla()
    {
        return $this->belongsTo(Tipo::class, 'id_estado_planilla');
    }

    public function generadoPor()
    {
        return $this->belongsTo(User::class, 'id_generado_por');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'id_aprobado_por');
    }

    public function detalles()
    {
        return $this->hasMany(PlanillaDetalle::class, 'id_planilla');
    }
}
