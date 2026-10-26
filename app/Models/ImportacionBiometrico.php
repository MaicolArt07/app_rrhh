<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportacionBiometrico extends Model
{
    protected $table = 'importaciones_biometrico';

    protected $fillable = [
        'nombre_archivo',
        'periodo_inicio',
        'periodo_fin',
        'id_usuario',
        'total_empleados',
        'total_no_vinculados',
        'estado',
    ];

    protected $casts = [
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_importacion');
    }
}
