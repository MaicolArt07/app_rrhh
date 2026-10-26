<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\Cargo;
use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Persona;
use App\Models\Tipo;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class SincronizarColaboradores extends Command
{
    protected $signature = 'rrhh:sincronizar-colaboradores
        {file=informe-datos/01. Base _ Guía para Incorporación _ Desvinculación.xlsx : Ruta del archivo, relativa a la raíz del proyecto}
        {--sheet=Base Colaboradores : Nombre de la hoja a leer}
        {--dry-run : Muestra los cambios sin guardarlos}';

    protected $description = 'Compara la hoja "Base Colaboradores" con los empleados registrados y actualiza empresa, área, cargo, fecha de ingreso, género, salario, contrato y estado';

    /** Salarios menores a este monto se consideran un error de carga y no se aplican. */
    private const SALARIO_MINIMO_VALIDO = 400;

    /** Nombres de área del Excel que se unifican bajo un nombre canónico. */
    private const AREAS_EQUIVALENTES = [
        'cs' => 'Customer Success',
        'customer success' => 'Customer Success',
        'operaciones' => 'Operaciones',
        'ti' => 'TI',
        'rrhh' => 'RRHH',
        'ia' => 'IA',
    ];

    private array $cambios = [];
    private array $dudosos = [];
    private array $omitidos = [];
    private array $cacheAreas = [];
    private array $cacheEmpresas = [];
    private array $cacheCargos = [];

    public function handle(): int
    {
        $path = base_path($this->argument('file'));

        // Ubicación anterior del Excel (raíz del proyecto).
        if (! is_file($path) && is_file(base_path(basename($path)))) {
            $path = base_path(basename($path));
        }

        if (! is_file($path)) {
            $this->error("No se encontró el archivo: {$path}");

            return self::FAILURE;
        }

        $filas = $this->leerFilas($path, $this->option('sheet'));

        if ($filas === null) {
            return self::FAILURE;
        }

        $tipos = $this->cargarTipos();
        $dryRun = (bool) $this->option('dry-run');

        DB::beginTransaction();

        try {
            $usadas = $this->actualizarExistentes($filas, $tipos);
            $this->crearNuevos($filas, $usadas, $tipos);
            $this->limpiarCatalogos();

            $dryRun ? DB::rollBack() : DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Se canceló la sincronización, no se guardó ningún cambio: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->mostrarReporte($dryRun);

        return self::SUCCESS;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    private function leerFilas(string $path, string $hoja): ?array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly([$hoja]);
        $sheet = $reader->load($path)->getSheetByName($hoja);

        if (! $sheet) {
            $this->error("No se encontró la hoja \"{$hoja}\" en el archivo.");

            return null;
        }

        $datos = $sheet->toArray(null, true, false, false);
        $encabezados = array_map(fn ($h) => $this->normalizar((string) $h), array_shift($datos));
        $columna = fn (string $nombre) => array_search($this->normalizar($nombre), $encabezados, true);

        $indices = [
            'empresa' => $columna('Empresa'),
            'ci' => $columna('ID Colaborador'),
            'nombre' => $columna('Nombre Completo'),
            'sexo' => $columna('Sexo'),
            'cargo' => $columna('Cargo'),
            'area' => $columna('Área'),
            'tipo_contrato' => $columna('Tipo de Contrato'),
            'ingreso' => $columna('Fecha de Ingreso'),
            'fin' => $columna('Fecha Fin de Contrato'),
            'estado' => $columna('Estado (Activo/Inactivo)'),
            'salario' => $columna('Salario Básico'),
        ];

        // Columna opcional: se usa para sugerir el lugar de expedición del CI.
        $iProcedencia = $columna('DPTO - Provincia');

        if (in_array(false, $indices, true)) {
            $faltantes = implode(', ', array_keys(array_filter($indices, fn ($i) => $i === false)));
            $this->error("Faltan columnas esperadas en la hoja: {$faltantes}");

            return null;
        }

        $filas = [];

        foreach ($datos as $i => $valores) {
            $nombre = $this->limpiarTexto($valores[$indices['nombre']] ?? null);

            if ($nombre === null) {
                continue;
            }

            $filas[] = [
                'fila' => $i + 2,
                'empresa' => $this->limpiarTexto($valores[$indices['empresa']] ?? null),
                'ci' => $this->limpiarCi($valores[$indices['ci']] ?? null),
                'nombre' => $nombre,
                'sexo' => strtoupper((string) $this->limpiarTexto($valores[$indices['sexo']] ?? null)),
                'cargo' => $this->limpiarTexto($valores[$indices['cargo']] ?? null),
                'area' => $this->normalizarArea($valores[$indices['area']] ?? null),
                'tipo_contrato' => $this->normalizar((string) ($valores[$indices['tipo_contrato']] ?? '')),
                'ingreso' => $this->parseFecha($valores[$indices['ingreso']] ?? null),
                'fin' => $this->parseFecha($valores[$indices['fin']] ?? null),
                'activo' => $this->normalizar((string) ($valores[$indices['estado']] ?? '')) === 'activo',
                'estado_texto' => $this->limpiarTexto($valores[$indices['estado']] ?? null),
                'salario' => $valores[$indices['salario']] ?? null,
                'procedencia' => $iProcedencia !== false ? $this->limpiarTexto($valores[$iProcedencia] ?? null) : null,
            ];
        }

        return $filas;
    }

    private function cargarTipos(): array
    {
        return [
            'documento_ci' => Tipo::firstOrCreate(
                ['categoria' => 'tipo_documento', 'codigo' => 'ci'],
                ['nombre' => 'Cédula de Identidad', 'orden' => 0, 'estado' => true]
            ),
            'activo' => Tipo::where('categoria', 'estado_empleado')->where('codigo', 'activo')->firstOrFail(),
            'inactivo' => Tipo::where('categoria', 'estado_empleado')->where('codigo', 'inactivo')->firstOrFail(),
            'contrato_indefinido' => Tipo::firstOrCreate(
                ['categoria' => 'tipo_contrato', 'codigo' => 'indefinido'],
                ['nombre' => 'Indefinido', 'orden' => 1, 'estado' => true]
            ),
            'contrato_prestacion' => Tipo::firstOrCreate(
                ['categoria' => 'tipo_contrato', 'codigo' => 'prestacion'],
                ['nombre' => 'Prestación de Servicios', 'orden' => 12, 'estado' => true]
            ),
        ];
    }

    /**
     * Actualiza a los empleados registrados con su fila más reciente del Excel.
     *
     * @return array<int, true> Filas del Excel ya asociadas a un empleado.
     */
    private function actualizarExistentes(array $filas, array $tipos): array
    {
        $usadas = [];

        $empleados = Empleado::with(['persona', 'cargo', 'area', 'empresa', 'contratos'])->get();

        foreach ($empleados as $empleado) {
            [$fila, $criterio] = $this->buscarFila($empleado->persona, $filas);

            if (! $fila) {
                $this->omitidos[] = ['Empleado sin fila en el Excel', $empleado->persona->nombreCompleto(), $empleado->codigo_empleado];

                continue;
            }

            foreach ($filas as $otra) {
                if ($this->mismaPersona($otra, $fila)) {
                    $usadas[$otra['fila']] = true;
                }
            }

            $nombre = $empleado->persona->nombreCompleto();

            if ($fila['ci'] && $fila['ci'] !== strtoupper($empleado->persona->numero_documento) && $criterio !== 'ci') {
                $this->dudosos[] = [$nombre, 'CI', $empleado->persona->numero_documento, $fila['ci'], 'Se mantiene el CI de la base'];
            }

            $this->aplicarDatos($empleado, $fila, $tipos, $nombre);
        }

        return $usadas;
    }

    private function aplicarDatos(Empleado $empleado, array $fila, array $tipos, string $nombre): void
    {
        $area = $fila['area'] ? $this->area($fila['area']) : $empleado->area;
        $cargo = $fila['cargo'] ? $this->cargo($fila['cargo'], $area) : $empleado->cargo;

        $empresa = $fila['empresa'] ? $this->empresa($fila['empresa']) : $empleado->empresa;

        $datos = [
            'id_empresa' => $empresa?->id,
            'id_area' => $area->id,
            'id_cargo' => $cargo->id,
            'id_estado_empleado' => $fila['activo'] ? $tipos['activo']->id : $tipos['inactivo']->id,
            'estado' => $fila['activo'],
        ];

        $ingreso = $this->fechaIngresoValida($fila, $nombre, $empleado->fecha_ingreso?->toDateString());
        if ($ingreso) {
            $datos['fecha_ingreso'] = $ingreso;
        }

        $salario = $this->salarioValido($fila, $nombre, (string) $empleado->sueldo_actual);
        if ($salario !== null) {
            $datos['sueldo_actual'] = $salario;
        }

        $this->registrarCambios($nombre, [
            'Empresa' => [$empleado->empresa?->nombre, $empresa?->nombre],
            'Área' => [$empleado->area?->nombre, $area->nombre],
            'Cargo' => [$empleado->cargo?->nombre, $cargo->nombre],
            'Fecha de ingreso' => [$empleado->fecha_ingreso?->toDateString(), $datos['fecha_ingreso'] ?? $empleado->fecha_ingreso?->toDateString()],
            'Salario' => [number_format((float) $empleado->sueldo_actual, 2, '.', ''), number_format((float) ($datos['sueldo_actual'] ?? $empleado->sueldo_actual), 2, '.', '')],
            'Estado' => [$empleado->estado ? 'Activo' : 'Inactivo', $fila['activo'] ? 'Activo' : 'Inactivo'],
        ]);

        $empleado->update($datos);

        $genero = $this->genero($fila['sexo']);
        if ($genero && $genero !== $empleado->persona->genero) {
            $this->registrarCambios($nombre, ['Género' => [$empleado->persona->genero, $genero]]);
            $empleado->persona->update(['genero' => $genero]);
        }

        if ($fila['procedencia'] && $fila['procedencia'] !== $empleado->persona->procedencia) {
            $this->registrarCambios($nombre, ['DPTO - Provincia' => [$empleado->persona->procedencia, $fila['procedencia']]]);
            $empleado->persona->update(['procedencia' => $fila['procedencia']]);
        }

        $this->actualizarContrato($empleado, $fila, $tipos, $nombre);
    }

    private function actualizarContrato(Empleado $empleado, array $fila, array $tipos, string $nombre): void
    {
        $empleado->refresh();
        $tipoContrato = $this->tipoContrato($fila, $tipos);
        $contrato = $empleado->contratos()->orderByDesc('fecha_inicio')->first();
        $fechaInicio = $empleado->fecha_ingreso->toDateString();

        if (! $contrato) {
            $tipoContrato ??= $tipos['contrato_indefinido'];
            $this->registrarCambios($nombre, ['Contrato' => ['—', $tipoContrato->nombre]]);
            // La fecha fin de contrato queda vacía: la completa RR.HH.
            Contrato::create([
                'id_empleado' => $empleado->id,
                'id_empresa' => $empleado->id_empresa,
                'id_tipo_contrato' => $tipoContrato->id,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => null,
                'estado' => true,
            ]);

            return;
        }

        $cambios = [];

        // Los contratos sin empresa toman la del empleado.
        if (! $contrato->id_empresa && $empleado->id_empresa) {
            $cambios['id_empresa'] = $empleado->id_empresa;
        }

        // Con historial (renovaciones o paso a indefinido) el tipo y las fechas se
        // gestionan en el módulo de contratos: la sincronización no los toca.
        if ($tipoContrato && $empleado->contratos()->count() === 1) {
            $this->registrarCambios($nombre, [
                'Contrato' => [$contrato->tipoContrato?->nombre, $tipoContrato->nombre],
                'Inicio de contrato' => [$contrato->fecha_inicio?->toDateString(), $fechaInicio],
            ]);
            $cambios += ['id_tipo_contrato' => $tipoContrato->id, 'fecha_inicio' => $fechaInicio];
        }

        if ($cambios) {
            $contrato->update($cambios);
        }
    }

    /**
     * Registra a los colaboradores activos del Excel que aún no existen en la base.
     */
    private function crearNuevos(array $filas, array $usadas, array $tipos): void
    {
        $pendientes = [];

        foreach ($filas as $fila) {
            if (! $fila['activo'] || isset($usadas[$fila['fila']])) {
                continue;
            }

            $clave = $fila['ci'] ?: $this->normalizar($fila['nombre']);
            $actual = $pendientes[$clave] ?? null;

            if (! $actual || $this->esMasReciente($fila, $actual)) {
                $pendientes[$clave] = $fila;
            }
        }

        foreach ($pendientes as $fila) {
            $nombre = $fila['nombre'];

            if (! $fila['ci']) {
                $this->omitidos[] = ['Colaborador nuevo sin CI en el Excel (no se registró)', $nombre, "fila {$fila['fila']}"];

                continue;
            }

            $genero = $this->genero($fila['sexo']);
            if (! $genero) {
                $this->omitidos[] = ['Colaborador nuevo sin sexo en el Excel (no se registró)', $nombre, "fila {$fila['fila']}"];

                continue;
            }

            if (! $fila['area'] || ! $fila['cargo'] || ! $fila['ingreso']) {
                $this->omitidos[] = ['Colaborador nuevo sin área, cargo o fecha de ingreso (no se registró)', $nombre, "fila {$fila['fila']}"];

                continue;
            }

            [$primerNombre, $segundoNombre, $primerApellido, $segundoApellido] = $this->dividirNombre($nombre);

            $persona = Persona::firstOrCreate(
                ['id_tipo_documento' => $tipos['documento_ci']->id, 'numero_documento' => $fila['ci']],
                [
                    'primer_nombre' => $primerNombre,
                    'segundo_nombre' => $segundoNombre,
                    'primer_apellido' => $primerApellido,
                    'segundo_apellido' => $segundoApellido,
                    'genero' => $genero,
                    'procedencia' => $fila['procedencia'],
                    'estado' => true,
                ]
            );

            $area = $this->area($fila['area']);
            $cargo = $this->cargo($fila['cargo'], $area);
            $ingreso = $this->fechaIngresoValida($fila, $nombre, null) ?? $fila['ingreso'];
            $salario = $this->salarioValido($fila, $nombre, '0') ?? 0;

            $empresa = $fila['empresa'] ? $this->empresa($fila['empresa']) : null;

            $empleado = Empleado::create([
                'id_persona' => $persona->id,
                'id_empresa' => $empresa?->id,
                'codigo_empleado' => 'EMP-'.$fila['ci'],
                'id_cargo' => $cargo->id,
                'id_area' => $area->id,
                'fecha_ingreso' => $ingreso,
                'sueldo_actual' => $salario,
                'id_estado_empleado' => $tipos['activo']->id,
                'estado' => true,
            ]);

            $tipoContrato = $this->tipoContrato($fila, $tipos) ?? $tipos['contrato_indefinido'];

            // La fecha fin de contrato queda vacía: la completa RR.HH.
            Contrato::create([
                'id_empleado' => $empleado->id,
                'id_empresa' => $empresa?->id,
                'id_tipo_contrato' => $tipoContrato->id,
                'fecha_inicio' => $ingreso,
                'fecha_fin' => null,
                'estado' => true,
            ]);

            $this->cambios[] = [$nombre, 'Nuevo empleado', '—', "{$cargo->nombre} · {$area->nombre} · ingreso {$ingreso}"];
        }
    }

    /**
     * Elimina las áreas y cargos que quedaron sin uso (p. ej. el área sin nombre
     * que dejó la importación de vacaciones), siempre que nada los referencie.
     */
    private function limpiarCatalogos(): void
    {
        $cargosSinUso = Cargo::doesntHave('empleados')
            ->whereNotIn('id', DB::table('historial_cargos')->select('id_cargo_anterior')->whereNotNull('id_cargo_anterior'))
            ->whereNotIn('id', DB::table('historial_cargos')->select('id_cargo_nuevo')->whereNotNull('id_cargo_nuevo'))
            ->get();

        foreach ($cargosSinUso as $cargo) {
            $this->cambios[] = ["Cargo \"{$cargo->nombre}\"", 'Eliminado (sin empleados)', '', ''];
            $cargo->delete();
        }

        foreach (Area::doesntHave('empleados')->doesntHave('cargos')->get() as $area) {
            if (trim((string) $area->nombre) === '') {
                $this->cambios[] = ['Área sin nombre', 'Eliminada (sin empleados)', '', ''];
                $area->delete();
            }
        }
    }

    /**
     * @return array{0: ?array, 1: ?string}
     */
    private function buscarFila(Persona $persona, array $filas): array
    {
        $ci = strtoupper((string) $persona->numero_documento);
        $ciBase = explode('-', $ci)[0];
        $nombre = $this->normalizar($persona->nombreCompleto());

        $candidatas = array_filter($filas, fn ($f) => $f['ci'] && ($f['ci'] === $ci || explode('-', $f['ci'])[0] === $ciBase));
        $criterio = 'ci';

        if (! $candidatas) {
            $candidatas = array_filter($filas, fn ($f) => $this->normalizar($f['nombre']) === $nombre);
            $criterio = 'nombre';
        }

        if (! $candidatas) {
            $mejor = 0;
            $tokens = explode(' ', $nombre);

            foreach ($filas as $f) {
                $otros = explode(' ', $this->normalizar($f['nombre']));
                $comunes = count(array_intersect($tokens, $otros));

                if (($comunes >= 3 || ($comunes >= 2 && $comunes === min(count($tokens), count($otros)))) && $comunes > $mejor) {
                    $mejor = $comunes;
                    $candidatas = [$f];
                }
            }

            $criterio = 'aproximado';
        }

        if (! $candidatas) {
            return [null, null];
        }

        $elegida = array_reduce($candidatas, fn ($a, $b) => $a === null || $this->esMasReciente($b, $a) ? $b : $a);

        return [$elegida, $criterio];
    }

    private function mismaPersona(array $a, array $b): bool
    {
        if ($a['ci'] && $b['ci']) {
            return $a['ci'] === $b['ci'];
        }

        return $this->normalizar($a['nombre']) === $this->normalizar($b['nombre']);
    }

    private function esMasReciente(array $a, array $b): bool
    {
        return [$a['ingreso'] ?? '', $a['fila']] > [$b['ingreso'] ?? '', $b['fila']];
    }

    private function fechaIngresoValida(array $fila, string $nombre, ?string $actual): ?string
    {
        $ingreso = $fila['ingreso'];

        if (! $ingreso || $ingreso === $actual) {
            return null;
        }

        if ($ingreso > now()->toDateString()) {
            $this->dudosos[] = [$nombre, 'Fecha de ingreso', $actual ?? '—', $ingreso, 'Es una fecha futura; se mantiene la de la base'];

            return null;
        }

        if ($fila['fin'] && $fila['fin'] < $ingreso) {
            $this->dudosos[] = [$nombre, 'Fecha de ingreso', $actual ?? '—', $ingreso, "Es posterior a la fecha fin del Excel ({$fila['fin']}); se mantiene la de la base"];

            return null;
        }

        return $ingreso;
    }

    private function salarioValido(array $fila, string $nombre, string $actual): ?float
    {
        $valor = $fila['salario'];

        if ($valor === null || $valor === '') {
            return null;
        }

        if (! is_numeric($valor)) {
            $this->dudosos[] = [$nombre, 'Salario', $actual, (string) $valor, 'No es un monto numérico; se mantiene el de la base'];

            return null;
        }

        if ((float) $valor < self::SALARIO_MINIMO_VALIDO) {
            $this->dudosos[] = [$nombre, 'Salario', $actual, number_format((float) $valor, 2, '.', ''), 'Monto demasiado bajo; se mantiene el de la base'];

            return null;
        }

        return round((float) $valor, 2);
    }

    private function tipoContrato(array $fila, array $tipos): ?Tipo
    {
        return match (true) {
            str_starts_with($fila['tipo_contrato'], 'indefinido') => $tipos['contrato_indefinido'],
            str_starts_with($fila['tipo_contrato'], 'prestacion') => $tipos['contrato_prestacion'],
            default => null,
        };
    }

    /** Empresa por el nombre del Excel ("Deltaexpress" = "Delta Express"); si no existe, se crea. */
    private function empresa(string $nombre): Empresa
    {
        $clave = $this->normalizar(str_replace(' ', '', $nombre));

        return $this->cacheEmpresas[$clave] ??= Empresa::porNombreFlexible($nombre)
            ?? Empresa::create([
                'codigo' => Str::upper(Str::substr(preg_replace('/[^A-Za-z]/', '', Str::ascii($nombre)), 0, 10)),
                'nombre' => $nombre,
                'estado' => true,
            ]);
    }

    private function area(string $nombre): Area
    {
        $clave = $this->normalizar($nombre);

        return $this->cacheAreas[$clave] ??= Area::whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre)])->first()
            ?? Area::create(['nombre' => $nombre, 'estado' => true]);
    }

    private function cargo(string $nombre, Area $area): Cargo
    {
        $clave = $area->id.'|'.$this->normalizar($nombre);

        if (isset($this->cacheCargos[$clave])) {
            return $this->cacheCargos[$clave];
        }

        $cargo = Cargo::where('id_area', $area->id)->whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre)])->first();

        // Reutiliza el cargo que la importación anterior dejó en el área sin nombre.
        if (! $cargo) {
            $cargo = Cargo::whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre)])
                ->whereHas('area', fn ($q) => $q->where('nombre', ''))
                ->first();
            $cargo?->update(['id_area' => $area->id]);
        }

        return $this->cacheCargos[$clave] = $cargo
            ?? Cargo::create(['id_area' => $area->id, 'nombre' => $nombre, 'sueldo_base' => 0, 'estado' => true]);
    }

    private function genero(string $sexo): ?string
    {
        return match ($sexo) {
            'M' => 'masculino',
            'F' => 'femenino',
            default => null,
        };
    }

    private function registrarCambios(string $nombre, array $campos): void
    {
        foreach ($campos as $campo => [$antes, $despues]) {
            if ((string) $antes !== (string) $despues) {
                $this->cambios[] = [$nombre, $campo, $antes ?? '—', $despues ?? '—'];
            }
        }
    }

    private function mostrarReporte(bool $dryRun): void
    {
        $this->newLine();
        $this->info($dryRun ? 'SIMULACIÓN: no se guardó ningún cambio.' : 'Sincronización aplicada.');

        $this->newLine();
        $this->line('<options=bold>Cambios ('.count($this->cambios).')</>');
        $this->table(['Colaborador', 'Campo', 'Antes', 'Después'], $this->cambios);

        if ($this->dudosos) {
            $this->line('<options=bold>Datos dudosos no aplicados ('.count($this->dudosos).')</>');
            $this->table(['Colaborador', 'Campo', 'Base', 'Excel', 'Motivo'], $this->dudosos);
        }

        if ($this->omitidos) {
            $this->line('<options=bold>Omitidos ('.count($this->omitidos).')</>');
            $this->table(['Motivo', 'Colaborador', 'Referencia'], $this->omitidos);
        }
    }

    private function normalizarArea(mixed $valor): ?string
    {
        $area = $this->limpiarTexto($valor);

        if ($area === null) {
            return null;
        }

        return self::AREAS_EQUIVALENTES[$this->normalizar($area)] ?? $area;
    }

    private function limpiarTexto(mixed $valor): ?string
    {
        // Quita caracteres invisibles de formato (p. ej. U+202C) que trae el Excel.
        $texto = trim(preg_replace(['/\p{Cf}/u', '/\s+/u'], ['', ' '], (string) $valor));

        return $texto === '' || str_starts_with($texto, '#') ? null : $texto;
    }

    private function limpiarCi(mixed $valor): ?string
    {
        if (is_float($valor) || is_int($valor)) {
            $valor = (string) (int) $valor;
        }

        $ci = strtoupper((string) $this->limpiarTexto($valor));

        return $ci === '' ? null : $ci;
    }

    private function normalizar(string $texto): string
    {
        $texto = Str::lower(Str::ascii($texto));

        return trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9 ]/', '', $texto)));
    }

    private function parseFecha(mixed $valor): ?string
    {
        try {
            if ($valor instanceof \DateTimeInterface) {
                return $valor->format('Y-m-d');
            }

            if (is_numeric($valor) && $valor > 0 && $valor < 2958466) {
                return ExcelDate::excelToDateTimeObject($valor)->format('Y-m-d');
            }

            if (is_string($valor) && trim($valor) !== '' && ! str_starts_with(trim($valor), '#')) {
                return Carbon::parse($valor)->toDateString();
            }
        } catch (\Throwable) {
            // Fecha ilegible: se trata como vacía.
        }

        return null;
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
