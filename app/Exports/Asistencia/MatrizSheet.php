<?php

namespace App\Exports\Asistencia;

use App\Models\TipoAsistencia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MatrizSheet extends HojaAsistencia
{
    private const JUSTIFICADOS = [
        TipoAsistencia::VACACION => 'VACACIÓN',
        TipoAsistencia::FERIADO => 'FERIADO',
        TipoAsistencia::REMOTO => 'REMOTO',
        TipoAsistencia::TRABAJO_FUERA => 'FUERA',
        TipoAsistencia::BAJA_PERMISO => 'PERMISO',
    ];

    /**
     * @param  list<Carbon>  $dias
     */
    public function __construct(private Collection $filas, private array $dias)
    {
        $this->filasEncabezado = 2;
    }

    public function title(): string
    {
        return 'Matriz';
    }

    public function array(): array
    {
        $tipos = TipoAsistencia::porCodigo();
        $noAplica = $tipos[TipoAsistencia::NO_APLICA];

        $encabezado = ['ID', 'Nombre', 'Área'];
        $diasSemana = ['', '', ''];
        foreach ($this->dias as $dia) {
            $encabezado[] = $dia->day;
            $diasSemana[] = ucfirst($dia->translatedFormat('D'));
        }
        array_push($encabezado, 'Atrasos', 'Min. atraso', 'Sin marcar', 'Incompletos', 'Eventos', 'Multa (Bs)');
        array_push($diasSemana, '', '', '', '', '', '');

        $datos = [$encabezado, $diasSemana];

        foreach ($this->filas as $i => $fila) {
            $numeroFila = $i + 3;
            $renglon = [$fila['empleado']->codigo_biometrico, $fila['empleado']->persona?->nombreCompleto(), $fila['empleado']->area?->nombre];

            foreach ($this->dias as $j => $dia) {
                $asistencia = $fila['dias'][$dia->toDateString()] ?? null;
                $tipo = $asistencia?->tipoAsistencia ?? $noAplica;
                $marcas = $asistencia?->marcaciones ?? [];

                $renglon[] = match (true) {
                    ! $asistencia => '—',
                    isset(self::JUSTIFICADOS[$tipo->codigo]) => self::JUSTIFICADOS[$tipo->codigo],
                    default => implode("\n", $marcas),
                };

                if ($tipo->codigo !== TipoAsistencia::PUNTUAL) {
                    $this->pintar(Coordinate::stringFromColumnIndex($j + 4).$numeroFila, $tipo->color_hex, $tipo->colorTexto());
                }
            }

            $t = $fila['totales'];
            array_push($renglon, $t['atrasos'], $t['minutos'], $t['sin_marcar'], $t['incompletos'], $t['eventos'], $t['multa']);
            $datos[] = $renglon;
        }

        // Leyenda al pie.
        $datos[] = [];
        $datos[] = ['Leyenda'];
        foreach ($tipos as $tipo) {
            if ($tipo->codigo === TipoAsistencia::PUNTUAL) {
                continue;
            }
            $datos[] = ['', $tipo->nombre];
            $this->pintar('A'.count($datos), $tipo->color_hex, $tipo->colorTexto());
        }
        $datos[] = ['', 'Eventos = atrasos + días sin marcar. Los primeros '.config('asistencia.eventos_perdonados').' se perdonan; desde el siguiente, Bs '.config('asistencia.multa_por_evento').' por evento.'];

        return $datos;
    }

    protected function aplicarFormato(Worksheet $sheet): void
    {
        parent::aplicarFormato($sheet);

        $ultimaColumnaDias = Coordinate::stringFromColumnIndex(count($this->dias) + 3);
        $ultimaFila = count($this->filas) + 2;
        $sheet->getStyle("D3:{$ultimaColumnaDias}{$ultimaFila}")->getAlignment()
            ->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->freezePane('D3');
    }
}
