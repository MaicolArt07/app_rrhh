<div class="flex h-full flex-col bg-zinc-200 p-4 dark:bg-zinc-950">

    <div class="mb-3 flex flex-wrap items-center justify-between gap-3 rounded border border-zinc-300 bg-zinc-100 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-900">
        <button wire:click="openModal"
            class="flex items-center gap-2 rounded border border-zinc-400 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
            <i class="bi bi-plus-lg text-primary"></i> Nuevo
        </button>

        <div class="flex flex-1 flex-wrap items-center justify-end gap-2">
            <input type="text" wire:model="search" wire:keydown.enter="buscar"
                class="w-full max-w-xs rounded border border-zinc-400 bg-white px-2 py-1.5 text-sm text-zinc-800 placeholder:text-zinc-400 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                placeholder="Buscar turno...">

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
            <p class="text-sm">Selecciona los filtros que necesites y presiona <strong>Buscar</strong> para ver los turnos.</p>
        </div>
    @else
        <div class="hidden flex-1 overflow-auto rounded border border-zinc-300 dark:border-zinc-700 md:block">
            <table class="min-w-full border-collapse text-sm">
                <thead class="sticky top-0 bg-zinc-300 dark:bg-zinc-800">
                <tr>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-left font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Nombre</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Entrada</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Salida</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Tolerancia (min)</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Estado</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Opciones</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @forelse ($turnos as $item)
                    <tr wire:click="verDetalle({{ $item->id }})"
                        class="cursor-pointer border-b border-zinc-200 hover:bg-zinc-100 dark:border-zinc-800 dark:hover:bg-zinc-800">
                        <td class="px-3 py-1.5 text-zinc-800 dark:text-zinc-100">{{ $item->nombre }}</td>
                        <td class="px-3 py-1.5 text-center text-zinc-800 dark:text-zinc-100">{{ \Illuminate\Support\Carbon::parse($item->hora_entrada)->format('h:i A') }}</td>
                        <td class="px-3 py-1.5 text-center text-zinc-800 dark:text-zinc-100">{{ \Illuminate\Support\Carbon::parse($item->hora_salida)->format('h:i A') }}</td>
                        <td class="px-3 py-1.5 text-center text-zinc-800 dark:text-zinc-100">{{ $item->tolerancia_minutos }}</td>
                        <td class="px-3 py-1.5 text-center">
                            <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $item->estado ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                {{ $item->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-3 py-1.5 text-center" onclick="event.stopPropagation()">
                            <div class="flex justify-center gap-3">
                                <button wire:click="openModal({{ $item->id }})"
                                        class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button wire:click="delete({{ $item->id }})"
                                        class="{{ $item->estado ? 'text-zinc-500 hover:text-red-600 dark:text-zinc-400' : 'text-zinc-400 dark:text-zinc-600' }} cursor-pointer">
                                    @if($item->estado)
                                        <i class="bi bi-trash"></i>
                                    @else
                                        <i class="bi bi-x-circle"></i>
                                    @endif
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-3 text-center text-zinc-500 dark:text-zinc-400">No se encontraron turnos con esos filtros.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-2 md:hidden">
            @forelse ($turnos as $item)
                <div x-data="{ open: false }"
                     class="rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center gap-2">
                        <button type="button" x-on:click.stop="open = !open"
                            class="flex h-6 w-6 shrink-0 cursor-pointer items-center justify-center rounded-full border border-primary text-primary transition hover:bg-primary hover:text-white">
                            <i class="bi text-xs" :class="open ? 'bi-dash' : 'bi-plus'"></i>
                        </button>

                        <div wire:click="verDetalle({{ $item->id }})" class="min-w-0 flex-1 cursor-pointer">
                            <p class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $item->nombre }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ \Illuminate\Support\Carbon::parse($item->hora_entrada)->format('h:i A') }} - {{ \Illuminate\Support\Carbon::parse($item->hora_salida)->format('h:i A') }}</p>
                        </div>

                        <div class="flex shrink-0 gap-3" onclick="event.stopPropagation()">
                            <button wire:click="openModal({{ $item->id }})"
                                    class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button wire:click="delete({{ $item->id }})"
                                    class="{{ $item->estado ? 'text-zinc-500 hover:text-red-600 dark:text-zinc-400' : 'text-zinc-400 dark:text-zinc-600' }} cursor-pointer">
                                @if($item->estado)
                                    <i class="bi bi-trash"></i>
                                @else
                                    <i class="bi bi-x-circle"></i>
                                @endif
                            </button>
                        </div>
                    </div>

                    <div x-show="open" x-collapse x-cloak onclick="event.stopPropagation()"
                         class="mt-2 grid grid-cols-2 gap-2 border-t border-zinc-200 pt-2 pl-8 dark:border-zinc-800">
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Tolerancia (min)</span>
                            <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $item->tolerancia_minutos }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Estado</span>
                            <p class="text-sm">
                                <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $item->estado ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                    {{ $item->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded border border-zinc-300 bg-white p-4 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                    No se encontraron turnos con esos filtros.
                </div>
            @endforelse
        </div>

        <div class="mt-3 dark:text-zinc-200">
            {{ $turnos->links() }}
        </div>
    @endif

    @if ($modalVisible)
        @include('livewire.turnos.modal.index')
    @endif

    @if ($viewingId && $turnoDetalle)
        @include('livewire.turnos.detalle')
    @endif

</div>
