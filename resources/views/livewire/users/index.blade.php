<div class="flex h-full flex-col bg-zinc-200 p-4 dark:bg-zinc-950">
    @if (session()->has('message'))
        <div class="mb-3 cursor-pointer rounded border border-emerald-300 bg-emerald-100 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-3 flex flex-wrap items-center justify-between gap-3 rounded border border-zinc-300 bg-zinc-100 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-900">
        <button wire:click="openModal"
            class="flex items-center gap-2 rounded border border-zinc-400 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
            <i class="bi bi-plus-lg text-primary"></i> Nuevo
        </button>

        <div class="flex flex-1 flex-wrap items-center justify-end gap-2">
            <input type="text" wire:model="search" wire:keydown.enter="buscar"
                class="w-full max-w-xs rounded border border-zinc-400 bg-white px-2 py-1.5 text-sm text-zinc-800 placeholder:text-zinc-400 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                placeholder="Buscar por nombre o correo...">

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
            <p class="text-sm">Selecciona los filtros que necesites y presiona <strong>Buscar</strong> para ver los usuarios.</p>
        </div>
    @else
        <div class="hidden flex-1 overflow-auto rounded border border-zinc-300 dark:border-zinc-700 md:block">
            <table class="min-w-full border-collapse text-sm">
                <thead class="sticky top-0 bg-zinc-300 dark:bg-zinc-800">
                <tr>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-left font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Nombre</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-left font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Correo</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-left font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Roles</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Opciones</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @forelse ($users as $user)
                    <tr wire:click="verDetalle({{ $user->id }})" class="cursor-pointer border-b border-zinc-200 hover:bg-zinc-100 dark:border-zinc-800 dark:hover:bg-zinc-800">
                        <td class="px-3 py-1.5 capitalize text-zinc-800 dark:text-zinc-100">{{ $user->name }}</td>
                        <td class="px-3 py-1.5 text-zinc-800 dark:text-zinc-100">{{ $user->email }}</td>
                        <td class="px-3 py-1.5 capitalize text-zinc-800 dark:text-zinc-100">
                            @foreach ($user->roles as $role)
                                <span class="mr-1 rounded bg-zinc-200 px-2 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-3 py-1.5 text-center" onclick="event.stopPropagation()">
                            <div class="flex justify-center gap-3">
                                <button wire:click="openModal({{ $user->id }})"
                                        class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button wire:click="delete({{ $user->id }})"
                                        onclick="return confirm('¿Eliminar usuario?')"
                                        class="text-zinc-500 hover:text-red-600 dark:text-zinc-400 cursor-pointer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-3 text-center text-zinc-500 dark:text-zinc-400">No se encontraron usuarios con esos filtros.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-2 md:hidden">
            @forelse ($users as $user)
                <div x-data="{ open: false }"
                     class="rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center gap-2">
                        <button type="button" x-on:click.stop="open = !open"
                            class="flex h-6 w-6 shrink-0 cursor-pointer items-center justify-center rounded-full border border-primary text-primary transition hover:bg-primary hover:text-white">
                            <i class="bi text-xs" :class="open ? 'bi-dash' : 'bi-plus'"></i>
                        </button>

                        <div wire:click="verDetalle({{ $user->id }})" class="min-w-0 flex-1 cursor-pointer">
                            <p class="truncate text-sm font-medium capitalize text-zinc-800 dark:text-zinc-100">{{ $user->name }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                        </div>

                        <div class="flex shrink-0 gap-3" onclick="event.stopPropagation()">
                            <button wire:click="openModal({{ $user->id }})"
                                    class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button wire:click="delete({{ $user->id }})"
                                    onclick="return confirm('¿Eliminar usuario?')"
                                    class="text-zinc-500 hover:text-red-600 dark:text-zinc-400 cursor-pointer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div x-show="open" x-collapse x-cloak onclick="event.stopPropagation()"
                         class="mt-2 border-t border-zinc-200 pt-2 pl-8 dark:border-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Roles</span>
                        <p class="mt-1 text-sm capitalize text-zinc-800 dark:text-zinc-100">
                            @foreach ($user->roles as $role)
                                <span class="mr-1 rounded bg-zinc-200 px-2 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </p>
                    </div>
                </div>
            @empty
                <div class="rounded border border-zinc-300 bg-white p-4 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                    No se encontraron usuarios con esos filtros.
                </div>
            @endforelse
        </div>

        <div class="mt-3 dark:text-zinc-200">
            {{ $users->links() }}
        </div>
    @endif

    @if ($modalVisible)
        @include('livewire.users.modal.index')
    @endif

    @if ($viewingId && $userDetalle)
        @include('livewire.users.detalle')
    @endif
</div>
