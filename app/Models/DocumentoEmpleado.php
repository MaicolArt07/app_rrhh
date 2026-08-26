<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DocumentoEmpleado extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $table = 'documentos_empleado';

    protected $fillable = [
        'id_empleado',
        'id_tipo_documento_empleado',
        'numero_documento',
        'fecha_emision',
        'fecha_vencimiento',
        'notificado_vencimiento',
        'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'notificado_vencimiento' => 'boolean',
        'estado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function tipoDocumentoEmpleado()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo_documento_empleado');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('archivo')->singleFile();
    }
}
