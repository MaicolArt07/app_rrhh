<div class="p-6">

    @if ($detallePlanilla)
        <div class="mb-4 flex items-center justify-between">
            <button wire:click="cerrarDetalle" class="flex items-center gap-2 text-blue-600 hover:underline dark:text-blue-400 cursor-pointer">
                <i class="bi bi-arrow-left"></i> Volver al listado
            </button>

            <div class="flex gap-2">
                @php $codigoEstado = $detallePlanilla->estadoPlanilla->codigo ?? ''; @endphp
                @if ($codigoEstado === 'borrador')
                    <button wire:click="avanzarEstado({{ $detallePlanilla->id }}, 'revisada')"
                            class="rounded bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700 cursor-pointer">Marcar Revisada</button>
                @elseif ($codigoEstado === 'revisada')
                    <button wire:click="avanzarEstado({{ $detallePlanilla->id }}, 'aprobada')"
                            class="rounded bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700 cursor-pointer">Aprobar</button>
                @elseif ($codigoEstado === 'aprobada')
                    <button wire:click="avanzarEstado({{ $detallePlanilla->id }}, 'pagada')"
                            class="rounded bg-green-600 px-3 py-2 text-sm text-white hover:bg-green-700 cursor-pointer">Marcar Pagada</button>
                @endif
            </div>
        </div>

        <div class="mb-4 rounded-lg border border-gray-300 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex flex-wrap justify-between gap-3">
                <div>
                    <p class="text-lg font-semibold text-zinc-800 dark:text-zinc-100">
                        {{ $detallePlanilla->gestion->anio }}{{ $detallePlanilla->mes ? ' — ' . $detallePlanilla->mes->nombre : '' }}
                    </p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $detallePlanilla->fecha_inicio->format('d/m/Y') }} al {{ $detallePlanilla->fecha_fin->format('d/m/Y') }}</p>
                </div>
                <div class="text-right">
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">{{ $detallePlanilla->estadoPlanilla->nombre ?? '' }}</span>
                    <p class="mt-1 text-xl font-bold text-zinc-800 dark:text-zinc-100">Q {{ number_format($detallePlanilla->total_planilla, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
                <thead class="bg-gray-100 dark:bg-zinc-800">
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">EMPLEADO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">SUELDO BASE</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">INGRESOS</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">DEDUCCIONES</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">LÍQUIDO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ESTADO PAGO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">BOLETA</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @forelse ($detallePlanilla->detalles as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                        <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            {{ $item->empleado->persona?->nombreCompleto() }}
                            <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $item->empleado->codigo_empleado }}</span>
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">Q {{ number_format($item->sueldo_base, 2) }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">Q {{ number_format($item->total_ingresos, 2) }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">Q {{ number_format($item->total_deducciones, 2) }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center font-semibold text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">Q {{ number_format($item->sueldo_liquido, 2) }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $item->estado_pago === 'pagado' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' }}">
                                {{ ucfirst($item->estado_pago) }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                            <a href="{{ route('planillas.boleta', $item->id) }}" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">
                                <i class="bi bi-file-earmark-pdf-fill text-xl"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-3 text-center text-gray-500 dark:text-zinc-400">Esta planilla no tiene empleados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <button wire:click="openModal"
                class="rounded bg-green-600 px-3 py-2 text-white transition hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 cursor-pointer">
                <i class="bi bi-plus-lg"></i> Generar Planilla
            </button>

            <div class="flex flex-1 flex-wrap justify-end gap-3">
                <input type="text" wire:model.live="search"
                    class="w-full max-w-xs rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                    placeholder="Buscar planilla...">
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
                <thead class="bg-gray-100 dark:bg-zinc-800">
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">GESTIÓN</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">TIPO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">PERIODO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">TOTAL</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ESTADO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">GENERADA POR</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">OPCIONES</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @forelse ($planillas as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->gestion->anio ?? '—' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->tipoPlanilla->nombre ?? '—' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->fecha_inicio->format('d/m') }} - {{ $item->fecha_fin->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center font-semibold text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">Q {{ number_format($item->total_planilla, 2) }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">{{ $item->estadoPlanilla->nombre ?? '—' }}</span>
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->generadoPor->name ?? '—' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                            <button wire:click="verDetalle({{ $item->id }})" class="text-blue-600 hover:underline dark:text-blue-400 cursor-pointer">
                                <i class="bi bi-eye-fill text-xl"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-3 text-center text-gray-500 dark:text-zinc-400">No hay planillas generadas.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 dark:text-zinc-200">
            {{ $planillas->links() }}
        </div>
    @endif

    @if ($modalVisible)
        @include('livewire.planillas.modal.index')
    @endif

</div>
