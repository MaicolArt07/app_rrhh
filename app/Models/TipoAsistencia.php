<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TipoAsistencia extends Model
{
    use LogsActivity;

    public const VACACION = 'VACACION';
    public const FERIADO = 'FERIADO';
    public const REMOTO = 'REMOTO';
    public const TRABAJO_FUERA = 'TRABAJO_FUERA';
    public const BAJA_PERMISO = 'BAJA_PERMISO';
    public const PUNTUAL = 'PUNTUAL';
    public const ATRASO = 'ATRASO';
    public const INCOMPLETO = 'INCOMPLETO';
    public const SIN_MARCAR = 'SIN_MARCAR';
    public const NO_APLICA = 'NO_APLICA';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->dontSubmitEmptyLogs();
    }

    protected $table = 'tipos_asistencia';

    protected $fillable = [
        'codigo',
        'nombre',
        'color_hex',
        'es_justificado',
        'es_sancionable',
        'seleccionable_manual',
        'orden',
        'estado',
    ];

    protected $casts = [
        'es_justificado' => 'boolean',
        'es_sancionable' => 'boolean',
        'seleccionable_manual' => 'boolean',
        'estado' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('tipos_asistencia'));
    }

    /**
     * Catálogo completo indexado por código.
     *
     * @return \Illuminate\Support\Collection<string, TipoAsistencia>
     */
    public static function porCodigo()
    {
        return Cache::rememberForever('tipos_asistencia', fn () => static::orderBy('orden')->get()->keyBy('codigo'));
    }

    public static function idDe(string $codigo): int
    {
        return static::porCodigo()[$codigo]->id;
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_tipo_asistencia');
    }

    /** Color de texto legible sobre el color de fondo del tipo. */
    public function colorTexto(): string
    {
        [$r, $g, $b] = sscanf($this->color_hex, '#%02x%02x%02x');

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) < 140 ? '#FFFFFF' : '#1F2937';
    }
}
