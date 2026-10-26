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
        {{-- Cabecera con avatar: estilo propio, distinto al panel de edición --}}
        <div class="flex items-center gap-3 border-b border-zinc-300 bg-white px-5 py-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/15 text-base font-semibold text-primary">
                @php
                    $iniciales = collect([$empleadoDetalle->persona?->primer_nombre, $empleadoDetalle->persona?->primer_apellido])
                        ->filter()
                        ->map(fn ($n) => mb_strtoupper(mb_substr($n, 0, 1)))
                        ->implode('');
                @endphp
                {{ $iniciales !== '' ? $iniciales : mb_strtoupper(mb_substr($empleadoDetalle->codigo_empleado, 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $empleadoDetalle->persona?->nombreCompleto() ?? '—' }}
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $empleadoDetalle->codigo_empleado }}
                    @if ($empleadoDetalle->empresa) · {{ $empleadoDetalle->empresa->nombre }} @endif
                </p>
            </div>
            <span class="rounded px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                {{ $empleadoDetalle->estadoEmpleado->nombre ?? '—' }}
            </span>
            <button wire:click="cerrarDetalle" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-5">

            {{-- Datos laborales --}}
            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-briefcase-fill"></i> Datos laborales
                </h3>
                <div class="grid grid-cols-2 gap-3 rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Cargo</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->cargo->nombre ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Área</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->area->nombre ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Fecha de ingreso</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->fecha_ingreso?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Fecha de baja</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->fecha_baja?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Sueldo actual</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">Q {{ number_format($empleadoDetalle->sueldo_actual, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Código biométrico</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->codigo_biometrico ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Email corporativo</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->email_corporativo ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Banco / cuenta</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">
                            {{ $empleadoDetalle->banco ?? '—' }}
                            @if ($empleadoDetalle->numero_cuenta_bancaria) · {{ $empleadoDetalle->numero_cuenta_bancaria }} @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Contrato vigente --}}
            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-file-earmark-text-fill"></i> Contrato vigente
                </h3>
                <div class="rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    @if ($empleadoDetalle->contratoVigente)
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">Número</span>
                                <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->contratoVigente->etiquetaNumero() }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">Tipo</span>
                                <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->contratoVigente->tipoContrato?->nombre ?? '—' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-zinc-400">Sin contrato vigente.</p>
                    @endif
                </div>
            </div>

            {{-- Datos personales --}}
            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-person-vcard-fill"></i> Datos personales
                </h3>
                <div class="grid grid-cols-2 gap-3 rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Documento</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">
                            {{ $empleadoDetalle->persona?->tipoDocumento?->nombre }} {{ $empleadoDetalle->persona?->numero_documento ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Fecha de nacimiento</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->persona?->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Género</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ ucfirst($empleadoDetalle->persona?->genero ?? '—') }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Estado civil</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ ucfirst($empleadoDetalle->persona?->estado_civil ?? '—') }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Profesión</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->persona?->profesion ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Teléfono</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->persona?->telefono ?? '—' }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Email</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->persona?->email ?? '—' }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Dirección</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $empleadoDetalle->persona?->direccion ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-zinc-300 bg-white px-5 py-3 dark:border-zinc-700 dark:bg-zinc-900">
            <button wire:click="cerrarDetalle"
                    class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 cursor-pointer">
                Cerrar
            </button>
            <button wire:click="editarDesdeDetalle({{ $empleadoDetalle->id }})"
                class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700">
                <i class="bi bi-pencil-square text-primary"></i> Editar
            </button>
        </div>
    </div>
</div>
