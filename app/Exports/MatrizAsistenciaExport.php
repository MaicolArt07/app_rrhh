<?php

namespace App\Exports;

use App\Exports\Asistencia\DetalleEventosSheet;
use App\Exports\Asistencia\IncompletosSheet;
use App\Exports\Asistencia\MatrizSheet;
use App\Exports\Asistencia\ResumenSheet;
use App\Services\Asistencia\AsistenciaService;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Excel de asistencia del mes con los mismos colores de la matriz (§7).
 */
class MatrizAsistenciaExport implements WithMultipleSheets
{
    public function __construct(private Carbon $mes, private ?int $idArea = null, private ?string $buscar = null, private ?int $idEmpresa = null)
    {
    }

    public function sheets(): array
    {
        $servicio = app(AsistenciaService::class);
        $filas = $servicio->matriz($this->mes, $this->idArea, $this->buscar, $this->idEmpresa);
        $dias = $servicio->diasHabiles($this->mes);

        return [
            new MatrizSheet($filas, $dias),
            new ResumenSheet($filas),
            new DetalleEventosSheet($filas),
            new IncompletosSheet($filas),
        ];
    }
}
