<?php

namespace App\Models;

use App\Services\Asistencia\AsistenciaService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Feriado extends Model
{
    protected $table = 'feriados';

    protected $fillable = [
        'id_gestion',
        'fecha',
        'descripcion',
        'aplica_todos',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'aplica_todos' => 'boolean',
        'estado' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Un feriado nuevo, modificado o desactivado reclasifica la asistencia de su mes.
        $recalcular = function (Feriado $feriado) {
            $fechas = array_filter([$feriado->fecha, $feriado->getOriginal('fecha')]);
            foreach ($fechas as $fecha) {
                app(AsistenciaService::class)->recalcular(Carbon::parse($fecha), Carbon::parse($fecha));
            }
        };

        static::saved($recalcular);
        static::deleted($recalcular);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion');
    }
}
