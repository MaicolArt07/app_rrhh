<?php

namespace App\Services\Asistencia;

use App\Models\Asistencia;
use App\Models\DiaRemoto;
use App\Models\Empleado;
use App\Models\Feriado;
use App\Models\Gestion;
use App\Models\ImportacionBiometrico;
use App\Models\Mes;
use App\Models\PermisoLaboral;
use App\Models\Tipo;
use App\Models\TipoAsistencia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Orquesta el módulo de asistencia (docs/modulo-asistencia-correcciones.md):
 * importación del biométrico, clasificación de cada día, eventos sancionables
 * y multa del mes, asignaciones manuales y días remotos.
 */
class AsistenciaService
{
    /** tipos_asistencia → código de tipos.estado_asistencia (compatibilidad con el CRUD de marcajes). */
    private const ESTADO_EQUIVALENTE = [
        TipoAsistencia::PUNTUAL => 'puntual',
        TipoAsistencia::ATRASO => 'tarde',
        TipoAsistencia::SIN_MARCAR => 'ausente',
        TipoAsistencia::BAJA_PERMISO => 'permiso',
        TipoAsistencia::VACACION => 'vacaciones',
    ];

    public function __construct(private ClasificadorAsistencia $clasificador)
    {
    }

    // ------------------------------------------------------------------
    // Vinculación de IDs del reloj con empleados
    // ------------------------------------------------------------------

    /**
     * Sugiere el empleado que corresponde al nombre del reloj ("PaolaCalzadilla"),
     * comparándolo con combinaciones ordenadas de sus nombres y apellidos.
     */
    public function sugerirEmpleado(string $nombreReloj, Collection $empleados): ?Empleado
    {
        $objetivo = $this->normalizarNombre($nombreReloj);

        if ($objetivo === '') {
            return null;
        }

        $candidatos = $empleados->filter(function (Empleado $empleado) use ($objetivo) {
            $tokens = array_values(array_filter(explode(' ', Str::lower(Str::ascii($empleado->persona?->nombreCompleto() ?? '')))));
            $tokens = array_map(fn ($t) => preg_replace('/[^a-z]/', '', $t), $tokens);

            return in_array($objetivo, $this->combinaciones($tokens), true);
        });

        if ($candidatos->count() > 1) {
            $candidatos = $candidatos->where('estado', true);
        }

        return $candidatos->count() === 1 ? $candidatos->first() : null;
    }

    /**
     * Concatenaciones de subconjuntos ordenados (1 a 4 tokens) de un nombre.
     *
     * @param  list<string>  $tokens
     * @return list<string>
     */
    private function combinaciones(array $tokens): array
    {
        $resultado = [];
        $n = count($tokens);

        for ($mascara = 1; $mascara < (1 << $n); $mascara++) {
            $partes = [];
            for ($i = 0; $i < $n; $i++) {
                if ($mascara & (1 << $i)) {
                    $partes[] = $tokens[$i];
                }
            }

            if (count($partes) <= 4) {
                $resultado[] = implode('', $partes);
            }
        }

        return $resultado;
    }

    private function normalizarNombre(string $nombre): string
    {
        return preg_replace('/[^a-z]/', '', Str::lower(Str::ascii($nombre)));
    }

    // ------------------------------------------------------------------
    // Importación
    // ------------------------------------------------------------------

