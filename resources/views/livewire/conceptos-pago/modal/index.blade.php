<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-md dark:border-zinc-700 dark:bg-zinc-900">

        <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">
            {{ $form->id ? 'Editar Concepto de Pago' : 'Registrar Concepto de Pago' }}
        </h2>

        <div class="space-y-5">
            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Nombre <span class="text-red-600">*</span></label>
                <input type="text" wire:model="form.nombre"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                @error('form.nombre') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Tipo <span class="text-red-600">*</span></label>
                <select wire:model="form.tipo_concepto"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    <option value="ingreso">Ingreso</option>
                    <option value="deduccion">Deducción</option>
                </select>
                @error('form.tipo_concepto') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Valor por Defecto</label>
                <input type="number" step="0.01" wire:model="form.valor_por_defecto"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                @error('form.valor_por_defecto') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model="form.es_porcentaje" class="rounded border-gray-300">
                <span class="text-zinc-700 dark:text-zinc-200">El valor por defecto es un porcentaje</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model="form.es_fijo" class="rounded border-gray-300">
                <span class="text-zinc-700 dark:text-zinc-200">Se aplica automáticamente a todas las planillas</span>
            </label>
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
