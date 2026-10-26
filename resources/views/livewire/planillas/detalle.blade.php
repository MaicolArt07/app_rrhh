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
        {{-- Cabecera --}}
        <div class="flex items-center gap-3 border-b border-zinc-300 bg-white px-5 py-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/15 text-base font-semibold text-primary">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $planillaDetalle->gestion->anio ?? '—' }}{{ $planillaDetalle->mes ? ' — ' . $planillaDetalle->mes->nombre : '' }}
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $planillaDetalle->tipoPlanilla->nombre ?? '—' }}
                </p>
            </div>
            <span class="rounded px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                {{ $planillaDetalle->estadoPlanilla->nombre ?? '—' }}
            </span>
            <button wire:click="cerrarDetalle" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-5">

            {{-- Acciones de estado --}}
            @php $codigoEstado = $planillaDetalle->estadoPlanilla->codigo ?? ''; @endphp
            @if (in_array($codigoEstado, ['borrador', 'revisada', 'aprobada']))
                <div class="flex justify-end gap-2">
                    @if ($codigoEstado === 'borrador')
                        <button wire:click="avanzarEstado({{ $planillaDetalle->id }}, 'revisada')"
                                class="rounded bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700 cursor-pointer">Marcar Revisada</button>
                    @elseif ($codigoEstado === 'revisada')
                        <button wire:click="avanzarEstado({{ $planillaDetalle->id }}, 'aprobada')"
                                class="rounded bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700 cursor-pointer">Aprobar</button>
                    @elseif ($codigoEstado === 'aprobada')
                        <button wire:click="avanzarEstado({{ $planillaDetalle->id }}, 'pagada')"
                                class="rounded bg-green-600 px-3 py-2 text-sm text-white hover:bg-green-700 cursor-pointer">Marcar Pagada</button>
                    @endif
                </div>
            @endif

            {{-- Resumen --}}
            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-info-circle-fill"></i> Resumen
                </h3>
                <div class="grid grid-cols-2 gap-3 rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Periodo</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $planillaDetalle->fecha_inicio->format('d/m/Y') }} al {{ $planillaDetalle->fecha_fin->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Total planilla</span>
                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Q {{ number_format($planillaDetalle->total_planilla, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Empleados incluidos</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $planillaDetalle->detalles->count() }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Generada por</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $planillaDetalle->generadoPor->name ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Aprobada por</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $planillaDetalle->aprobadoPor->name ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Empleados de la planilla --}}
            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-people-fill"></i> Empleados
                </h3>
                <div class="overflow-x-auto rounded border border-zinc-300 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                    <table class="min-w-full text-sm">
                        <thead class="bg-zinc-100 dark:bg-zinc-800">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-zinc-700 dark:text-zinc-200">Empleado</th>
                            <th class="px-3 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-200">Líquido</th>
                            <th class="px-3 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-200">Estado</th>
                            <th class="px-3 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-200">Boleta</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($planillaDetalle->detalles as $item)
                            <tr class="border-t border-zinc-200 dark:border-zinc-800">
                                <td class="px-3 py-2 text-zinc-800 dark:text-zinc-100">
                                    {{ $item->empleado->persona?->nombreCompleto() }}
                                    <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $item->empleado->codigo_empleado }}</span>
                                </td>
                                <td class="px-3 py-2 text-center font-semibold text-zinc-800 dark:text-zinc-100">Q {{ number_format($item->sueldo_liquido, 2) }}</td>
                                <td class="px-3 py-2 text-center">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $item->estado_pago === 'pagado' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' }}">
                                        {{ ucfirst($item->estado_pago) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <a href="{{ route('planillas.boleta', $item->id) }}" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">
                                        <i class="bi bi-file-earmark-pdf-fill text-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-3 text-center text-zinc-500 dark:text-zinc-400">Esta planilla no tiene empleados.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
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
