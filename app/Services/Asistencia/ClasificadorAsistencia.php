<?php

namespace App\Services\Asistencia;

use App\Models\TipoAsistencia;

/**
 * Clasifica un día de un empleado (docs/modulo-asistencia-correcciones.md §4.2 y §4.4).
 * No consulta la base de datos: recibe todo el contexto del día.
 */
class ClasificadorAsistencia
{
    public function __construct(private ?int $minutosDuplicado = null)
    {
        $this->minutosDuplicado ??= (int) config('asistencia.minutos_duplicado', 5);
    }

    /**
     * @param  array{
     *     evaluable: bool,
     *     tipo_manual?: ?string,
     *     feriado?: bool,
     *     permiso?: ?string,
     *     remoto?: bool,
     *     marcas?: list<string>,
     *     hora_limite: int,
     * }  $dia  permiso: 'vacaciones' | 'otro' | null; hora_limite en minutos desde 00:00.
     * @return array{codigo: string, marcas: list<string>, entrada: ?string, salida: ?string, minutos_atraso: int}
     */
    public function clasificar(array $dia): array
    {
        $marcas = $this->limpiar($dia['marcas'] ?? []);
        $entrada = $marcas[0] ?? null;
        $salida = count($marcas) > 1 ? end($marcas) : null;

        $resultado = fn (string $codigo, int $minutos = 0) => [
            'codigo' => $codigo,
            'marcas' => $marcas,
            'entrada' => $entrada,
            'salida' => $salida,
            'minutos_atraso' => $minutos,
        ];

        // 1. Fuera del rango ingreso–baja.
        if (! $dia['evaluable']) {
            return $resultado(TipoAsistencia::NO_APLICA);
        }

        // 2. Tipo asignado a mano por RR.HH.
        if (! empty($dia['tipo_manual'])) {
            return $resultado($dia['tipo_manual']);
        }

        // 3. Feriado.
        if (! empty($dia['feriado'])) {
            return $resultado(TipoAsistencia::FERIADO);
        }

        // 4. Permiso aprobado.
        if (! empty($dia['permiso'])) {
            return $resultado($dia['permiso'] === 'vacaciones' ? TipoAsistencia::VACACION : TipoAsistencia::BAJA_PERMISO);
        }

        // 5. Día remoto asignado.
        if (! empty($dia['remoto'])) {
            return $resultado(TipoAsistencia::REMOTO);
        }

        // 6. Marcaciones.
        if (! $marcas) {
            return $resultado(TipoAsistencia::SIN_MARCAR);
        }

        // Una sola marca: incompleto, sin minutos de atraso (sea entrada o salida).
        if (count($marcas) === 1) {
            return $resultado(TipoAsistencia::INCOMPLETO);
        }

        $minutos = self::minutos($entrada) - $dia['hora_limite'];

        return $minutos > 0
            ? $resultado(TipoAsistencia::ATRASO, $minutos)
            : $resultado(TipoAsistencia::PUNTUAL);
    }

    /**
     * Separa "08:0016:32" en ["08:00", "16:32"].
     *
     * @return list<string>
     */
    public static function separar(?string $crudo): array
    {
        preg_match_all('/([01]\d|2[0-3]):[0-5]\d/', (string) $crudo, $coincidencias);

        return $coincidencias[0];
    }

    /**
     * Ordena y une las marcas repetidas con pocos minutos de diferencia.
     *
     * @param  list<string>  $marcas
     * @return list<string>
     */
    public function limpiar(array $marcas): array
    {
        sort($marcas);
        $limpias = [];

        foreach ($marcas as $marca) {
            $anterior = end($limpias);

            if ($anterior === false || self::minutos($marca) - self::minutos($anterior) > $this->minutosDuplicado) {
                $limpias[] = $marca;
            }
        }

        return $limpias;
    }

    public static function minutos(string $hora): int
    {
        [$h, $m] = array_map('intval', explode(':', substr($hora, 0, 5)));

        return $h * 60 + $m;
    }
}
