<?php

namespace Database\Seeders;

use App\Models\Feriado;
use App\Models\Gestion;
use Illuminate\Database\Seeder;

/**
 * Feriados de Bolivia 2026 que la macro de asistencia tenía escritos en el código
 * (función FeriadosConocidos). El 21 de junio (Año Nuevo Andino) cae domingo y su
 * traslado no estaba definido, así que no se incluye.
 */
class FeriadosBolivia2026Seeder extends Seeder
{
    public const FERIADOS = [
        '2026-01-01' => 'Año Nuevo',
        '2026-01-02' => 'Feriado ampliado de Año Nuevo',
        '2026-02-16' => 'Carnaval',
        '2026-02-17' => 'Carnaval',
        '2026-04-03' => 'Viernes Santo',
        '2026-05-01' => 'Día del Trabajo',
        '2026-06-04' => 'Corpus Christi',
        '2026-08-06' => 'Día de la Independencia',
        '2026-08-07' => 'Feriado excepcional',
        '2026-09-24' => 'Aniversario de Santa Cruz',
        '2026-11-02' => 'Todos los Santos',
        '2026-12-25' => 'Navidad',
    ];

    public function run(): void
    {
        $gestion = Gestion::firstOrCreate(
            ['anio' => 2026],
            ['fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-12-31', 'estado' => true]
        );

        foreach (self::FERIADOS as $fecha => $descripcion) {
            Feriado::firstOrCreate(
                ['fecha' => $fecha],
                ['id_gestion' => $gestion->id, 'descripcion' => $descripcion, 'aplica_todos' => true, 'estado' => true]
            );
        }
    }
}
