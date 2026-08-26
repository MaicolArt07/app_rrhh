<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-lg dark:border-zinc-700 dark:bg-zinc-900 max-h-[90vh] overflow-y-auto">

        <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">
            Registrar Evaluación de Desempeño
        </h2>

        <div class="space-y-5">
            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Empleado <span class="text-red-600">*</span></label>
                <select wire:model="form.id_empleado"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($empleados as $empleado)
                        <option value="{{ $empleado->id }}">{{ $empleado->codigo_empleado }} — {{ $empleado->persona?->nombreCompleto() }}</option>
                    @endforeach
                </select>
                @error('form.id_empleado') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-zinc-700 dark:text-zinc-200">Gestión <span class="text-red-600">*</span></label>
                    <select wire:model="form.id_gestion"
                            class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                        <option value="">Seleccione...</option>
                        @foreach ($gestiones as $g)
                            <option value="{{ $g->id }}">{{ $g->anio }}</option>
                        @endforeach
                    </select>
                    @error('form.id_gestion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-zinc-700 dark:text-zinc-200">Semestre <span class="text-red-600">*</span></label>
                    <select wire:model="form.semestre"
                            class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                        <option value="">Seleccione...</option>
                        <option value="primero">Primero</option>
                        <option value="segundo">Segundo</option>
                    </select>
                    @error('form.semestre') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Fecha de Evaluación <span class="text-red-600">*</span></label>
                <input type="date" wire:model="form.fecha_evaluacion"
                       class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                @error('form.fecha_evaluacion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Criterios de Evaluación (0-100)</label>
                @foreach ($criterios as $index => $criterio)
                    <div class="flex gap-2">
                        <input type="text" wire:model="criterios.{{ $index }}.criterio" placeholder="Criterio"
                               class="flex-1 rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                        <input type="number" wire:model="criterios.{{ $index }}.puntuacion" placeholder="Pts" min="0" max="100"
                               class="w-20 rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                        <button type="button" wire:click="quitarCriterio({{ $index }})" class="text-red-600 hover:text-red-800 dark:text-red-400 cursor-pointer">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    @error("criterios.$index.criterio") <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    @error("criterios.$index.puntuacion") <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                @endforeach

                <button type="button" wire:click="agregarCriterio"
                        class="text-sm font-semibold text-blue-600 hover:underline dark:text-blue-400 cursor-pointer">
                    <i class="bi bi-plus-circle"></i> Agregar criterio
                </button>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Comentarios</label>
                <textarea wire:model="form.comentarios"
                          class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Plan de Mejora</label>
                <textarea wire:model="form.plan_mejora"
                          class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
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
