<?php

namespace App\Exports\Asistencia;

use App\Models\TipoAsistencia;
use Illuminate\Support\Collection;

class ResumenSheet extends HojaAsistencia
{
    public function __construct(private Collection $filas)
    {
    }

    public function title(): string
    {
        return 'Resumen';
    }

    public function array(): array
    {
        $atraso = TipoAsistencia::porCodigo()[TipoAsistencia::ATRASO];
        $datos = [['ID', 'Nombre', 'Área', 'Atrasos', 'Min. atraso', 'Incompletos', 'Sin marcar', 'Eventos', 'Multa (Bs)']];

        foreach ($this->filas as $fila) {
            $t = $fila['totales'];
            $datos[] = [
                $fila['empleado']->codigo_biometrico,
                $fila['empleado']->persona?->nombreCompleto(),
                $fila['empleado']->area?->nombre,
                $t['atrasos'], $t['minutos'], $t['incompletos'], $t['sin_marcar'], $t['eventos'], $t['multa'],
            ];

            $n = count($datos);
            if ($t['atrasos'] > 0) {
                $this->pintar("D{$n}", $atraso->color_hex, $atraso->colorTexto());
            }
            if ($t['multa'] > 0) {
                $this->pintar("I{$n}", '#FF0000', '#FFFFFF');
            }
        }

        $datos[] = [
            'TOTAL', '', '',
            $this->filas->sum('totales.atrasos'), $this->filas->sum('totales.minutos'),
            $this->filas->sum('totales.incompletos'), $this->filas->sum('totales.sin_marcar'),
            $this->filas->sum('totales.eventos'), $this->filas->sum('totales.multa'),
        ];

        return $datos;
    }
}
