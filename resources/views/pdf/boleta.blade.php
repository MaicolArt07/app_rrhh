<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Boleta de Pago</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 16px; margin-bottom: 0; }
        .subtitle { color: #666; margin-top: 2px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; background: #f9f9f9; }
        .info-table td { border: none; padding: 2px 8px 2px 0; }
    </style>
</head>
<body>
    <h1>Boleta de Pago</h1>
    <p class="subtitle">
        {{ $detalle->planilla->gestion->anio }}{{ $detalle->planilla->mes ? ' - ' . $detalle->planilla->mes->nombre : '' }}
        &middot; {{ $detalle->planilla->fecha_inicio->format('d/m/Y') }} al {{ $detalle->planilla->fecha_fin->format('d/m/Y') }}
    </p>

    <table class="info-table">
        <tr>
            <td><strong>Empleado:</strong></td>
            <td>{{ $detalle->empleado->persona?->nombreCompleto() }}</td>
            <td><strong>Código:</strong></td>
            <td>{{ $detalle->empleado->codigo_empleado }}</td>
        </tr>
        <tr>
            <td><strong>Cargo:</strong></td>
            <td>{{ $detalle->cargo->nombre ?? '—' }}</td>
            <td><strong>Días trabajados:</strong></td>
            <td>{{ $detalle->dias_trabajados }}</td>
        </tr>
    </table>

    <table>
        <thead>
        <tr>
            <th>Concepto</th>
            <th class="text-right">Monto</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Sueldo Base</td>
            <td class="text-right">Q {{ number_format($detalle->sueldo_base, 2) }}</td>
        </tr>
        @php
            $montoConceptosIngreso = $detalle->conceptos->filter(fn ($c) => $c->conceptoPago?->tipo_concepto === 'ingreso')->sum('monto');
            $montoHorasExtra = round($detalle->total_ingresos - $detalle->sueldo_base - $montoConceptosIngreso, 2);
        @endphp
        @if ($detalle->horas_extra > 0)
        <tr>
            <td>Horas Extra ({{ $detalle->horas_extra }} h)</td>
            <td class="text-right">Q {{ number_format($montoHorasExtra, 2) }}</td>
        </tr>
        @endif
        @foreach ($detalle->conceptos as $concepto)
        <tr>
            <td>{{ $concepto->conceptoPago->nombre ?? 'Concepto' }} @if($concepto->conceptoPago?->tipo_concepto === 'deduccion') (deducción) @endif</td>
            <td class="text-right">{{ $concepto->conceptoPago?->tipo_concepto === 'deduccion' ? '-' : '' }}Q {{ number_format($concepto->monto, 2) }}</td>
        </tr>
        @endforeach
        <tr>
            <td>Total Ingresos</td>
            <td class="text-right">Q {{ number_format($detalle->total_ingresos, 2) }}</td>
        </tr>
        <tr>
            <td>Total Deducciones</td>
            <td class="text-right">-Q {{ number_format($detalle->total_deducciones, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Sueldo Líquido</td>
            <td class="text-right">Q {{ number_format($detalle->sueldo_liquido, 2) }}</td>
        </tr>
        </tbody>
    </table>

    <p>Estado de pago: <strong>{{ ucfirst($detalle->estado_pago) }}</strong>
        @if ($detalle->fecha_pago) — Pagado el {{ $detalle->fecha_pago->format('d/m/Y') }} @endif
    </p>
</body>
</html>
