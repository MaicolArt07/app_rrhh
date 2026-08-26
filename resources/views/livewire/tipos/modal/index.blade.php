<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-md dark:border-zinc-700 dark:bg-zinc-900">

        <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">
            {{ $form->id ? 'Editar Tipo' : 'Registrar Tipo' }}
        </h2>

        <div class="space-y-5">

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Categoría <span class="text-red-600">*</span></label>

                <input type="text" wire:model="form.categoria" list="categorias-list"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                       placeholder="ej. tipo_documento">
                <datalist id="categorias-list">
                    @foreach ($categorias as $cat)
                        <option value="{{ $cat }}"></option>
                    @endforeach
                </datalist>

                @error('form.categoria')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Código <span class="text-red-600">*</span></label>

                <input type="text" wire:model="form.codigo"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                       placeholder="ej. dpi">

                @error('form.codigo')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Nombre <span class="text-red-600">*</span></label>

                <input type="text" wire:model="form.nombre"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                       placeholder="ej. DPI">

                @error('form.nombre')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-zinc-700 dark:text-zinc-200">Valor de Referencia</label>

                    <input type="number" step="0.0001" wire:model="form.valor_referencia"
                           class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                           placeholder="ej. 1.25">

                    @error('form.valor_referencia')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-zinc-700 dark:text-zinc-200">Orden</label>

                    <input type="number" wire:model="form.orden"
                           class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-400">

                    @error('form.orden')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>
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
