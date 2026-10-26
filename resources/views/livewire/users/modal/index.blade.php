<div
    x-data="{ open: false }"
    x-init="setTimeout(() => open = true, 10)"
    class="fixed inset-0 z-50"
>
    {{-- Fondo tenue, clic afuera cierra el panel --}}
    <div
        x-show="open"
        x-transition.opacity
        class="absolute inset-0 bg-zinc-900/30"
        wire:click="$set('modalVisible', false)"
    ></div>

    {{-- Panel lateral --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col border-l border-zinc-300 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
    >
        <div class="flex items-center justify-between border-b border-zinc-300 bg-zinc-200 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800">
            <h2 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                {{ $userId ? 'Editar Usuario' : 'Crear Usuario' }}
            </h2>
            <button wire:click="$set('modalVisible', false)" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form wire:submit.prevent="save" autocomplete="off" class="flex flex-1 flex-col overflow-y-auto">
            <div class="flex-1 space-y-5 px-4 py-5">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Nombre <span class="text-red-600">*</span></label>
                    <input type="text" wire:model.defer="name"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Correo <span class="text-red-600">*</span></label>
                    <input type="email" wire:model.defer="email"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Contraseña</label>
                    <input type="password" wire:model.defer="password" autocomplete="new-password"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Confirmar contraseña</label>
                    <input type="password" wire:model.defer="password_confirmation" autocomplete="new-password"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Roles <span class="text-red-600">*</span></label>
                    <div class="grid grid-cols-2 gap-2 rounded border border-zinc-400 p-3 dark:border-zinc-600 dark:bg-zinc-800/40">
                        @foreach ($roles as $role)
                            <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200">
                                <input type="checkbox" wire:model.defer="selectedRoles" value="{{ $role->name }}"
                                       class="rounded border-zinc-400 text-primary focus:ring-primary dark:border-zinc-600 dark:bg-zinc-800">
                                {{ $role->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('selectedRoles') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-zinc-300 bg-zinc-100 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                <button type="button" wire:click="$set('modalVisible', false)"
                        class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                    class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700">
                    <i class="bi bi-check-lg text-primary"></i> {{ $userId ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</div>
