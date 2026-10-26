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
        wire:click="closeModal"
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
        class="absolute inset-y-0 right-0 flex w-full max-w-xl flex-col border-l border-zinc-300 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
    >
        <div class="flex items-center justify-between border-b border-zinc-300 bg-zinc-200 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800">
            <h2 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                {{ $form->id ? 'Editar Persona' : 'Registrar Persona' }}
            </h2>
            <button wire:click="closeModal" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto px-4 py-5">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Primer Nombre <span class="text-red-600">*</span></label>
                    <input type="text" wire:model="form.primer_nombre"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.primer_nombre') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Segundo Nombre</label>
                    <input type="text" wire:model="form.segundo_nombre"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.segundo_nombre') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Primer Apellido <span class="text-red-600">*</span></label>
                    <input type="text" wire:model="form.primer_apellido"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.primer_apellido') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Segundo Apellido</label>
                    <input type="text" wire:model="form.segundo_apellido"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.segundo_apellido') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Tipo de Documento <span class="text-red-600">*</span></label>
                    <select wire:model="form.id_tipo_documento"
                            class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                        <option value="">Seleccione...</option>
                        @foreach ($tiposDocumento as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    @error('form.id_tipo_documento') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Número de Documento <span class="text-red-600">*</span></label>
                    <input type="text" wire:model="form.numero_documento"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.numero_documento') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">NIT</label>
                    <input type="text" wire:model="form.nit"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.nit') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Fecha de Nacimiento</label>
                    <input type="date" wire:model="form.fecha_nacimiento"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.fecha_nacimiento') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Género <span class="text-red-600">*</span></label>
                    <select wire:model="form.genero"
                            class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                        <option value="">Seleccione...</option>
                        <option value="masculino">Masculino</option>
                        <option value="femenino">Femenino</option>
                        <option value="otro">Otro</option>
                    </select>
                    @error('form.genero') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Teléfono</label>
                    <input type="text" wire:model="form.telefono"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.telefono') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Correo Electrónico</label>
                    <input type="email" wire:model="form.email"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Dirección</label>
                    <textarea wire:model="form.direccion"
                              class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
                    @error('form.direccion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Foto</label>
                    <input type="file" wire:model="foto" accept="image/*"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    <div wire:loading wire:target="foto" class="text-xs text-zinc-500 dark:text-zinc-400">Subiendo...</div>
                    @if ($foto)
                        <img src="{{ $foto->temporaryUrl() }}" class="mt-2 h-16 w-16 rounded-full object-cover">
                    @endif
                    @error('foto') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-zinc-300 bg-zinc-100 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
            <button wire:click="closeModal"
                    class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 cursor-pointer">
                Cancelar
            </button>
            <button wire:click="save"
                class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700">
                <i class="bi bi-check-lg text-primary"></i> Guardar
            </button>
        </div>
    </div>
</div>