    /**
     * @param  array  $reporte  resultado de LectorReporteBiometrico::leer()
     * @param  array<string, int|null>  $vinculos  ID del reloj → id del empleado (null = no vincular)
     */
    public function importar(array $reporte, array $vinculos, string $nombreArchivo, ?int $idUsuario): ImportacionBiometrico
    {
        return DB::transaction(function () use ($reporte, $vinculos, $nombreArchivo, $idUsuario) {
            $this->guardarVinculos($vinculos);

            $inicio = Carbon::parse($reporte['periodo_inicio']);
            $fin = Carbon::parse($reporte['periodo_fin']);

            $importacion = ImportacionBiometrico::create([
                'nombre_archivo' => $nombreArchivo,
                'periodo_inicio' => $inicio,
                'periodo_fin' => $fin,
                'id_usuario' => $idUsuario,
                'estado' => 'procesado',
            ]);

            $empleados = Empleado::whereIn('id', array_filter($vinculos))->get()->keyBy('id');
            $existentes = Asistencia::whereIn('id_empleado', $empleados->keys())
                ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
                ->get()
                ->keyBy(fn ($a) => $a->id_empleado.'|'.$a->fecha->toDateString());

            $gestiones = [];
            $meses = Mes::pluck('id', 'numero');
            $importados = [];
            $noVinculados = 0;

            foreach ($reporte['empleados'] as $bloque) {
                $empleado = $empleados[$vinculos[$bloque['codigo']] ?? 0] ?? null;

                if (! $empleado) {
                    $noVinculados++;

                    continue;
                }

                $tieneMarcas = collect($bloque['dias'])->contains(fn ($crudo) => $crudo !== '');

                // Un empleado inactivo solo se evalúa si marcó algún día del periodo.
                if (! $empleado->estado && ! $tieneMarcas) {
                    continue;
                }

                foreach ($bloque['dias'] as $fecha => $crudo) {
                    $dia = Carbon::parse($fecha);

                    if ($dia->isWeekend() || ! $this->esEvaluable($empleado, $dia)) {
                        continue;
                    }

                    $datos = [
                        'marcaciones_crudas' => $crudo !== '' ? $crudo : null,
                        'marcaciones' => ClasificadorAsistencia::separar($crudo),
                        'origen' => 'biometrico',
                        'id_importacion' => $importacion->id,
                    ];

                    $fila = $existentes[$empleado->id.'|'.$fecha] ?? null;

                    if ($fila) {
                        // No se pisan los tipos asignados a mano (tipo_asignado_manual).
                        $fila->update($datos);
                    } else {
                        $gestiones[$dia->year] ??= Gestion::firstOrCreate(
                            ['anio' => $dia->year],
                            ['fecha_inicio' => $dia->copy()->startOfYear(), 'fecha_fin' => $dia->copy()->endOfYear(), 'estado' => true]
                        )->id;

                        Asistencia::create($datos + [
                            'id_empleado' => $empleado->id,
                            'id_gestion' => $gestiones[$dia->year],
                            'id_mes' => $meses[$dia->month],
                            'fecha' => $fecha,
                            'id_tipo_asistencia' => TipoAsistencia::idDe(TipoAsistencia::SIN_MARCAR),
                            'estado' => true,
                        ]);
                    }
                }

                $importados[] = $empleado->id;
            }

            $importacion->update([
                'total_empleados' => count($importados),
                'total_no_vinculados' => $noVinculados,
            ]);

            $this->recalcular($inicio, $fin, $importados);

            return $importacion;
        });
    }

    /**
     * @param  array<string, int|null>  $vinculos
     */
    private function guardarVinculos(array $vinculos): void
    {
        foreach ($vinculos as $codigo => $idEmpleado) {
            if (! $idEmpleado) {
                continue;
            }

            Empleado::where('codigo_biometrico', $codigo)->where('id', '!=', $idEmpleado)->update(['codigo_biometrico' => null]);
            Empleado::whereKey($idEmpleado)->update(['codigo_biometrico' => (string) $codigo]);
        }
    }

    public function esEvaluable(Empleado $empleado, Carbon $dia): bool
    {
        return $dia->gte($empleado->fecha_ingreso->copy()->startOfDay())
            && (! $empleado->fecha_baja || $dia->lte($empleado->fecha_baja->copy()->startOfDay()));
    }

    // ------------------------------------------------------------------
    // Recalcular clasificación, eventos y multa
    // ------------------------------------------------------------------

    /**
     * Reclasifica los días y renumera los eventos sancionables. Siempre trabaja
     * con meses completos, porque la multa se calcula por mes.
     *
     * @param  list<int>|null  $idsEmpleados
     */
    public function recalcular(Carbon $desde, Carbon $hasta, ?array $idsEmpleados = null): void
    {
        $desde = $desde->copy()->startOfMonth();
        $hasta = $hasta->copy()->endOfMonth();

        $filas = Asistencia::with('tipoAsistencia')
            ->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->when($idsEmpleados !== null, fn ($q) => $q->whereIn('id_empleado', $idsEmpleados))
            ->orderBy('fecha')
            ->get();

        if ($filas->isEmpty()) {
            return;
        }

        $ids = $filas->pluck('id_empleado')->unique()->values();
        $empleados = Empleado::with(['turnos' => fn ($q) => $q->wherePivot('estado', true)])->whereIn('id', $ids)->get()->keyBy('id');
        $contexto = $this->cargarContexto($desde, $hasta, $ids->all());
        $tipos = TipoAsistencia::porCodigo();
        $estados = Tipo::categoria('estado_asistencia')->pluck('id', 'codigo');

        DB::transaction(function () use ($filas, $empleados, $contexto, $tipos, $estados) {
            foreach ($filas as $fila) {
                $empleado = $empleados[$fila->id_empleado];
                $this->reclasificar($fila, $empleado, $contexto, $tipos, $estados);
            }

            foreach ($filas->groupBy(fn ($f) => $f->id_empleado.'|'.$f->fecha->format('Y-m')) as $delMes) {
                $this->numerarEventos($delMes);
            }
        });
    }

