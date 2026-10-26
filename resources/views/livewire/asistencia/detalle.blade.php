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
        class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col border-l border-zinc-300 bg-zinc-50 shadow-xl dark:border-zinc-700 dark:bg-zinc-950"
    >
        <div class="flex items-center gap-3 border-b border-zinc-300 bg-white px-5 py-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/15 text-base font-semibold text-primary">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $asistenciaDetalle->empleado?->persona?->nombreCompleto() ?? '—' }}
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $asistenciaDetalle->empleado?->codigo_empleado }} · {{ $asistenciaDetalle->fecha?->format('d/m/Y') }}
                </p>
            </div>
            <span class="rounded bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                {{ $asistenciaDetalle->estadoAsistencia->nombre ?? '—' }}
            </span>
            <button wire:click="cerrarDetalle" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-5">

            {{-- Marcaciones --}}
            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-stopwatch-fill"></i> Marcaciones
                </h3>
                <div class="grid grid-cols-2 gap-3 rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Entrada</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $asistenciaDetalle->hora_entrada?->format('h:i A') ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Salida</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $asistenciaDetalle->hora_salida?->format('h:i A') ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Horas trabajadas</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $asistenciaDetalle->horas_trabajadas ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Horas extra</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $asistenciaDetalle->horas_extra ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Minutos de atraso</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $asistenciaDetalle->minutos_atraso ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Tipo de asistencia</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $asistenciaDetalle->tipoAsistencia->nombre ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Otros datos --}}
            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-info-circle-fill"></i> Otros datos
                </h3>
                <div class="grid grid-cols-2 gap-3 rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Origen</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ ucfirst($asistenciaDetalle->origen ?? '—') }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Sancionable</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $asistenciaDetalle->es_sancionable ? 'Sí' : 'No' }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Observaciones</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $asistenciaDetalle->observaciones ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-zinc-300 bg-white px-5 py-3 dark:border-zinc-700 dark:bg-zinc-900">
            <button wire:click="cerrarDetalle"
                    class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 cursor-pointer">
                Cerrar
            </button>
            <button wire:click="editarDesdeDetalle({{ $asistenciaDetalle->id }})"
                class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700">
                <i class="bi bi-pencil-square text-primary"></i> Editar
            </button>
        </div>
    </div>
</div>
