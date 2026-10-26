<?php

namespace App\Exports\Asistencia;

use App\Models\TipoAsistencia;
use Illuminate\Support\Collection;

class IncompletosSheet extends HojaAsistencia
{
    public function __construct(private Collection $filas)
    {
    }

    public function title(): string
    {
        return 'Sin marcar e incompletos';
    }

    public function array(): array
    {
        $datos = [['ID', 'Nombre', 'Fecha', 'Día', 'Tipo', 'Marca registrada', '¿Suma a la multa?']];

        foreach ($this->filas as $fila) {
            $dias = $fila['dias']
                ->filter(fn ($a) => in_array($a->tipoAsistencia?->codigo, [TipoAsistencia::SIN_MARCAR, TipoAsistencia::INCOMPLETO]))
                ->sortBy('fecha');

            foreach ($dias as $dia) {
                $datos[] = [
                    $fila['empleado']->codigo_biometrico,
                    $fila['empleado']->persona?->nombreCompleto(),
                    $dia->fecha->format('d/m/Y'),
                    ucfirst($dia->fecha->translatedFormat('l')),
                    $dia->tipoAsistencia->nombre,
                    implode(' ', $dia->marcaciones ?? []) ?: '—',
                    $dia->es_sancionable ? 'Sí' : 'No',
                ];

                $this->pintar('E'.count($datos), $dia->tipoAsistencia->color_hex, $dia->tipoAsistencia->colorTexto());
            }
        }

        return $datos;
    }
}
