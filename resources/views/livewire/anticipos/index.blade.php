<div class="flex h-full flex-col bg-zinc-200 p-4 dark:bg-zinc-950">

    <div class="mb-3 flex flex-wrap items-center justify-between gap-3 rounded border border-zinc-300 bg-zinc-100 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-900">
        <button wire:click="openModal"
            class="flex items-center gap-2 rounded border border-zinc-400 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
            <i class="bi bi-plus-lg text-primary"></i> Nuevo
        </button>

        <div class="flex flex-1 flex-wrap items-center justify-end gap-2">
            <select wire:model="estadoFiltro"
                class="rounded border border-zinc-400 bg-white px-2 py-1.5 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="pagado">Pagados</option>
                <option value="cancelado">Cancelados</option>
            </select>

            <input type="text" wire:model="search" wire:keydown.enter="buscar"
                class="w-full max-w-xs rounded border border-zinc-400 bg-white px-2 py-1.5 text-sm text-zinc-800 placeholder:text-zinc-400 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                placeholder="Buscar empleado...">

            <button wire:click="buscar"
                class="flex items-center gap-2 rounded border border-zinc-400 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                <i class="bi bi-search"></i> Buscar
            </button>

            @if ($buscado)
                <button wire:click="limpiarFiltros"
                    class="flex items-center gap-2 rounded border border-zinc-400 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                    <i class="bi bi-x-lg"></i> Limpiar
                </button>
            @endif
        </div>
    </div>

    @if (!$buscado)
        <div class="flex flex-1 flex-col items-center justify-center gap-3 rounded border border-dashed border-zinc-400 bg-zinc-100 text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
            <i class="bi bi-funnel text-4xl"></i>
            <p class="text-sm">Selecciona los filtros que necesites y presiona <strong>Buscar</strong> para ver los anticipos.</p>
        </div>
    @else
        <div class="hidden flex-1 overflow-auto rounded border border-zinc-300 dark:border-zinc-700 md:block">
            <table class="min-w-full border-collapse text-sm">
                <thead class="sticky top-0 bg-zinc-300 dark:bg-zinc-800">
                <tr>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-left font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Empleado</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-left font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Tipo</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-right font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Monto Total</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-right font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Cuota</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-right font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Saldo Pendiente</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Estado</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Opciones</th>
                </tr>
                </thead>

                <tbody class="bg-white dark:bg-zinc-900">
                @forelse ($anticipos as $item)
                    <tr wire:click="verDetalle({{ $item->id }})"
                        class="cursor-pointer border-b border-zinc-200 hover:bg-zinc-100 dark:border-zinc-800 dark:hover:bg-zinc-800">
                        <td class="px-3 py-1.5 text-zinc-800 dark:text-zinc-100">
                            {{ $item->empleado->persona?->nombreCompleto() }}
                            <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $item->empleado->codigo_empleado }}</span>
                        </td>
                        <td class="px-3 py-1.5 text-zinc-800 dark:text-zinc-100">{{ $item->tipoAnticipo->nombre ?? '—' }}</td>
                        <td class="px-3 py-1.5 text-right text-zinc-800 dark:text-zinc-100">Q {{ number_format($item->monto_total, 2) }}</td>
                        <td class="px-3 py-1.5 text-right text-zinc-800 dark:text-zinc-100">Q {{ number_format($item->monto_cuota, 2) }} x {{ $item->numero_cuotas }}</td>
                        <td class="px-3 py-1.5 text-right font-semibold text-zinc-800 dark:text-zinc-100">Q {{ number_format($item->saldo_pendiente, 2) }}</td>
                        <td class="px-3 py-1.5 text-center">
                            @php
                                $badge = match($item->estado_anticipo) {
                                    'pagado' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                    'cancelado' => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                                    default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                };
                            @endphp
                            <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $badge }}">{{ ucfirst($item->estado_anticipo) }}</span>
                        </td>
                        <td class="px-3 py-1.5 text-center" onclick="event.stopPropagation()">
                            @if ($item->estado_anticipo === 'activo')
                                <div class="flex justify-center gap-3">
                                    <button wire:click="abrirPago({{ $item->id }})"
                                            class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer" title="Registrar pago">
                                        <i class="bi bi-cash-coin"></i>
                                    </button>
                                    <button wire:click="cancelar({{ $item->id }})"
                                            class="text-zinc-500 transition hover:text-red-600 dark:text-zinc-400 cursor-pointer" title="Cancelar">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-3 text-center text-zinc-500 dark:text-zinc-400">No se encontraron anticipos con esos filtros.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-2 md:hidden">
            @forelse ($anticipos as $item)
                <div x-data="{ open: false }"
                     class="rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center gap-2">
                        <button type="button" x-on:click.stop="open = !open"
                            class="flex h-6 w-6 shrink-0 cursor-pointer items-center justify-center rounded-full border border-primary text-primary transition hover:bg-primary hover:text-white">
                            <i class="bi text-xs" :class="open ? 'bi-dash' : 'bi-plus'"></i>
                        </button>

                        <div wire:click="verDetalle({{ $item->id }})" class="min-w-0 flex-1 cursor-pointer">
                            <p class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $item->empleado->persona?->nombreCompleto() }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $item->empleado->codigo_empleado }}</p>
                        </div>

                        @if ($item->estado_anticipo === 'activo')
                            <div class="flex shrink-0 gap-3" onclick="event.stopPropagation()">
                                <button wire:click="abrirPago({{ $item->id }})"
                                        class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer" title="Registrar pago">
                                    <i class="bi bi-cash-coin"></i>
                                </button>
                                <button wire:click="cancelar({{ $item->id }})"
                                        class="text-zinc-500 transition hover:text-red-600 dark:text-zinc-400 cursor-pointer" title="Cancelar">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        @endif
                    </div>

                    <div x-show="open" x-collapse x-cloak onclick="event.stopPropagation()"
                         class="mt-2 grid grid-cols-2 gap-2 border-t border-zinc-200 pt-2 pl-8 dark:border-zinc-800">
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Tipo</span>
                            <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $item->tipoAnticipo->nombre ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Monto Total</span>
                            <p class="text-sm text-zinc-800 dark:text-zinc-100">Q {{ number_format($item->monto_total, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Cuota</span>
                            <p class="text-sm text-zinc-800 dark:text-zinc-100">Q {{ number_format($item->monto_cuota, 2) }} x {{ $item->numero_cuotas }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Saldo Pendiente</span>
                            <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Q {{ number_format($item->saldo_pendiente, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Estado</span>
                            <p class="text-sm">
                                @php
                                    $badge = match($item->estado_anticipo) {
                                        'pagado' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        'cancelado' => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                                        default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                    };
                                @endphp
                                <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $badge }}">{{ ucfirst($item->estado_anticipo) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded border border-zinc-300 bg-white p-4 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                    No se encontraron anticipos con esos filtros.
                </div>
            @endforelse
        </div>

        <div class="mt-3 dark:text-zinc-200">
            {{ $anticipos->links() }}
        </div>
    @endif

    @if ($modalVisible)
        @include('livewire.anticipos.modal.index')
    @endif

    @if ($pagoVisible)
        @include('livewire.anticipos.modal.pago')
    @endif

    @if ($viewingId && $anticipoDetalle)
        @include('livewire.anticipos.detalle')
    @endif

</div>
