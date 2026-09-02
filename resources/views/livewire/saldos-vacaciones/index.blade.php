<div class="p-6">

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <button wire:click="openModal"
            class="rounded bg-green-600 px-3 py-2 text-white transition hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 cursor-pointer">
            <i class="bi bi-plus-lg text-xl"></i>
        </button>

        <div class="flex flex-1 flex-wrap justify-end gap-3">
            <select wire:model.live="gestionFiltro"
                class="rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                <option value="">Todas las gestiones</option>
                @foreach ($gestiones as $g)
                    <option value="{{ $g->id }}">{{ $g->anio }}</option>
                @endforeach
            </select>

            <input type="text" wire:model.live="search"
                class="w-full max-w-xs rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                placeholder="Buscar empleado...">
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg">
        <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
            <thead class="bg-gray-100 dark:bg-zinc-800">
            <tr>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">EMPLEADO</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">GESTIÓN</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">GENERADOS</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">TOMADOS</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">DISPONIBLES</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ÚLT. CÁLCULO</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">OPCIONES</th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900">
            @forelse ($saldos as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        {{ $item->empleado->persona?->nombreCompleto() }}
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $item->empleado->codigo_empleado }}</span>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->gestion->anio ?? '—' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ rtrim(rtrim(number_format($item->dias_generados, 2), '0'), '.') ?: 0 }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ rtrim(rtrim(number_format($item->dias_tomados, 2), '0'), '.') ?: 0 }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center font-semibold text-green-700 dark:border-zinc-700 dark:text-green-400">{{ rtrim(rtrim(number_format($item->dias_disponibles, 2), '0'), '.') ?: 0 }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->fecha_ultimo_calculo?->format('d/m/Y') ?? '—' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        <button wire:click="openModal({{ $item->id }})"
                                class="text-yellow-500 transition hover:text-yellow-600 dark:text-yellow-400 dark:hover:text-yellow-300 cursor-pointer">
                            <i class="bi bi-pencil-square text-xl"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-3 text-center text-gray-500 dark:text-zinc-400">No hay saldos de vacaciones registrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 dark:text-zinc-200">
        {{ $saldos->links() }}
    </div>

    @if ($modalVisible)
        @include('livewire.saldos-vacaciones.modal.index')
    @endif

</div>
