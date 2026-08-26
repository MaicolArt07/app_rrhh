<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-md dark:border-zinc-700 dark:bg-zinc-900">

        <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">
            {{ $form->id ? 'Editar Persona' : 'Registrar Persona' }}
        </h2>

        <div class="space-y-5">

            <!-- Pais -->
            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Pais <span class="text-red-600">*</span></label>

                <select wire:model="form.id_country"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione un pais</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>

                @error('form.id_country')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nombre -->
            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Nombre <span class="text-red-600">*</span></label>

                <input type="text" wire:model="form.name"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                       placeholder="Nombre">

                @error('form.name')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Apellido -->
            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Apellido <span class="text-red-600">*</span></label>

                <input type="text" wire:model="form.lastname"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                       placeholder="Apellido">

                @error('form.lastname')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- CI -->
            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">CI <span class="text-red-600">*</span></label>

                <input type="text" wire:model="form.ci"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                       placeholder="CI">

                @error('form.ci')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Telefono -->
            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Telefono <span class="text-red-600">*</span></label>

                <input type="text" wire:model="form.phone"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                       placeholder="Telefono">

                @error('form.phone')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Direccion -->
            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Direccion <span class="text-red-600">*</span></label>

                <textarea wire:model="form.address"
                          class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                          placeholder="Direccion"></textarea>

                @error('form.address')
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