    private function reclasificar(Asistencia $fila, Empleado $empleado, array $contexto, Collection $tipos, Collection $estados): void
    {
        $fecha = $fila->fecha->toDateString();
        $clave = $empleado->id.'|'.$fecha;

        if ($fila->origen === 'biometrico') {
            $resultado = $this->clasificador->clasificar([
                'evaluable' => $this->esEvaluable($empleado, $fila->fecha),
                'tipo_manual' => $fila->tipo_asignado_manual ? $fila->tipoAsistencia?->codigo : null,
                'feriado' => isset($contexto['feriados'][$fecha]),
                'permiso' => $this->permisoDelDia($contexto['permisos'][$empleado->id] ?? [], $fecha),
                'remoto' => isset($contexto['remotos'][$clave]),
                'marcas' => ClasificadorAsistencia::separar($fila->marcaciones_crudas),
                'hora_limite' => $this->horaLimite($empleado),
            ]);

            $codigo = $resultado['codigo'];
            $fila->fill([
                'id_tipo_asistencia' => $tipos[$codigo]->id,
                'id_estado_asistencia' => $estados[self::ESTADO_EQUIVALENTE[$codigo] ?? ''] ?? null,
                'marcaciones' => $resultado['marcas'],
                'minutos_atraso' => $codigo === TipoAsistencia::ATRASO ? $resultado['minutos_atraso'] : 0,
                'hora_entrada' => $resultado['entrada'] ? "{$fecha} {$resultado['entrada']}" : null,
                'hora_salida' => $resultado['salida'] ? "{$fecha} {$resultado['salida']}" : null,
                'horas_trabajadas' => $resultado['entrada'] && $resultado['salida']
                    ? round((ClasificadorAsistencia::minutos($resultado['salida']) - ClasificadorAsistencia::minutos($resultado['entrada'])) / 60, 2)
                    : 0,
            ]);
        } else {
            // Registro del CRUD de marcajes: se respeta el estado elegido.
            $codigo = $fila->tipoAsistencia?->codigo ?? TipoAsistencia::PUNTUAL;
            $minutos = $codigo === TipoAsistencia::ATRASO && $fila->hora_entrada
                ? max(0, ClasificadorAsistencia::minutos($fila->hora_entrada->format('H:i')) - $this->horaLimite($empleado))
                : 0;
            $fila->fill(['minutos_atraso' => $minutos]);
        }

        $fila->es_sancionable = (bool) $tipos[$codigo]->es_sancionable;

        if ($fila->isDirty()) {
            $fila->save();
        }
    }

    /**
     * Numera los eventos sancionables del mes: los primeros se perdonan y cada
     * uno de los siguientes suma la multa por evento (§4.3).
     */
    private function numerarEventos(Collection $filasDelMes): void
    {
        $perdonados = (int) config('asistencia.eventos_perdonados');
        $monto = (float) config('asistencia.multa_por_evento');
        $numero = 0;

        foreach ($filasDelMes->sortBy('fecha') as $fila) {
            if ($fila->es_sancionable) {
                $numero++;
                $fila->numero_evento_mes = $numero;
                $fila->multa_dia = $numero > $perdonados ? $monto : 0;
            } else {
                $fila->numero_evento_mes = null;
                $fila->multa_dia = 0;
            }

            if ($fila->isDirty()) {
                $fila->save();
            }
        }
    }

