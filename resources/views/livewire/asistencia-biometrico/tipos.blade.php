<div class="p-6">

    <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">
        Puede cambiar el nombre y el color de cada tipo. Los tipos base no se pueden borrar porque las reglas de cálculo dependen de ellos.
    </p>

    <div class="overflow-x-auto rounded-lg">
        <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
            <thead class="bg-gray-100 dark:bg-zinc-800">
            <tr>
                @foreach (['COLOR', 'CÓDIGO', 'NOMBRE', 'JUSTIFICADO', 'SUMA A LA MULTA', 'SE ELIGE A MANO', 'DÍAS REGISTRADOS', 'ESTADO', 'OPCIONES'] as $encabezado)
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">{{ $encabezado }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900">
            @foreach ($tipos as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        <span class="inline-block h-5 w-10 rounded border border-zinc-400" style="background: {{ $item->color_hex }}"></span>
                        <span class="block font-mono text-xs text-zinc-500">{{ $item->color_hex }}</span>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 font-mono text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->codigo }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->nombre }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->es_justificado ? 'Sí' : 'No' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->es_sancionable ? 'Sí' : 'No' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->seleccionable_manual ? 'Sí' : 'No (automático)' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->asistencias_count }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $item->estado ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-200 text-gray-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                            {{ $item->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        <button wire:click="openModal({{ $item->id }})" title="Editar"
                                class="text-yellow-500 hover:text-yellow-600 dark:text-yellow-400 cursor-pointer">
                            <i class="bi bi-pencil-square text-xl"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if ($modalVisible)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
            <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-md dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">Editar tipo {{ $codigo }}</h2>

                <div class="space-y-5">
                    <div class="space-y-1">
                        <label class="font-semibold text-zinc-700 dark:text-zinc-200">Nombre <span class="text-red-600">*</span></label>
                        <input type="text" wire:model="nombre"
                               class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                        @error('nombre') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="font-semibold text-zinc-700 dark:text-zinc-200">Color</label>
                            <input type="color" wire:model="color_hex" class="h-10 w-full rounded border border-gray-300 dark:border-zinc-700">
                            @error('color_hex') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="font-semibold text-zinc-700 dark:text-zinc-200">Orden</label>
                            <input type="number" min="0" wire:model="orden"
                                   class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                            @error('orden') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200">
                        <input type="checkbox" wire:model="estado" class="rounded border-gray-300 dark:border-zinc-600">
                        Activo (disponible en el selector de la matriz)
                    </label>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeModal" class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700 cursor-pointer">Cancelar</button>
                    <button wire:click="save" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 cursor-pointer">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>
