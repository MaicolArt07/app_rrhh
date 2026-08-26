<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-md dark:border-zinc-700 dark:bg-zinc-900">

        <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">
            {{ $form->id ? 'Editar Gestión' : 'Registrar Gestión' }}
        </h2>

        <div class="space-y-5">

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Año <span class="text-red-600">*</span></label>

                <input type="number" wire:model.live="form.anio"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                       placeholder="ej. 2026">

                @error('form.anio')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Fecha Inicio <span class="text-red-600">*</span></label>

                <input type="date" wire:model="form.fecha_inicio"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">

                @error('form.fecha_inicio')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Fecha Fin <span class="text-red-600">*</span></label>

                <input type="date" wire:model="form.fecha_fin"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">

                @error('form.fecha_fin')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
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
