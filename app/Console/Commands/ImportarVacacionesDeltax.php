<?php

namespace App\Console\Commands;

use App\Models\Cargo;
use App\Models\Contrato;
use App\Models\Departamento;
use App\Models\Empleado;
use App\Models\Gestion;
use App\Models\Persona;
use App\Models\SaldoVacacion;
use App\Models\Tipo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportarVacacionesDeltax extends Command
{
    protected $signature = 'rrhh:importar-vacaciones-deltax
        {file=documentos/05 PROGRAMACIÓN - VACACIONES DELTAX 2026.xlsx : Ruta del archivo, relativa a la raíz del proyecto}
        {--sheet=Conteo de Vacaciones Gral. : Nombre de la hoja a importar}
        {--gestion=2026 : Año de gestión al que se asignará el saldo de vacaciones}
        {--desde-fila=8 : Primera fila de datos (después de encabezados)}
        {--hasta-fila=73 : Última fila de datos a procesar}';

    protected $description = 'Importa personas, empleados, contratos y saldos de vacaciones desde el Excel de programación de vacaciones DeltaX';

    public function handle(): int
    {
        $path = base_path($this->argument('file'));

        if (! is_file($path)) {
            $this->error("No se encontró el archivo: {$path}");

            return self::FAILURE;
        }

        $sheetName = $this->option('sheet');

        // Las celdas de esta hoja son fórmulas que referencian otras hojas del
        // libro (p. ej. 'Registro BO 2026'). Usamos el valor cacheado que Excel
        // guardó la última vez que se calculó el archivo (getOldCalculatedValue)
        // en vez de recalcular en vivo, para no depender de la fecha del sistema
        // ni tener que cargar el libro completo en memoria.
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly([$sheetName]);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheetByName($sheetName);

        if (! $sheet) {
            $this->error("No se encontró la hoja \"{$sheetName}\" en el archivo.");

            return self::FAILURE;
        }

        $tipoDocumento = Tipo::firstOrCreate(
            ['categoria' => 'tipo_documento', 'codigo' => 'ci'],
            ['nombre' => 'Cédula de Identidad', 'orden' => 0, 'estado' => true]
        );

        $tipoContratoFijo = Tipo::firstOrCreate(
            ['categoria' => 'tipo_contrato', 'codigo' => 'fijo'],
            ['nombre' => 'Fijo', 'orden' => 10, 'estado' => true]
        );

        $tipoContratoPlanilla = Tipo::firstOrCreate(
            ['categoria' => 'tipo_contrato', 'codigo' => 'planilla'],
            ['nombre' => 'Planilla', 'orden' => 11, 'estado' => true]
        );

        $estadoActivo = Tipo::where('categoria', 'estado_empleado')->where('codigo', 'activo')->firstOrFail();

        $anioGestion = (int) $this->option('gestion');
        $gestion = Gestion::firstOrCreate(
            ['anio' => $anioGestion],
            [
                'fecha_inicio' => "{$anioGestion}-01-01",
                'fecha_fin' => "{$anioGestion}-12-31",
                'estado' => true,
            ]
        );

        $desdeFila = (int) $this->option('desde-fila');
        $hastaFila = (int) $this->option('hasta-fila');
        $procesados = 0;
        $omitidos = [];

        for ($row = $desdeFila; $row <= $hastaFila; $row++) {
            $nombreCompleto = trim((string) $this->valorCalculado($sheet, "E{$row}"));

            if ($nombreCompleto === '' || str_starts_with($nombreCompleto, '=') || str_contains($nombreCompleto, '#REF')) {
                continue;
            }

            $numeroDocumento = trim((string) $this->valorCalculado($sheet, "D{$row}"));

            if ($numeroDocumento === '' || str_starts_with($numeroDocumento, '=') || str_contains($numeroDocumento, '#REF')) {
                $omitidos[] = "Fila {$row}: sin número de documento válido ({$nombreCompleto}).";

                continue;
            }

            $estadoContrato = strtoupper(trim((string) $this->valorCalculado($sheet, "C{$row}")));
            $area = trim((string) $this->valorCalculado($sheet, "F{$row}"));
            $sexo = strtoupper(trim((string) $this->valorCalculado($sheet, "G{$row}")));
            $ocupacion = trim((string) $this->valorCalculado($sheet, "H{$row}"));
            $fechaIngresoValor = $this->valorCalculado($sheet, "I{$row}");
            // Las columnas K, R y S usan formato de Excel "#,##0" (sin decimales):
            // aunque el valor interno de la fórmula es fraccionario, lo que ve el
            // usuario en la hoja son números enteros redondeados, así que replicamos
            // ese mismo redondeo al importar.
            $diasAcumulados = round((float) ($this->valorCalculado($sheet, "K{$row}") ?: 0));
            $totalDiasTomados = round((float) ($this->valorCalculado($sheet, "R{$row}") ?: 0));
            $saldoDiasPorTomar = round((float) ($this->valorCalculado($sheet, "S{$row}") ?: 0));

            $fechaIngreso = $this->parseFecha($fechaIngresoValor);
            [$primerNombre, $segundoNombre, $primerApellido, $segundoApellido] = $this->dividirNombre($nombreCompleto);

            DB::transaction(function () use (
                $tipoDocumento,
                $numeroDocumento,
                $primerNombre,
                $segundoNombre,
                $primerApellido,
                $segundoApellido,
                $sexo,
                $area,
                $ocupacion,
                $fechaIngreso,
                $estadoActivo,
                $estadoContrato,
                $tipoContratoFijo,
                $tipoContratoPlanilla,
                $gestion,
                $diasAcumulados,
                $totalDiasTomados,
                $saldoDiasPorTomar,
            ) {
                $persona = Persona::updateOrCreate(
                    [
                        'id_tipo_documento' => $tipoDocumento->id,
                        'numero_documento' => $numeroDocumento,
                    ],
                    [
                        'primer_nombre' => $primerNombre,
                        'segundo_nombre' => $segundoNombre,
                        'primer_apellido' => $primerApellido,
                        'segundo_apellido' => $segundoApellido,
                        'genero' => $sexo === 'F' ? 'femenino' : 'masculino',
                        'estado' => true,
                    ]
                );

                $departamento = Departamento::firstOrCreate(
                    ['nombre' => $area],
                    ['estado' => true]
                );

                $cargo = Cargo::firstOrCreate(
                    ['nombre' => $ocupacion, 'id_departamento' => $departamento->id],
                    ['sueldo_base' => 0, 'estado' => true]
                );

                $empleado = Empleado::updateOrCreate(
                    ['id_persona' => $persona->id],
                    [
                        'codigo_empleado' => 'EMP-'.$numeroDocumento,
                        'id_cargo' => $cargo->id,
                        'id_departamento' => $departamento->id,
                        'fecha_ingreso' => $fechaIngreso,
                        'sueldo_actual' => 0,
                        'id_estado_empleado' => $estadoActivo->id,
                        'estado' => true,
                    ]
                );

                $tipoContrato = $estadoContrato === 'FIJO' ? $tipoContratoFijo : $tipoContratoPlanilla;

                Contrato::updateOrCreate(
                    ['id_empleado' => $empleado->id, 'id_tipo_contrato' => $tipoContrato->id],
                    [
                        'fecha_inicio' => $fechaIngreso,
                        'estado' => true,
                    ]
                );

                SaldoVacacion::updateOrCreate(
                    ['id_empleado' => $empleado->id, 'id_gestion' => $gestion->id],
                    [
                        'dias_generados' => $diasAcumulados,
                        'dias_tomados' => $totalDiasTomados,
                        'dias_disponibles' => $saldoDiasPorTomar,
                        'fecha_ultimo_calculo' => now()->toDateString(),
                        'estado' => true,
                    ]
                );
            });

            $procesados++;
        }

        $this->info("Empleados procesados: {$procesados}");

        foreach ($omitidos as $mensaje) {
            $this->warn($mensaje);
        }

        return self::SUCCESS;
    }

    private function valorCalculado(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $coordenada): mixed
    {
        try {
            return $sheet->getCell($coordenada)->getOldCalculatedValue();
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseFecha(mixed $valor): string
    {
        if ($valor instanceof \DateTimeInterface) {
            return $valor->format('Y-m-d');
        }

        if (is_numeric($valor)) {
            return ExcelDate::excelToDateTimeObject($valor)->format('Y-m-d');
        }

        return now()->toDateString();
    }

    /**
     * Divide un nombre completo en primer/segundo nombre y primer/segundo apellido,
     * siguiendo la convención latinoamericana de dos apellidos.
     *
     * @return array{0: string, 1: ?string, 2: string, 3: ?string}
     */
    private function dividirNombre(string $nombreCompleto): array
    {
        $partes = preg_split('/\s+/', trim($nombreCompleto));
        $total = count($partes);

        return match (true) {
            $total <= 1 => [$partes[0] ?? '', null, $partes[0] ?? '', null],
            $total === 2 => [$partes[0], null, $partes[1], null],
            $total === 3 => [$partes[0], null, $partes[1], $partes[2]],
            $total === 4 => [$partes[0], $partes[1], $partes[2], $partes[3]],
            default => [
                $partes[0],
                $partes[1],
                implode(' ', array_slice($partes, 2, $total - 3)),
                $partes[$total - 1],
            ],
        };
    }
}
