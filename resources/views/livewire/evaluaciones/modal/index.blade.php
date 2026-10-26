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
                Registrar Evaluación de Desempeño
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

            <div class="grid grid-cols-2 gap-3">
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

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Semestre <span class="text-red-600">*</span></label>
                    <select wire:model="form.semestre"
                            class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                        <option value="">Seleccione...</option>
                        <option value="primero">Primero</option>
                        <option value="segundo">Segundo</option>
                    </select>
                    @error('form.semestre') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Fecha de Evaluación <span class="text-red-600">*</span></label>
                <input type="date" wire:model="form.fecha_evaluacion"
                       class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                @error('form.fecha_evaluacion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Criterios de Evaluación (0-100)</label>
                @foreach ($criterios as $index => $criterio)
                    <div class="flex gap-2">
                        <input type="text" wire:model="criterios.{{ $index }}.criterio" placeholder="Criterio"
                               class="flex-1 rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                        <input type="number" wire:model="criterios.{{ $index }}.puntuacion" placeholder="Pts" min="0" max="100"
                               class="w-20 rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                        <button type="button" wire:click="quitarCriterio({{ $index }})" class="text-zinc-500 hover:text-red-600 dark:text-zinc-400 cursor-pointer">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    @error("criterios.$index.criterio") <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    @error("criterios.$index.puntuacion") <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                @endforeach

                <button type="button" wire:click="agregarCriterio"
                        class="text-sm font-semibold text-primary hover:underline cursor-pointer">
                    <i class="bi bi-plus-circle"></i> Agregar criterio
                </button>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Comentarios</label>
                <textarea wire:model="form.comentarios" rows="3"
                          class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Plan de Mejora</label>
                <textarea wire:model="form.plan_mejora" rows="3"
                          class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
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
