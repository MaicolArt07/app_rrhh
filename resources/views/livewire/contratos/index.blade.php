<div class="p-6">

    <div class="mb-4 flex justify-between gap-3">
        <button wire:click="openModal"
            class="rounded bg-green-600 px-3 py-2 text-white transition hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 cursor-pointer">
            <i class="bi bi-plus-lg text-xl"></i>
        </button>

        <input type="text" wire:model.live="search"
            class="w-1/3 rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-400"
            placeholder="Buscar por empleado...">
    </div>

    <div class="overflow-x-auto rounded-lg">
        <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
            <thead class="bg-gray-100 dark:bg-zinc-800">
            <tr>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">EMPLEADO</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">TIPO</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">INICIO</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">FIN</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">DOCUMENTO</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ESTADO</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">OPCIONES</th>
            </tr>
            </thead>

            <tbody class="bg-white dark:bg-zinc-900">
            @forelse ($contratos as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 {{ $item->fecha_fin && $item->fecha_fin->isPast() ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        {{ $item->empleado->persona?->nombreCompleto() }}
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $item->empleado->codigo_empleado }}</span>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->tipoContrato->nombre ?? '—' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->fecha_inicio->format('d/m/Y') }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        {{ $item->fecha_fin?->format('d/m/Y') ?? 'Indefinido' }}
                        @if ($item->fecha_fin && $item->fecha_fin->isPast())
                            <span class="ml-1 text-xs font-semibold text-red-600 dark:text-red-400">Vencido</span>
                        @endif
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        @if ($item->getFirstMediaUrl('documento'))
                            <a href="{{ $item->getFirstMediaUrl('documento') }}" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">
                                <i class="bi bi-file-earmark-pdf-fill text-xl"></i>
                            </a>
                        @else
                            <span class="text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $item->estado ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-200 text-gray-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                            {{ $item->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        <div class="flex justify-center gap-3">
                            <button wire:click="openModal({{ $item->id }})"
                                    class="text-yellow-500 transition hover:text-yellow-600 dark:text-yellow-400 dark:hover:text-yellow-300 cursor-pointer">
                                <i class="bi bi-pencil-square text-xl"></i>
                            </button>
                            <button wire:click="delete({{ $item->id }})"
                                    class="{{ $item->estado ? 'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300' : 'text-gray-400 dark:text-zinc-500' }} cursor-pointer">
                                @if($item->estado)
                                    <i class="bi bi-trash text-xl"></i>
                                @else
                                    <i class="bi bi-x-circle text-xl"></i>
                                @endif
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-3 text-center text-gray-500 dark:text-zinc-400">No hay contratos registrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 dark:text-zinc-200">
        {{ $contratos->links() }}
    </div>

    @if ($modalVisible)
        @include('livewire.contratos.modal.index')
    @endif

</div>