    /**
     * @param  list<int>  $idsEmpleados
     */
    private function cargarContexto(Carbon $desde, Carbon $hasta, array $idsEmpleados): array
    {
        $feriados = Feriado::where('estado', true)
            ->where('aplica_todos', true)
            ->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->pluck('fecha')
            ->mapWithKeys(fn ($f) => [Carbon::parse($f)->toDateString() => true])
            ->all();

        $permisos = PermisoLaboral::with('tipoPermiso')
            ->whereIn('id_empleado', $idsEmpleados)
            ->where('estado', true)
            ->where('estado_solicitud', 'aprobado')
            ->where('fecha_inicio', '<=', $hasta->toDateString())
            ->where('fecha_fin', '>=', $desde->toDateString())
            ->get()
            ->groupBy('id_empleado')
            ->map(fn ($lista) => $lista->map(fn ($p) => [
                'inicio' => Carbon::parse($p->fecha_inicio)->toDateString(),
                'fin' => Carbon::parse($p->fecha_fin)->toDateString(),
                'tipo' => $p->tipoPermiso?->codigo === 'vacaciones' ? 'vacaciones' : 'otro',
            ])->all())
            ->all();

        $remotos = DiaRemoto::whereIn('id_empleado', $idsEmpleados)
            ->where('estado', true)
            ->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->get()
            ->mapWithKeys(fn ($d) => [$d->id_empleado.'|'.$d->fecha->toDateString() => true])
            ->all();

        return compact('feriados', 'permisos', 'remotos');
    }

    private function permisoDelDia(array $permisos, string $fecha): ?string
    {
        foreach ($permisos as $permiso) {
            if ($fecha >= $permiso['inicio'] && $fecha <= $permiso['fin']) {
                return $permiso['tipo'];
            }
        }

        return null;
    }

    /** Hora límite de entrada en minutos: entrada del turno + tolerancia (§4.3). */
    public function horaLimite(Empleado $empleado): int
    {
        $turno = $empleado->relationLoaded('turnos') ? $empleado->turnos->first() : $empleado->turnos()->wherePivot('estado', true)->first();

        if ($turno && $turno->hora_entrada) {
            return ClasificadorAsistencia::minutos((string) $turno->hora_entrada) + (int) $turno->tolerancia_minutos;
        }

        return ClasificadorAsistencia::minutos(config('asistencia.hora_entrada')) + (int) config('asistencia.tolerancia_minutos');
    }

    // ------------------------------------------------------------------
    // Cambios manuales y días remotos
    // ------------------------------------------------------------------

    /**
     * Asigna un tipo a mano desde la matriz. null = volver a la clasificación automática.
     * REMOTO se registra como día remoto asignado (tabla dias_remotos).
     */
    public function asignarTipo(Asistencia $asistencia, ?string $codigo, int $idUsuario, ?string $observacion = null): void
    {
        DB::transaction(function () use ($asistencia, $codigo, $idUsuario, $observacion) {
            $remoto = DiaRemoto::where('id_empleado', $asistencia->id_empleado)->whereDate('fecha', $asistencia->fecha)->first();

            if ($codigo === TipoAsistencia::REMOTO) {
                DiaRemoto::updateOrCreate(
                    ['id_empleado' => $asistencia->id_empleado, 'fecha' => $asistencia->fecha->toDateString()],
                    ['estado' => true, 'id_asignado_por' => $idUsuario, 'observacion' => $observacion]
                );
                $asistencia->tipo_asignado_manual = false;
            } elseif ($codigo === null) {
                $remoto?->update(['estado' => false]);
                $asistencia->tipo_asignado_manual = false;
            } else {
                $remoto?->update(['estado' => false]);
                $asistencia->tipo_asignado_manual = true;
                $asistencia->id_tipo_asistencia = TipoAsistencia::idDe($codigo);
            }

            $asistencia->id_usuario_modifico = $idUsuario;
            if ($observacion !== null) {
                $asistencia->observaciones = $observacion ?: null;
            }
            $asistencia->save();

            $this->recalcular($asistencia->fecha, $asistencia->fecha, [$asistencia->id_empleado]);
        });
    }

    /**
     * @param  list<string>  $fechas
     * @return int cantidad de días asignados
     */
    public function asignarRemotos(int $idEmpleado, array $fechas, ?string $observacion, int $idUsuario): int
    {
        $fechas = collect($fechas)->map(fn ($f) => Carbon::parse($f))->reject->isWeekend()->unique(fn ($f) => $f->toDateString());

        if ($fechas->isEmpty()) {
            return 0;
        }

        DB::transaction(function () use ($idEmpleado, $fechas, $observacion, $idUsuario) {
            foreach ($fechas as $fecha) {
                DiaRemoto::updateOrCreate(
                    ['id_empleado' => $idEmpleado, 'fecha' => $fecha->toDateString()],
                    ['estado' => true, 'observacion' => $observacion ?: null, 'id_asignado_por' => $idUsuario]
                );
            }

            $this->recalcular($fechas->min(), $fechas->max(), [$idEmpleado]);
        });

        return $fechas->count();
    }

