<?php

namespace App\Exports\Asistencia;

use Illuminate\Support\Collection;

class DetalleEventosSheet extends HojaAsistencia
{
    public function __construct(private Collection $filas)
    {
    }

    public function title(): string
    {
        return 'Detalle de eventos';
    }

    public function array(): array
    {
        $datos = [['ID', 'Nombre', 'Fecha', 'Día', 'Tipo', 'Entrada', 'Min. atraso', 'N.º evento', 'Multa del día (Bs)']];

        foreach ($this->filas as $fila) {
            $eventos = $fila['dias']->where('es_sancionable', true)->sortBy('fecha');

            if ($eventos->isEmpty()) {
                continue;
            }

            foreach ($eventos as $evento) {
                $datos[] = [
                    $fila['empleado']->codigo_biometrico,
                    $fila['empleado']->persona?->nombreCompleto(),
                    $evento->fecha->format('d/m/Y'),
                    ucfirst($evento->fecha->translatedFormat('l')),
                    $evento->tipoAsistencia->nombre,
                    $evento->hora_entrada?->format('H:i') ?? '—',
                    $evento->minutos_atraso,
                    $evento->numero_evento_mes,
                    (float) $evento->multa_dia,
                ];

                $n = count($datos);
                $this->pintar("E{$n}", $evento->tipoAsistencia->color_hex, $evento->tipoAsistencia->colorTexto());
                if ((float) $evento->multa_dia > 0) {
                    $this->pintar("I{$n}", '#FF0000', '#FFFFFF');
                }
            }

            $datos[] = ['', 'TOTAL — '.$fila['empleado']->persona?->nombreCompleto(), '', '', '', '', $fila['totales']['minutos'], $fila['totales']['eventos'], $fila['totales']['multa']];
            $n = count($datos);
            $this->pintar("A{$n}:I{$n}", '#E5E7EB', '#111827');
        }

        return $datos;
    }
}
