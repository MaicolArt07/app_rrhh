<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-lg dark:border-zinc-700 dark:bg-zinc-900 max-h-[90vh] overflow-y-auto">

        <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">
            {{ $form->id ? 'Editar Documento' : 'Registrar Documento' }}
        </h2>

        <div class="space-y-5">
            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Empleado <span class="text-red-600">*</span></label>
                <select wire:model="form.id_empleado"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($empleados as $empleado)
                        <option value="{{ $empleado->id }}">{{ $empleado->codigo_empleado }} — {{ $empleado->persona?->nombreCompleto() }}</option>
                    @endforeach
                </select>
                @error('form.id_empleado') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Tipo de Documento <span class="text-red-600">*</span></label>
                <select wire:model="form.id_tipo_documento_empleado"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($tiposDocumentoEmpleado as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
                @error('form.id_tipo_documento_empleado') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Número de Documento</label>
                <input type="text" wire:model="form.numero_documento"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                @error('form.numero_documento') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-zinc-700 dark:text-zinc-200">Fecha Emisión</label>
                    <input type="date" wire:model="form.fecha_emision"
                           class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.fecha_emision') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-zinc-700 dark:text-zinc-200">Fecha Vencimiento</label>
                    <input type="date" wire:model="form.fecha_vencimiento"
                           class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.fecha_vencimiento') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Archivo (PDF o imagen)</label>
                <input type="file" wire:model="archivo" accept=".pdf,image/*"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                <div wire:loading wire:target="archivo" class="text-xs text-zinc-500 dark:text-zinc-400">Subiendo...</div>
                @error('archivo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button wire:click="closeModal"
                    class="rounded bg-red-600 px-4 py-2 text-white transition hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 cursor-pointer">
                Cancelar
            </button>
            <button wire:click="save"
                class="rounded bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 cursor-pointer">
                Guardar
            </button>
        </div>

    </div>
</div>
