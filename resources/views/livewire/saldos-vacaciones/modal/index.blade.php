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
        class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col border-l border-zinc-300 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
    >
        <div class="flex items-center justify-between border-b border-zinc-300 bg-zinc-200 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800">
            <h2 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                {{ $form->id ? 'Editar Saldo de Vacaciones' : 'Registrar Saldo de Vacaciones' }}
            </h2>
            <button wire:click="closeModal" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto px-4 py-5">
            <div class="space-y-1">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Empleado <span class="text-red-600">*</span></label>
                <select wire:model="form.id_empleado"
                        class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($empleados as $empleado)
                        <option value="{{ $empleado->id }}">{{ $empleado->codigo_empleado }} — {{ $empleado->persona?->nombreCompleto() }}</option>
                    @endforeach
                </select>
                @error('form.id_empleado') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Gestión <span class="text-red-600">*</span></label>
                <select wire:model="form.id_gestion"
                        class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($gestiones as $g)
                        <option value="{{ $g->id }}">{{ $g->anio }}</option>
                    @endforeach
                </select>
                @error('form.id_gestion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Días Generados <span class="text-red-600">*</span></label>
                    <input type="number" step="0.01" wire:model="form.dias_generados"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.dias_generados') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Días Tomados <span class="text-red-600">*</span></label>
                    <input type="number" step="0.01" wire:model="form.dias_tomados"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.dias_tomados') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
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
