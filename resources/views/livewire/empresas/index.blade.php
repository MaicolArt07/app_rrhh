<div class="p-6">

    <div class="mb-4 flex justify-between gap-3">
        <button wire:click="openModal"
            class="rounded bg-green-600 px-3 py-2 text-white transition hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 cursor-pointer">
            <i class="bi bi-plus-lg text-xl"></i>
        </button>
        <p class="self-center text-sm text-zinc-500 dark:text-zinc-400">
            Los datos legales (razón social, NIT, representante) se usarán en contratos, certificados de trabajo y memorandos.
        </p>
    </div>

    <div class="overflow-x-auto rounded-lg">
        <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
            <thead class="bg-gray-100 dark:bg-zinc-800">
            <tr>
                @foreach (['CÓDIGO', 'EMPRESA', 'RAZÓN SOCIAL / NIT', 'REPRESENTANTE LEGAL', 'ACTIVOS', 'INDEFINIDOS', 'PRESTACIÓN', 'ESTADO', 'OPCIONES'] as $encabezado)
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">{{ $encabezado }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900">
            @forelse ($empresas as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                    <td class="border border-gray-300 px-3 py-2 text-center font-mono text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->codigo }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        <span class="inline-block h-3 w-3 rounded-full" style="background: {{ $item->color_hex }}"></span>
                        {{ $item->nombre }}
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        {{ $item->razon_social ?? '—' }}
                        @if ($item->nit) <span class="block text-xs text-zinc-500">NIT {{ $item->nit }}</span> @endif
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->representante_legal ?? '—' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        <a href="{{ route('personal.empresa', $item->codigo) }}" wire:navigate class="font-semibold text-blue-600 hover:underline dark:text-blue-400">{{ $item->activos_count }}</a>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->indefinidos_count }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->prestacion_count }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $item->estado ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-200 text-gray-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                            {{ $item->estado ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        <div class="flex justify-center gap-3">
                            <button wire:click="openModal({{ $item->id }})" title="Editar"
                                    class="text-yellow-500 transition hover:text-yellow-600 dark:text-yellow-400 cursor-pointer">
                                <i class="bi bi-pencil-square text-xl"></i>
                            </button>
                            <button wire:click="delete({{ $item->id }})" title="{{ $item->estado ? 'Desactivar' : 'Activar' }}"
                                    class="{{ $item->estado ? 'text-red-600 hover:text-red-800 dark:text-red-400' : 'text-gray-400 dark:text-zinc-500' }} cursor-pointer">
                                <i class="bi {{ $item->estado ? 'bi-trash' : 'bi-x-circle' }} text-xl"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="py-3 text-center text-gray-500 dark:text-zinc-400">No hay empresas registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($modalVisible)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
            <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-2xl dark:border-zinc-700 dark:bg-zinc-900 max-h-[90vh] overflow-y-auto">
                <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">{{ $empresaId ? 'Editar Empresa' : 'Registrar Empresa' }}</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ([
                        'codigo' => ['Código', true, 'Ej. DC, DX'],
                        'nombre' => ['Nombre', true, 'Ej. Delta Cargo'],
                        'razon_social' => ['Razón social', false, ''],
                        'nit' => ['NIT', false, ''],
                        'representante_legal' => ['Representante legal', false, ''],
                        'telefono' => ['Teléfono', false, ''],
                        'email' => ['Correo', false, ''],
                        'direccion' => ['Dirección', false, ''],
                    ] as $campo => [$etiqueta, $obligatorio, $ayuda])
                        <div class="space-y-1">
                            <label class="font-semibold text-zinc-700 dark:text-zinc-200">{{ $etiqueta }} @if ($obligatorio)<span class="text-red-600">*</span>@endif</label>
                            <input type="text" wire:model="{{ $campo }}" placeholder="{{ $ayuda }}"
                                   class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                            @error($campo) <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                    <div class="space-y-1">
                        <label class="font-semibold text-zinc-700 dark:text-zinc-200">Color</label>
                        <input type="color" wire:model="color_hex" class="h-10 w-full rounded border border-gray-300 dark:border-zinc-700">
                        @error('color_hex') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
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
