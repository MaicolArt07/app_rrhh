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
                {{ $form->id ? 'Editar Empleado' : 'Registrar Empleado' }}
            </h2>
            <button wire:click="closeModal" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-5">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div class="space-y-1 sm:col-span-2">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Empresa <span class="text-red-600">*</span></label>
                    <select wire:model="form.id_empresa"
                            class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                        <option value="">Seleccione...</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                    @error('form.id_empresa') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Persona <span class="text-red-600">*</span></label>
                    <select wire:model="form.id_persona"
                            class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                        <option value="">Seleccione...</option>
                        @foreach ($personasDisponibles as $persona)
                            <option value="{{ $persona->id }}">{{ $persona->nombreCompleto() }} — {{ $persona->numero_documento }}</option>
                        @endforeach
                    </select>
                    @error('form.id_persona') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        ¿No aparece la persona? Regístrala primero en el módulo de <a href="{{ route('personas') }}" wire:navigate class="text-primary underline">Personas</a>.
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Código de Empleado <span class="text-red-600">*</span></label>
                    <input type="text" wire:model="form.codigo_empleado"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.codigo_empleado') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Código Biométrico</label>
                    <input type="text" wire:model="form.codigo_biometrico" placeholder="ID del empleado en el reloj (ej. 2, 10, 21)"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.codigo_biometrico') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Correo corporativo</label>
                    <input type="email" wire:model="form.email_corporativo" placeholder="nombre@deltax.la"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.email_corporativo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Estado <span class="text-red-600">*</span></label>
                    <select wire:model="form.id_estado_empleado"
                            class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                        <option value="">Seleccione...</option>
                        @foreach ($estadosEmpleado as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                    @error('form.id_estado_empleado') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Cargo <span class="text-red-600">*</span></label>
                    <select wire:model="form.id_cargo"
                            class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                        <option value="">Seleccione...</option>
                        @foreach ($cargos as $cargo)
                            <option value="{{ $cargo->id }}">{{ $cargo->nombre }} ({{ $cargo->area->nombre ?? '' }})</option>
                        @endforeach
                    </select>
                    @error('form.id_cargo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Área <span class="text-red-600">*</span></label>
                    <select wire:model="form.id_area"
                            class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                        <option value="">Seleccione...</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                    @error('form.id_area') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Sueldo Actual <span class="text-red-600">*</span></label>
                    <input type="number" step="0.01" wire:model="form.sueldo_actual"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.sueldo_actual') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Fecha de Ingreso <span class="text-red-600">*</span></label>
                    <input type="date" wire:model="form.fecha_ingreso"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.fecha_ingreso') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Fecha de Baja</label>
                    <input type="date" wire:model="form.fecha_baja"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.fecha_baja') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Banco</label>
                    <input type="text" wire:model="form.banco"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.banco') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Número de Cuenta Bancaria</label>
                    <input type="text" wire:model="form.numero_cuenta_bancaria"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.numero_cuenta_bancaria') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
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
