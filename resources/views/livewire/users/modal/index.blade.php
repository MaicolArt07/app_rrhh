<div>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="relative w-full max-w-md rounded-lg border border-gray-200 bg-white p-6 shadow-lg animate-[modalDrop_0.30s_ease-out] dark:border-zinc-700 dark:bg-zinc-900">

            <h2 class="mb-4 flex items-center justify-center gap-2 text-xl font-semibold text-zinc-800 dark:text-zinc-100">
                <i class="bi bi-person-circle text-blue-600 dark:text-blue-400"></i>
                {{ $userId ? 'Editar Usuario' : 'Crear Usuario' }}
            </h2>

            <form wire:submit.prevent="save" autocomplete="off">

                <!-- Nombre -->
                <div class="mb-4">
                    <label class="mb-1 flex items-center gap-2 font-medium text-zinc-700 dark:text-zinc-200">
                        <i class="bi bi-person-fill text-zinc-500"></i>
                        Nombre
                    </label>

                    <input
                        type="text"
                        wire:model.defer="name"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                    />

                    @error('name')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="mb-1 flex items-center gap-2 font-medium text-zinc-700 dark:text-zinc-200">
                        <i class="bi bi-envelope-fill text-zinc-500"></i>
                        Correo
                    </label>

                    <input
                        type="email"
                        wire:model.defer="email"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                    />

                    @error('email')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="mb-1 flex items-center gap-2 font-medium text-zinc-700 dark:text-zinc-200">
                        <i class="bi bi-lock-fill text-zinc-500"></i>
                        Contraseña
                    </label>

                    <input
                        type="password"
                        wire:model.defer="password"
                        autocomplete="new-password"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                    />

                    @error('password')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirmar password -->
                <div class="mb-4">
                    <label class="mb-1 flex items-center gap-2 font-medium text-zinc-700 dark:text-zinc-200">
                        <i class="bi bi-shield-lock-fill text-zinc-500"></i>
                        Confirmar contraseña
                    </label>

                    <input
                        type="password"
                        wire:model.defer="password_confirmation"
                        autocomplete="new-password"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                    />
                </div>

                <!-- Roles -->
                <div class="mb-4">
                    <label class="mb-1 flex items-center gap-2 font-medium text-zinc-700 dark:text-zinc-200">
                        <i class="bi bi-person-badge-fill text-zinc-500"></i>
                        Roles
                    </label>

                    <div class="grid grid-cols-2 gap-2 rounded border border-gray-200 p-3 dark:border-zinc-700 dark:bg-zinc-800/40">
                        @foreach ($roles as $role)

                        <label class="flex items-center gap-2 text-zinc-700 dark:text-zinc-200">
                            <input
                                type="checkbox"
                                wire:model.defer="selectedRoles"
                                value="{{ $role->name }}"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800"
                            >
                            {{ $role->name }}
                        </label>

                        @endforeach
                    </div>

                    @error('selectedRoles')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-2">

                    <button
                        type="button"
                        wire:click="$set('modalVisible', false)"
                        class="cursor-pointer rounded bg-gray-500 px-4 py-2 text-white transition hover:bg-gray-600 dark:bg-zinc-700 dark:hover:bg-zinc-600"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="cursor-pointer rounded bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                    >
                        {{ $userId ? 'Actualizar' : 'Guardar' }}
                    </button>

                </div>
            </form>

            <!-- Botón cerrar -->
            <button
                wire:click="$set('modalVisible', false)"
                class="absolute right-3 top-3 cursor-pointer text-xl text-gray-500 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-zinc-100"
            >
            </button>

        </div>
    </div>
</div>