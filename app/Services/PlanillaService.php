<?php

namespace App\Services;

use App\Models\ConceptoPago;
use App\Models\Empleado;
use App\Models\HoraExtraDetalle;
use App\Models\Planilla;
use App\Models\PlanillaDetalle;
use App\Models\Tipo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PlanillaService
{
    public function generar(array $datos): Planilla
    {
        return DB::transaction(function () use ($datos) {
            $estadoBorrador = Tipo::categoria('estado_planilla')->where('codigo', 'borrador')->firstOrFail();

            $planilla = Planilla::create([
                'id_gestion' => $datos['id_gestion'],
                'id_mes' => $datos['id_mes'] ?? null,
                'fecha_inicio' => $datos['fecha_inicio'],
                'fecha_fin' => $datos['fecha_fin'],
                'id_tipo_planilla' => $datos['id_tipo_planilla'],
                'id_estado_planilla' => $estadoBorrador->id,
                'id_generado_por' => $datos['id_generado_por'],
                'total_planilla' => 0,
            ]);

            $fechaInicio = Carbon::parse($datos['fecha_inicio']);
            $fechaFin = Carbon::parse($datos['fecha_fin']);

            $conceptosFijos = ConceptoPago::where('es_fijo', true)->where('estado', true)->get();

            $empleados = Empleado::where('estado', true)
                ->where('fecha_ingreso', '<=', $fechaFin)
                ->where(function ($q) use ($fechaInicio) {
                    $q->whereNull('fecha_baja')->orWhere('fecha_baja', '>=', $fechaInicio);
                })
                ->get();

            $totalPlanilla = 0;

            foreach ($empleados as $empleado) {
                $horasExtraPendientes = HoraExtraDetalle::where('id_empleado', $empleado->id)
                    ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                    ->whereNull('id_planilla_detalle')
                    ->where('estado', true)
                    ->get();

                $montoHorasExtra = round($horasExtraPendientes->sum('monto_calculado'), 2);

                $diasTrabajados = $empleado->asistencias()
                    ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                    ->whereHas('estadoAsistencia', fn ($q) => $q->whereIn('codigo', ['puntual', 'tarde']))
                    ->count();

                if ($diasTrabajados === 0) {
                    $diasTrabajados = $fechaInicio->diffInDays($fechaFin) + 1;
                }

                $sueldoBase = (float) $empleado->sueldo_actual;
                $totalIngresos = $sueldoBase + $montoHorasExtra;
                $totalDeducciones = 0;

                $conceptosParaDetalle = [];
                foreach ($conceptosFijos as $concepto) {
                    $monto = $concepto->es_porcentaje
                        ? round($sueldoBase * ((float) $concepto->valor_por_defecto / 100), 2)
                        : (float) ($concepto->valor_por_defecto ?? 0);

                    if ($monto <= 0) {
                        continue;
                    }

                    if ($concepto->tipo_concepto === 'ingreso') {
                        $totalIngresos += $monto;
                    } else {
                        $totalDeducciones += $monto;
                    }

                    $conceptosParaDetalle[] = ['id_concepto_pago' => $concepto->id, 'monto' => $monto];
                }

                $sueldoLiquido = round($totalIngresos - $totalDeducciones, 2);

                $detalle = PlanillaDetalle::create([
                    'id_planilla' => $planilla->id,
                    'id_empleado' => $empleado->id,
                    'id_cargo' => $empleado->id_cargo,
                    'sueldo_base' => $sueldoBase,
                    'dias_trabajados' => $diasTrabajados,
                    'horas_extra' => $horasExtraPendientes->sum('horas'),
                    'total_ingresos' => round($totalIngresos, 2),
                    'total_deducciones' => round($totalDeducciones, 2),
                    'sueldo_liquido' => $sueldoLiquido,
                    'total_pagado' => 0,
                    'estado_pago' => 'pendiente',
                ]);

                foreach ($conceptosParaDetalle as $c) {
                    $detalle->conceptos()->create($c);
                }

                HoraExtraDetalle::whereIn('id', $horasExtraPendientes->pluck('id'))
                    ->update(['id_planilla_detalle' => $detalle->id]);

                $totalPlanilla += $sueldoLiquido;
            }

            $planilla->update(['total_planilla' => round($totalPlanilla, 2)]);

            return $planilla->fresh('detalles');
        });
    }

    public function avanzarEstado(Planilla $planilla, string $codigoEstado): Planilla
    {
        $tipo = Tipo::categoria('estado_planilla')->where('codigo', $codigoEstado)->firstOrFail();
        $planilla->update(['id_estado_planilla' => $tipo->id]);

        if ($codigoEstado === 'pagada') {
            foreach ($planilla->detalles as $detalle) {
                $detalle->update([
                    'estado_pago' => 'pagado',
                    'fecha_pago' => now(),
                    'metodo_pago' => $detalle->metodo_pago ?? 'transferencia',
                    'total_pagado' => $detalle->sueldo_liquido,
                ]);
            }

            app(NotificacionService::class)->notificarUsuariosConPermiso(
                'Ver Planillas',
                'planilla_lista',
                'Planilla pagada',
                "La planilla del periodo {$planilla->fecha_inicio->format('d/m/Y')} al {$planilla->fecha_fin->format('d/m/Y')} fue marcada como pagada.",
                $planilla
            );
        }

        return $planilla->fresh();
    }
}
