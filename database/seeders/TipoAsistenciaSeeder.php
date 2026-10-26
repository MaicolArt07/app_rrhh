<?php

namespace Database\Seeders;

use App\Models\TipoAsistencia;
use Illuminate\Database\Seeder;

/**
 * Catálogo de tipos de asistencia (docs/modulo-asistencia-correcciones.md §3).
 * La migración del módulo ya lo siembra; este seeder solo restaura los valores base.
 */
class TipoAsistenciaSeeder extends Seeder
{
    /** [codigo, nombre, color, justificado, sancionable, seleccionable en la matriz] */
    public const TIPOS = [
        ['VACACION', 'Vacación', '#FFFF00', true, false, true],
        ['FERIADO', 'Feriado', '#ADD8FF', true, false, true],
        ['REMOTO', 'Remoto', '#A6A6A6', true, false, true],
        ['TRABAJO_FUERA', 'Trabajo fuera de instalaciones', '#B292BE', true, false, true],
        ['BAJA_PERMISO', 'Baja / Permiso', '#C5E0B4', true, false, true],
        ['PUNTUAL', 'Asistencia normal', '#FFFFFF', false, false, false],
        ['ATRASO', 'Atraso', '#ED7D31', false, true, false],
        ['INCOMPLETO', 'Incompleto', '#FFCCCF', false, false, false],
        ['SIN_MARCAR', 'Sin marcar', '#FF0000', false, true, false],
        ['NO_APLICA', 'No aplica', '#E7E6E6', false, false, false],
    ];

    public function run(): void
    {
        foreach (self::TIPOS as $orden => [$codigo, $nombre, $color, $justificado, $sancionable, $seleccionable]) {
            TipoAsistencia::updateOrCreate(['codigo' => $codigo], [
                'nombre' => $nombre,
                'color_hex' => $color,
                'es_justificado' => $justificado,
                'es_sancionable' => $sancionable,
                'seleccionable_manual' => $seleccionable,
                'orden' => $orden,
                'estado' => true,
            ]);
        }
    }
}