    public function anularRemoto(DiaRemoto $dia): void
    {
        DB::transaction(function () use ($dia) {
            $dia->update(['estado' => false]);
            $this->recalcular($dia->fecha, $dia->fecha, [$dia->id_empleado]);
        });
    }

    // ------------------------------------------------------------------
    // Consultas para la matriz, el resumen y el detalle
    // ------------------------------------------------------------------

    /**
     * @return list<Carbon> días hábiles del mes
     */
    public function diasHabiles(Carbon $mes): array
    {
        $dias = [];
        for ($d = $mes->copy()->startOfMonth(); $d->lte($mes->copy()->endOfMonth()); $d->addDay()) {
            if (! $d->isWeekend()) {
                $dias[] = $d->copy();
            }
        }

        return $dias;
    }

    /**
     * Filas de la matriz del mes: un empleado por fila con sus asistencias por fecha
     * y los totales del mes. Los empleados sin ninguna marca van al final (§4.1).
     */
    public function matriz(Carbon $mes, ?int $idArea = null, ?string $buscar = null, ?int $idEmpresa = null): Collection
    {
        $inicio = $mes->copy()->startOfMonth()->toDateString();
        $fin = $mes->copy()->endOfMonth()->toDateString();

        $asistencias = Asistencia::with('tipoAsistencia')
            ->whereBetween('fecha', [$inicio, $fin])
            ->whereHas('empleado', function ($q) use ($idArea, $buscar, $idEmpresa) {
                $q->when($idArea, fn ($qq) => $qq->where('id_area', $idArea))
                    ->when($idEmpresa, fn ($qq) => $qq->where('id_empresa', $idEmpresa))
                    ->when($buscar, fn ($qq) => $qq->where(function ($b) use ($buscar) {
                        $b->where('codigo_empleado', 'like', "%{$buscar}%")
                            ->orWhere('codigo_biometrico', $buscar)
                            ->orWhereHas('persona', fn ($p) => $p->where('primer_nombre', 'like', "%{$buscar}%")
                                ->orWhere('segundo_nombre', 'like', "%{$buscar}%")
                                ->orWhere('primer_apellido', 'like', "%{$buscar}%")
                                ->orWhere('segundo_apellido', 'like', "%{$buscar}%"));
                    }));
            })
            ->get()
            ->groupBy('id_empleado');

        $empleados = Empleado::with(['persona', 'area', 'empresa'])->whereIn('id', $asistencias->keys())->get();

        return $empleados
            ->map(function (Empleado $empleado) use ($asistencias) {
                $filas = $asistencias[$empleado->id];
                $codigo = fn ($f) => $f->tipoAsistencia?->codigo;

                return [
                    'empleado' => $empleado,
                    'dias' => $filas->keyBy(fn ($f) => $f->fecha->toDateString()),
                    'con_marcas' => $filas->contains(fn ($f) => ! empty($f->marcaciones)),
                    'totales' => [
                        'atrasos' => $filas->filter(fn ($f) => $codigo($f) === TipoAsistencia::ATRASO)->count(),
                        'minutos' => (int) $filas->sum('minutos_atraso'),
                        'incompletos' => $filas->filter(fn ($f) => $codigo($f) === TipoAsistencia::INCOMPLETO)->count(),
                        'sin_marcar' => $filas->filter(fn ($f) => $codigo($f) === TipoAsistencia::SIN_MARCAR)->count(),
                        'eventos' => $filas->where('es_sancionable', true)->count(),
                        'multa' => (float) $filas->sum('multa_dia'),
                    ],
                ];
            })
            ->sortBy([
                fn ($a, $b) => $b['con_marcas'] <=> $a['con_marcas'],
                fn ($a, $b) => strcmp($a['empleado']->persona?->nombreCompleto() ?? '', $b['empleado']->persona?->nombreCompleto() ?? ''),
            ])
            ->values();
    }
}
