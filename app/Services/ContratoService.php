<?php

namespace App\Services;

use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Tipo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Reglas del historial de contratos:
 * - Un contrato nuevo finaliza el vigente del empleado y lo enlaza como anterior; nunca se borra.
 * - Se numeran por tipo (1.º, 2.º contrato por prestación…).
 * - No se permite un tercer contrato por prestación de servicios (Ministerio de Trabajo).
 */
class ContratoService
{
    public function __construct(private NotificacionService $notificaciones)
    {
    }

    public function vigente(int $idEmpleado, ?int $excepto = null): ?Contrato
    {
        return Contrato::with('tipoContrato')
            ->where('id_empleado', $idEmpleado)
            ->where('estado', true)
            ->when($excepto, fn ($q) => $q->where('id', '!=', $excepto))
            ->orderByDesc('fecha_inicio')
            ->first();
    }

    public function cantidadPrestaciones(int $idEmpleado, ?int $excepto = null): int
    {
        return Contrato::where('id_empleado', $idEmpleado)
            ->whereHas('tipoContrato', fn ($q) => $q->where('codigo', 'prestacion'))
            ->when($excepto, fn ($q) => $q->where('id', '!=', $excepto))
            ->count();
    }

    public function puedeRenovarPrestacion(int $idEmpleado): bool
    {
        return $this->cantidadPrestaciones($idEmpleado) < Contrato::MAX_PRESTACION;
    }

    /**
     * Registra un contrato nuevo y deja el vigente como historial.
     *
     * @throws ValidationException
     */
    public function registrar(array $datos): Contrato
    {
        return DB::transaction(function () use ($datos) {
            $tipo = Tipo::findOrFail($datos['id_tipo_contrato']);
            $this->validarLimite((int) $datos['id_empleado'], $tipo);

            $inicio = Carbon::parse($datos['fecha_inicio']);
            $anterior = $this->vigente((int) $datos['id_empleado']);

            if ($anterior) {
                if ($inicio->lte($anterior->fecha_inicio)) {
                    throw ValidationException::withMessages([
                        'form.fecha_inicio' => "Debe ser posterior al inicio del contrato vigente ({$anterior->fecha_inicio->format('d/m/Y')}).",
                    ]);
                }

                $finAnterior = $inicio->copy()->subDay();
                $anterior->update([
                    'estado' => false,
                    'fecha_fin' => $anterior->fecha_fin && $anterior->fecha_fin->lt($finAnterior) ? $anterior->fecha_fin : $finAnterior,
                ]);
            }

            $contrato = Contrato::create([
                'id_empleado' => $datos['id_empleado'],
                'id_empresa' => ($datos['id_empresa'] ?? null) ?: Empleado::whereKey($datos['id_empleado'])->value('id_empresa'),
                'id_tipo_contrato' => $tipo->id,
                'numero_contrato' => $this->siguienteNumero((int) $datos['id_empleado'], $tipo->id),
                // Cargo y monto pactados al firmar (se imprimen en el contrato).
                'cargo' => ($datos['cargo'] ?? null) ?: Empleado::with('cargo')->find($datos['id_empleado'])?->cargo?->nombre,
                'monto' => ($datos['monto'] ?? null) ?: (Empleado::whereKey($datos['id_empleado'])->value('sueldo_actual') ?: null),
                'id_contrato_anterior' => $anterior?->id,
                'fecha_inicio' => $inicio->toDateString(),
                'fecha_fin' => $datos['fecha_fin'] ?: null,
                'observaciones' => $datos['observaciones'] ?: null,
                'notificado_vencimiento' => false,
                'estado' => true,
            ]);

            if ($contrato->load('tipoContrato', 'empleado.persona')->esUltimaPrestacion()) {
                $this->notificarSegundoContrato($contrato);
            }

            return $contrato;
        });
    }

