<div
    x-data="{ open: false }"
    x-init="setTimeout(() => open = true, 10)"
    class="fixed inset-0 z-50"
>
    <div
        x-show="open"
        x-transition.opacity
        class="absolute inset-0 bg-zinc-900/30"
        wire:click="cerrarDetalle"
    ></div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute inset-y-0 right-0 flex w-full max-w-lg flex-col border-l border-zinc-300 bg-zinc-50 shadow-xl dark:border-zinc-700 dark:bg-zinc-950"
    >
        <div class="flex items-center gap-3 border-b border-zinc-300 bg-white px-5 py-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/15 text-base font-semibold text-primary">
                @php
                    $iniciales = collect([$liquidacionDetalle->empleado?->persona?->primer_nombre, $liquidacionDetalle->empleado?->persona?->primer_apellido])
                        ->filter()
                        ->map(fn ($n) => mb_strtoupper(mb_substr($n, 0, 1)))
                        ->implode('');
                @endphp
                @if ($iniciales !== '')
                    {{ $iniciales }}
                @else
                    <i class="bi bi-calculator-fill"></i>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $liquidacionDetalle->empleado?->persona?->nombreCompleto() ?? '—' }}
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $liquidacionDetalle->empleado?->codigo_empleado }}
                    @if ($liquidacionDetalle->tipoLiquidacion) · {{ $liquidacionDetalle->tipoLiquidacion->nombre }} @endif
                </p>
            </div>
            <span class="rounded px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                Q {{ number_format($liquidacionDetalle->total_liquidacion, 2) }}
            </span>
            <button wire:click="cerrarDetalle" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-5">

            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-calculator-fill"></i> Datos generales
                </h3>
                <div class="grid grid-cols-2 gap-3 rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Tipo</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $liquidacionDetalle->tipoLiquidacion->nombre ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Fecha de liquidación</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $liquidacionDetalle->fecha_liquidacion?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Tiempo laborado (años)</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $liquidacionDetalle->tiempo_laborado_anios }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Salario promedio</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">Q {{ number_format($liquidacionDetalle->salario_promedio, 2) }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Observaciones</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $liquidacionDetalle->observaciones ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-cash-stack"></i> Desglose
                </h3>
                <div class="grid grid-cols-2 gap-3 rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Indemnización</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">Q {{ number_format($liquidacionDetalle->monto_indemnizacion, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Vacaciones pendientes</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">Q {{ number_format($liquidacionDetalle->monto_vacaciones_pendientes, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Aguinaldo proporcional</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">Q {{ number_format($liquidacionDetalle->monto_aguinaldo_proporcional, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Bono 14 proporcional</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">Q {{ number_format($liquidacionDetalle->monto_bono14_proporcional, 2) }}</p>
                    </div>
                    <div class="col-span-2 border-t border-zinc-200 pt-2 dark:border-zinc-800">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Total liquidación</span>
                        <p class="text-base font-semibold text-zinc-800 dark:text-zinc-100">Q {{ number_format($liquidacionDetalle->total_liquidacion, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-zinc-300 bg-white px-5 py-3 dark:border-zinc-700 dark:bg-zinc-900">
            <button wire:click="cerrarDetalle"
                    class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 cursor-pointer">
                Cerrar
            </button>
        </div>
    </div>
</div>
