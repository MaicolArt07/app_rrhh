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
        @php
            $situacion = $contratoDetalle->situacion();
            $dias = $contratoDetalle->diasParaVencer();
        @endphp

        <div class="flex items-center gap-3 border-b border-zinc-300 bg-white px-5 py-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/15 text-base font-semibold text-primary">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $contratoDetalle->empleado?->persona?->nombreCompleto() ?? '—' }}
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $contratoDetalle->etiquetaNumero() }} {{ $contratoDetalle->tipoContrato?->nombre }}
                    @if ($contratoDetalle->empresa) · {{ $contratoDetalle->empresa->nombre }} @endif
                </p>
            </div>
            <span class="rounded px-2 py-0.5 text-xs font-semibold {{ match ($situacion) {
                'Vigente' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                'Finalizado' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                default => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
            } }}">
                {{ $situacion }}
            </span>
            <button wire:click="cerrarDetalle" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-5">

            {{-- Datos del contrato --}}
            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-file-earmark-text-fill"></i> Datos del contrato
                </h3>
                <div class="grid grid-cols-2 gap-3 rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Número</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $contratoDetalle->etiquetaNumero() }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Tipo</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $contratoDetalle->tipoContrato?->nombre ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Fecha inicio</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $contratoDetalle->fecha_inicio?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Fecha fin</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">
                            {{ $contratoDetalle->fecha_fin?->format('d/m/Y') ?? ($contratoDetalle->esIndefinido() ? 'Indefinido' : 'Sin fecha') }}
                            @if ($contratoDetalle->vencido())
                                <span class="block text-xs font-semibold text-red-600 dark:text-red-400">Vencido hace {{ abs($dias) }} día(s)</span>
                            @elseif ($contratoDetalle->porVencer())
                                <span class="block text-xs font-semibold text-yellow-700 dark:text-yellow-400">Vence en {{ $dias }} día(s)</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Cargo</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $contratoDetalle->cargo ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Monto</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $contratoDetalle->monto !== null ? 'Q '.number_format($contratoDetalle->monto, 2) : '—' }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Dependencia</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $contratoDetalle->dependencia ?? '—' }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Documento adjunto</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">
                            @if ($contratoDetalle->getFirstMediaUrl('documento'))
                                <a href="{{ $contratoDetalle->getFirstMediaUrl('documento') }}" target="_blank" class="text-primary hover:underline">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> Ver documento
                                </a>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Empleado --}}
            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-person-vcard-fill"></i> Empleado
                </h3>
                <div class="grid grid-cols-2 gap-3 rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Código</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $contratoDetalle->empleado->codigo_empleado ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Documento</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">
                            {{ $contratoDetalle->empleado?->persona?->tipoDocumento?->nombre }} {{ $contratoDetalle->empleado?->persona?->numero_documento ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Historial --}}
            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-clock-history"></i> Historial
                </h3>
                <div class="grid grid-cols-2 gap-3 rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Contrato anterior</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">
                            {{ $contratoDetalle->contratoAnterior ? $contratoDetalle->contratoAnterior->etiquetaNumero().' '.($contratoDetalle->contratoAnterior->tipoContrato?->nombre ?? '') : '—' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Contrato siguiente</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">
                            {{ $contratoDetalle->contratoSiguiente ? $contratoDetalle->contratoSiguiente->etiquetaNumero().' '.($contratoDetalle->contratoSiguiente->tipoContrato?->nombre ?? '') : '—' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-zinc-300 bg-white px-5 py-3 dark:border-zinc-700 dark:bg-zinc-900">
            <button wire:click="cerrarDetalle"
                    class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 cursor-pointer">
                Cerrar
            </button>
            <button wire:click="editarDesdeDetalle({{ $contratoDetalle->id }})"
                class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700">
                <i class="bi bi-pencil-square text-primary"></i> Editar
            </button>
        </div>
    </div>
</div>