    /**
     * @throws ValidationException
     */
    public function actualizar(Contrato $contrato, array $datos): Contrato
    {
        return DB::transaction(function () use ($contrato, $datos) {
            $tipo = Tipo::findOrFail($datos['id_tipo_contrato']);

            if ($tipo->id !== $contrato->id_tipo_contrato) {
                $this->validarLimite($contrato->id_empleado, $tipo, $contrato->id);
            }

            $fechaFin = $datos['fecha_fin'] ?: null;
            $cambioFin = $fechaFin !== $contrato->fecha_fin?->toDateString();

            $contrato->update([
                'id_empresa' => ($datos['id_empresa'] ?? null) ?: $contrato->id_empresa,
                'id_tipo_contrato' => $tipo->id,
                'fecha_inicio' => $datos['fecha_inicio'],
                'fecha_fin' => $fechaFin,
                'observaciones' => $datos['observaciones'] ?: null,
                // Si cambia la fecha fin, se vuelve a avisar del nuevo vencimiento.
                'notificado_vencimiento' => $cambioFin ? false : $contrato->notificado_vencimiento,
            ]);

            $this->renumerar($contrato->id_empleado);

            return $contrato->refresh();
        });
    }

    /**
     * @throws ValidationException
     */
    public function validarLimite(int $idEmpleado, Tipo $tipo, ?int $excepto = null): void
    {
        if ($tipo->codigo !== 'prestacion') {
            return;
        }

        if ($this->cantidadPrestaciones($idEmpleado, $excepto) >= Contrato::MAX_PRESTACION) {
            throw ValidationException::withMessages([
                'form.id_tipo_contrato' => 'Este empleado ya tuvo '.Contrato::MAX_PRESTACION.' contratos por prestación de servicios. '
                    .'Según el Ministerio de Trabajo no puede tener un tercero: use «Pasar a indefinido».',
            ]);
        }
    }

    /** Recalcula el número de cada contrato del empleado dentro de su tipo. */
    public function renumerar(int $idEmpleado): void
    {
        $porTipo = [];

        Contrato::where('id_empleado', $idEmpleado)->orderBy('fecha_inicio')->orderBy('id')->get()
            ->each(function (Contrato $c) use (&$porTipo) {
                $porTipo[$c->id_tipo_contrato] = ($porTipo[$c->id_tipo_contrato] ?? 0) + 1;

                if ($c->numero_contrato !== $porTipo[$c->id_tipo_contrato]) {
                    $c->update(['numero_contrato' => $porTipo[$c->id_tipo_contrato]]);
                }
            });
    }

    /**
     * Contratos vigentes que requieren atención: por vencer, vencidos o
     * en su último contrato por prestación permitido.
     */
    public function alertas(): Collection
    {
        return Contrato::with(['empleado.persona', 'tipoContrato'])
            ->where('estado', true)
            ->where(function ($q) {
                $q->where(fn ($qq) => $qq->whereNotNull('fecha_fin')->where('fecha_fin', '<=', now()->addDays(Contrato::DIAS_ALERTA)))
                    ->orWhere(fn ($qq) => $qq->where('numero_contrato', '>=', Contrato::MAX_PRESTACION)
                        ->whereHas('tipoContrato', fn ($t) => $t->where('codigo', 'prestacion')));
            })
            ->orderByRaw('fecha_fin IS NULL, fecha_fin')
            ->get();
    }

    private function siguienteNumero(int $idEmpleado, int $idTipo): int
    {
        return Contrato::where('id_empleado', $idEmpleado)->where('id_tipo_contrato', $idTipo)->count() + 1;
    }

    private function notificarSegundoContrato(Contrato $contrato): void
    {
        $nombre = $contrato->empleado->persona?->nombreCompleto() ?? $contrato->empleado->codigo_empleado;
        $vence = $contrato->fecha_fin ? " (vence el {$contrato->fecha_fin->format('d/m/Y')})" : '';

        $this->notificaciones->notificarUsuariosConPermiso(
            'Ver Contratos',
            'segundo_contrato',
            'Segundo contrato por prestación',
            "{$nombre} inicia su {$contrato->etiquetaNumero()} contrato por prestación de servicios{$vence}. "
                .'Según el Ministerio de Trabajo no puede tener un tercero: al finalizar debe pasar a contrato indefinido.',
            $contrato
        );
    }
}
