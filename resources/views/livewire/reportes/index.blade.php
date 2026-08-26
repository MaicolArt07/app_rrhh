<div class="p-6">

    <div class="mb-4 flex gap-2 border-b border-gray-300 dark:border-zinc-700">
        <button wire:click="setTab('empleados')"
            class="px-4 py-2 text-sm font-semibold {{ $tab === 'empleados' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400' : 'text-zinc-500 dark:text-zinc-400' }} cursor-pointer">
            <i class="bi bi-people-fill me-1"></i> Empleados
        </button>
        <button wire:click="setTab('planillas')"
            class="px-4 py-2 text-sm font-semibold {{ $tab === 'planillas' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400' : 'text-zinc-500 dark:text-zinc-400' }} cursor-pointer">
            <i class="bi bi-cash-stack me-1"></i> Planillas
        </button>
    </div>

    @if ($tab === 'empleados')
        <div class="mb-4 flex justify-end">
            <button wire:click="exportarEmpleados"
                class="rounded bg-green-600 px-3 py-2 text-sm text-white transition hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 cursor-pointer">
                <i class="bi bi-file-earmark-excel-fill"></i> Exportar a Excel
            </button>
        </div>

        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
                <thead class="bg-gray-100 dark:bg-zinc-800">
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">CÓDIGO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">NOMBRE</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">CARGO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">DEPARTAMENTO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">SUELDO</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @forelse ($empleados as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                        <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->codigo_empleado }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->persona?->nombreCompleto() }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->cargo->nombre ?? '—' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->departamento->nombre ?? '—' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">Q {{ number_format($item->sueldo_actual, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-3 text-center text-gray-500 dark:text-zinc-400">No hay empleados activos.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
                <thead class="bg-gray-100 dark:bg-zinc-800">
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">GESTIÓN</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">TIPO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">PERIODO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ESTADO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">EXPORTAR</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @forelse ($planillas as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->gestion->anio ?? '—' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->tipoPlanilla->nombre ?? '—' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->fecha_inicio->format('d/m') }} - {{ $item->fecha_fin->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->estadoPlanilla->nombre ?? '—' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                            <button wire:click="exportarPlanilla({{ $item->id }})" class="text-green-600 hover:text-green-700 dark:text-green-400 cursor-pointer">
                                <i class="bi bi-file-earmark-excel-fill text-xl"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-3 text-center text-gray-500 dark:text-zinc-400">No hay planillas generadas.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @endif

</div>
