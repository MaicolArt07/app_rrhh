<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-md dark:border-zinc-700 dark:bg-zinc-900">

        <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">
            Generar Planilla
        </h2>

        <div class="space-y-5">
            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Gestión <span class="text-red-600">*</span></label>
                <select wire:model="id_gestion"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($gestiones as $g)
                        <option value="{{ $g->id }}">{{ $g->anio }}</option>
                    @endforeach
                </select>
                @error('id_gestion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Tipo de Planilla <span class="text-red-600">*</span></label>
                <select wire:model="id_tipo_planilla"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($tiposPlanilla as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
                @error('id_tipo_planilla') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-zinc-700 dark:text-zinc-200">Mes (dejar vacío si es anual: aguinaldo/bono14)</label>
                <select wire:model="id_mes"
                        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">— Ninguno —</option>
                    @foreach ($meses as $m)
                        <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-zinc-700 dark:text-zinc-200">Fecha Inicio <span class="text-red-600">*</span></label>
                    <input type="date" wire:model="fecha_inicio"
                           class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('fecha_inicio') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-zinc-700 dark:text-zinc-200">Fecha Fin <span class="text-red-600">*</span></label>
                    <input type="date" wire:model="fecha_fin"
                           class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('fecha_fin') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                Se generará un detalle por cada empleado activo, calculando sueldo, horas extra pendientes y conceptos fijos automáticamente.
            </p>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button wire:click="closeModal"
                    class="rounded bg-red-600 px-4 py-2 text-white transition hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 cursor-pointer">
                Cancelar
            </button>
            <button wire:click="generar"
                class="rounded bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 cursor-pointer">
                Generar
            </button>
        </div>

    </div>
</div>
