<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-2xl dark:border-zinc-700 dark:bg-zinc-900 max-h-[90vh] overflow-y-auto">

        <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">
            {{ $form->id ? 'Editar Empleado' : 'Registrar Empleado' }}
        </h2>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

            <div class="space-y-1 sm:col-span-2">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Persona <span class="text-red-600">*</span></label>
                <select wire:model="form.id_persona"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($personasDisponibles as $persona)
                        <option value="{{ $persona->id }}">{{ $persona->nombreCompleto() }} — {{ $persona->numero_documento }}</option>
                    @endforeach
                </select>
                @error('form.id_persona') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    ¿No aparece la persona? Regístrala primero en el módulo de <a href="{{ route('personas') }}" wire:navigate class="text-blue-600 underline dark:text-blue-400">Personas</a>.
                </p>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Código de Empleado <span class="text-red-600">*</span></label>
                <input type="text" wire:model="form.codigo_empleado"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                @error('form.codigo_empleado') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Estado <span class="text-red-600">*</span></label>
                <select wire:model="form.id_estado_empleado"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($estadosEmpleado as $estado)
                        <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                    @endforeach
                </select>
                @error('form.id_estado_empleado') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Cargo <span class="text-red-600">*</span></label>
                <select wire:model="form.id_cargo"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($cargos as $cargo)
                        <option value="{{ $cargo->id }}">{{ $cargo->nombre }} ({{ $cargo->departamento->nombre ?? '' }})</option>
                    @endforeach
                </select>
                @error('form.id_cargo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Departamento <span class="text-red-600">*</span></label>
                <select wire:model="form.id_departamento"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($departamentos as $dep)
                        <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                    @endforeach
                </select>
                @error('form.id_departamento') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Sueldo Actual <span class="text-red-600">*</span></label>
                <input type="number" step="0.01" wire:model="form.sueldo_actual"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                @error('form.sueldo_actual') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Fecha de Ingreso <span class="text-red-600">*</span></label>
                <input type="date" wire:model="form.fecha_ingreso"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                @error('form.fecha_ingreso') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Fecha de Baja</label>
                <input type="date" wire:model="form.fecha_baja"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                @error('form.fecha_baja') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Banco</label>
                <input type="text" wire:model="form.banco"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                @error('form.banco') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Número de Cuenta Bancaria</label>
                <input type="text" wire:model="form.numero_cuenta_bancaria"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                @error('form.numero_cuenta_bancaria') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
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
