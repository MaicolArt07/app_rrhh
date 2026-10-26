<?php

namespace App\Services\Asistencia;

use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

/**
 * Lee el reporte crudo del reloj ZKTeco (docs/modulo-asistencia-correcciones.md §2).
 *
 * Formato de la hoja "Reporte de Asistencia":
 * - C3: periodo "2026-08-01 ~ 2026-08-31".
 * - Fila 4: números de día por columna.
 * - Fila r con A = "ID:": C = ID del reloj, K = nombre, U = departamento.
 * - Fila r+1: marcaciones del día concatenadas en bloques HH:MM.
 */
class LectorReporteBiometrico
{
    /**
     * @return array{
     *     periodo_inicio: string,
     *     periodo_fin: string,
     *     empleados: list<array{codigo: string, nombre: string, departamento: ?string, dias: array<string, string>}>
     * }
     */
    public function leer(string $ruta): array
    {
        $hoja = config('asistencia.hoja_reporte');

        $reader = IOFactory::createReaderForFile($ruta);
        $reader->setReadDataOnly(true);
        $libro = $reader->load($ruta);
        $sheet = $libro->getSheetByName($hoja);

        if (! $sheet) {
            throw new RuntimeException("El archivo no tiene la hoja \"{$hoja}\". Verifique que sea el reporte del reloj biométrico.");
        }

        [$inicio, $fin] = $this->leerPeriodo($sheet);
        $columnaPorDia = $this->columnasPorDia($sheet);

        $fechas = [];
        for ($d = $inicio->copy(); $d->lte($fin); $d->addDay()) {
            if (isset($columnaPorDia[$d->day])) {
                $fechas[$d->toDateString()] = $columnaPorDia[$d->day];
            }
        }

        $empleados = [];
        $ultimaFila = $sheet->getHighestDataRow();

        for ($fila = 5; $fila <= $ultimaFila; $fila++) {
            if (trim((string) $sheet->getCell("A{$fila}")->getValue()) !== 'ID:') {
                continue;
            }

            $codigo = $this->texto($sheet->getCell("C{$fila}")->getValue());

            if ($codigo === '') {
                continue;
            }

            $dias = [];
            foreach ($fechas as $fecha => $columna) {
                $dias[$fecha] = $this->marcaciones($sheet->getCell([$columna, $fila + 1])->getValue());
            }

            $empleados[] = [
                'codigo' => $codigo,
                'nombre' => $this->texto($sheet->getCell("K{$fila}")->getValue()),
                'departamento' => $this->texto($sheet->getCell("U{$fila}")->getValue()) ?: null,
                'dias' => $dias,
            ];
        }

        $libro->disconnectWorksheets();

        return [
            'periodo_inicio' => $inicio->toDateString(),
            'periodo_fin' => $fin->toDateString(),
            'empleados' => $empleados,
        ];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function leerPeriodo(Worksheet $sheet): array
    {
        $texto = $this->texto($sheet->getCell('C3')->getValue());
        $partes = array_map('trim', explode('~', $texto));

        try {
            $inicio = Carbon::parse($partes[0] ?? '')->startOfDay();
            $fin = Carbon::parse($partes[1] ?? '')->startOfDay();
        } catch (\Throwable) {
            throw new RuntimeException("No se pudo leer el periodo en la celda C3 (\"{$texto}\").");
        }

        if ($fin->lt($inicio) || $inicio->diffInDays($fin) > 30) {
            throw new RuntimeException("El periodo \"{$texto}\" no es válido: debe ser de un mes como máximo.");
        }

        return [$inicio, $fin];
    }

    /**
     * @return array<int, int> día del mes → índice de columna (1 = A)
     */
    private function columnasPorDia(Worksheet $sheet): array
    {
        $columnas = [];
        $ultima = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

        for ($c = 1; $c <= $ultima; $c++) {
            $valor = $sheet->getCell([$c, 4])->getValue();

            if (is_numeric($valor) && $valor >= 1 && $valor <= 31) {
                $columnas[(int) $valor] = $c;
            }
        }

        if (! $columnas) {
            throw new RuntimeException('No se encontraron los números de día en la fila 4 de la hoja.');
        }

        return $columnas;
    }

    /** Deja solo dígitos y ":" (el reloj concatena las horas sin separador). */
    private function marcaciones(mixed $valor): string
    {
        return preg_replace('/[^0-9:]/', '', (string) $valor);
    }

    private function texto(mixed $valor): string
    {
        if (is_float($valor) && floor($valor) === $valor) {
            $valor = (int) $valor;
        }

        return trim((string) $valor);
    }
}
