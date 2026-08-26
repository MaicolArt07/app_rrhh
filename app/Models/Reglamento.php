<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Reglamento extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $table = 'reglamentos';

    protected $fillable = [
        'titulo',
        'contenido',
        'version',
        'vigente_desde',
        'estado',
    ];

    protected $casts = [
        'vigente_desde' => 'date',
        'estado' => 'boolean',
    ];

    public function lecturas()
    {
        return $this->hasMany(ReglamentoLectura::class, 'id_reglamento');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documento')->singleFile();
    }
}
