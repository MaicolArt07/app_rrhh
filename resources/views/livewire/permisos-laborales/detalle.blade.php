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
                    $iniciales = collect([$permisoDetalle->empleado?->persona?->primer_nombre, $permisoDetalle->empleado?->persona?->primer_apellido])
                        ->filter()
                        ->map(fn ($n) => mb_strtoupper(mb_substr($n, 0, 1)))
                        ->implode('');
                @endphp
                @if ($iniciales !== '')
                    {{ $iniciales }}
                @else
                    <i class="bi bi-calendar2-x"></i>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $permisoDetalle->empleado?->persona?->nombreCompleto() ?? '—' }}
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $permisoDetalle->empleado?->codigo_empleado }}
                    @if ($permisoDetalle->tipoPermiso) · {{ $permisoDetalle->tipoPermiso->nombre }} @endif
                </p>
            </div>
            @php
                $badge = match($permisoDetalle->estado_solicitud) {
                    'aprobado' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                    'rechazado' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                    default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                };
            @endphp
            <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $badge }}">{{ ucfirst($permisoDetalle->estado_solicitud) }}</span>
            <button wire:click="cerrarDetalle" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-5">

            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-calendar2-x"></i> Datos del permiso
                </h3>
                <div class="grid grid-cols-2 gap-3 rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Tipo</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $permisoDetalle->tipoPermiso->nombre ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Días solicitados</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $permisoDetalle->dias_solicitados }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Desde</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $permisoDetalle->fecha_inicio?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Hasta</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $permisoDetalle->fecha_fin?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Goce de sueldo</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $permisoDetalle->goce_sueldo ? 'Sí' : 'No' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Aprobado por</span>
                        <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $permisoDetalle->aprobadoPor?->name ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-primary">
                    <i class="bi bi-paperclip"></i> Documento de soporte
                </h3>
                <div class="rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    @php $media = $permisoDetalle->getFirstMedia('documento_soporte'); @endphp
                    @if ($media)
                        <a href="{{ $media->getUrl() }}" target="_blank" class="text-sm text-primary hover:underline">
                            <i class="bi bi-file-earmark-arrow-down"></i> {{ $media->file_name }}
                        </a>
                    @else
                        <p class="text-sm text-zinc-400">Sin documento adjunto.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-zinc-300 bg-white px-5 py-3 dark:border-zinc-700 dark:bg-zinc-900">
            <button wire:click="cerrarDetalle"
                    class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 cursor-pointer">
                Cerrar
            </button>
            <button wire:click="editarDesdeDetalle({{ $permisoDetalle->id }})"
                class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700">
                <i class="bi bi-pencil-square text-primary"></i> Editar
            </button>
        </div>
    </div>
</div>
