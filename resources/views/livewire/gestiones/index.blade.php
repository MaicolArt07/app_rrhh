<div class="p-6">

    <div class="mb-4 flex gap-2 border-b border-gray-300 dark:border-zinc-700">
        <button wire:click="setTab('gestiones')"
            class="px-4 py-2 text-sm font-semibold {{ $tab === 'gestiones' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400' : 'text-zinc-500 dark:text-zinc-400' }} cursor-pointer">
            <i class="bi bi-calendar-range-fill me-1"></i> Gestiones (Años)
        </button>
        <button wire:click="setTab('meses')"
            class="px-4 py-2 text-sm font-semibold {{ $tab === 'meses' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400' : 'text-zinc-500 dark:text-zinc-400' }} cursor-pointer">
            <i class="bi bi-calendar3 me-1"></i> Meses
        </button>
    </div>

    @if ($tab === 'gestiones')
        <div class="mb-4 flex justify-end">
            <button wire:click="openModal"
                class="rounded bg-green-600 px-3 py-2 text-white transition hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 cursor-pointer">
                <i class="bi bi-plus-lg text-xl"></i>
            </button>
        </div>

        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
                <thead class="bg-gray-100 dark:bg-zinc-800">
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">AÑO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">FECHA INICIO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">FECHA FIN</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ESTADO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">OPCIONES</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @forelse ($gestiones as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                        <td class="border border-gray-300 px-3 py-2 text-center font-semibold text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->anio }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->fecha_inicio->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->fecha_fin->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $item->estado ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-200 text-gray-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                {{ $item->estado ? 'Abierta' : 'Cerrada' }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                            <div class="flex justify-center gap-3">
                                <button wire:click="openModal({{ $item->id }})"
                                        class="text-yellow-500 transition hover:text-yellow-600 dark:text-yellow-400 dark:hover:text-yellow-300 cursor-pointer">
                                    <i class="bi bi-pencil-square text-xl"></i>
                                </button>
                                <button wire:click="toggleGestion({{ $item->id }})"
                                        class="{{ $item->estado ? 'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300' : 'text-gray-400 dark:text-zinc-500' }} cursor-pointer">
                                    @if($item->estado)
                                        <i class="bi bi-lock-fill text-xl" title="Cerrar gestión"></i>
                                    @else
                                        <i class="bi bi-unlock-fill text-xl" title="Reabrir gestión"></i>
                                    @endif
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-3 text-center text-gray-500 dark:text-zinc-400">No hay gestiones registradas.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 dark:text-zinc-200">
            {{ $gestiones->links() }}
        </div>
    @else
        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
                <thead class="bg-gray-100 dark:bg-zinc-800">
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">N°</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">NOMBRE</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ABREVIATURA</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ESTADO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">OPCIONES</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @foreach ($meses as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->numero }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->nombre }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->abreviatura }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $item->estado ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-200 text-gray-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                {{ $item->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                            <button wire:click="toggleMes({{ $item->id }})"
                                    class="{{ $item->estado ? 'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300' : 'text-gray-400 dark:text-zinc-500' }} cursor-pointer">
                                @if($item->estado)
                                    <i class="bi bi-toggle-on text-2xl"></i>
                                @else
                                    <i class="bi bi-toggle-off text-2xl"></i>
                                @endif
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($modalVisible)
        @include('livewire.gestiones.modal.index')
    @endif

</div>
