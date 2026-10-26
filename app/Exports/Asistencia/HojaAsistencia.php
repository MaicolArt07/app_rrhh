<?php

namespace App\Exports\Asistencia;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Base de las hojas del Excel de asistencia: encabezado con estilo, bordes y
 * celdas pintadas con el color de su tipo de asistencia.
 */
abstract class HojaAsistencia implements FromArray, WithTitle, WithEvents, ShouldAutoSize, WithStrictNullComparison
{
    /** Coordenada → [color de fondo, color de texto] */
    protected array $colores = [];

    protected int $filasEncabezado = 1;

    protected function pintar(string $coordenada, string $fondo, string $texto = '#000000'): void
    {
        $this->colores[$coordenada] = [$fondo, $texto];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->aplicarFormato($sheet);
            },
        ];
    }

    protected function aplicarFormato(Worksheet $sheet): void
    {
        $rango = 'A1:'.$sheet->getHighestColumn().$sheet->getHighestRow();
        $encabezado = 'A1:'.$sheet->getHighestColumn().$this->filasEncabezado;

        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFBFBFBF');
        $sheet->getStyle($rango)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($encabezado)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($encabezado)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1F2937');
        $sheet->getStyle($encabezado)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
        $sheet->freezePane('A'.($this->filasEncabezado + 1));

        foreach ($this->colores as $coordenada => [$fondo, $texto]) {
            $estilo = $sheet->getStyle($coordenada);
            $estilo->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF'.ltrim($fondo, '#'));
            $estilo->getFont()->getColor()->setARGB('FF'.ltrim($texto, '#'));
        }
    }
}
