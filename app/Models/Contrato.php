<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Contrato extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'contratos';

    protected $fillable = [
        'id_empleado',
        'id_tipo_contrato',
        'fecha_inicio',
        'fecha_fin',
        'notificado_vencimiento',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'notificado_vencimiento' => 'boolean',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function tipoContrato()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo_contrato');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documento')->singleFile();
    }
}
