<div class="p-6">

    <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">
        Los días remotos asignados no generan descuento por no marcar ni atraso. Si ese día hay marcaciones, se muestran solo como información.
    </p>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <button wire:click="openModal"
            class="rounded bg-green-600 px-3 py-2 text-white transition hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 cursor-pointer">
            <i class="bi bi-plus-lg text-xl"></i>
        </button>

        <div class="flex flex-1 flex-wrap items-center justify-end gap-3">
            <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200">
                <input type="checkbox" wire:model.live="mostrarAnulados" class="rounded border-gray-300 dark:border-zinc-600">
                Mostrar anulados
            </label>
            <input type="month" wire:model.live="mesFiltro"
                   class="rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
            <select wire:model.live="empleadoFiltro"
                    class="rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                <option value="">Todos los empleados</option>
                @foreach ($empleados as $empleado)
                    <option value="{{ $empleado->id }}">{{ $empleado->persona?->nombreCompleto() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg">
        <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
            <thead class="bg-gray-100 dark:bg-zinc-800">
            <tr>
                @foreach (['EMPLEADO', 'FECHA', 'DÍA', 'OBSERVACIÓN', 'ASIGNADO POR', 'ESTADO', 'OPCIONES'] as $encabezado)
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">{{ $encabezado }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900">
            @forelse ($dias as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->empleado->persona?->nombreCompleto() }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->fecha->format('d/m/Y') }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ ucfirst($item->fecha->translatedFormat('l')) }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->observacion ?? '—' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->asignadoPor?->name ?? '—' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $item->estado ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-200 text-gray-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                            {{ $item->estado ? 'Vigente' : 'Anulado' }}
                        </span>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        @if ($item->estado)
                            <button wire:click="anular({{ $item->id }})" wire:confirm="¿Anular este día remoto?" title="Anular"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 cursor-pointer">
                                <i class="bi bi-x-circle text-xl"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-3 text-center text-gray-500 dark:text-zinc-400">No hay días remotos asignados en este periodo.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 dark:text-zinc-200">{{ $dias->links() }}</div>

    @if ($modalVisible)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
            <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-lg dark:border-zinc-700 dark:bg-zinc-900 max-h-[90vh] overflow-y-auto">
                <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">Asignar días remotos</h2>

                <div class="space-y-5">
                    <div class="space-y-1">
                        <label class="font-semibold text-zinc-700 dark:text-zinc-200">Empleado <span class="text-red-600">*</span></label>
                        <select wire:model="id_empleado"
                                class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                            <option value="">Seleccione...</option>
                            @foreach ($empleados as $empleado)
                                <option value="{{ $empleado->id }}">{{ $empleado->persona?->nombreCompleto() }}</option>
                            @endforeach
                        </select>
                        @error('id_empleado') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="font-semibold text-zinc-700 dark:text-zinc-200">Desde <span class="text-red-600">*</span></label>
                            <input type="date" wire:model.live="desde"
                                   class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                            @error('desde') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="font-semibold text-zinc-700 dark:text-zinc-200">Hasta <span class="text-red-600">*</span></label>
                            <input type="date" wire:model.live="hasta"
                                   class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                            @error('hasta') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="font-semibold text-zinc-700 dark:text-zinc-200">Días de la semana</label>
                        <div class="flex flex-wrap gap-3 text-sm text-zinc-700 dark:text-zinc-200">
                            @foreach ([1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie'] as $numero => $nombre)
                                <label class="flex items-center gap-1">
                                    <input type="checkbox" value="{{ $numero }}" wire:model.live="diasSemana" class="rounded border-gray-300 dark:border-zinc-600"> {{ $nombre }}
                                </label>
                            @endforeach
                        </div>
                        @error('diasSemana') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Se asignarán {{ count($fechasPrevistas) }} día(s)
                            @if (count($fechasPrevistas) && count($fechasPrevistas) <= 10)
                                : {{ collect($fechasPrevistas)->map(fn ($f) => \Illuminate\Support\Carbon::parse($f)->format('d/m'))->implode(', ') }}
                            @endif
                        </p>
                    </div>

                    <div class="space-y-1">
                        <label class="font-semibold text-zinc-700 dark:text-zinc-200">Observación</label>
                        <input type="text" wire:model="observacion"
                               class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                        @error('observacion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeModal" class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700 cursor-pointer">Cancelar</button>
                    <button wire:click="save" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 cursor-pointer">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>
