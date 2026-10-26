<div
    x-data="{ open: false }"
    x-init="setTimeout(() => open = true, 10)"
    class="fixed inset-0 z-50"
>
    <div
        x-show="open"
        x-transition.opacity
        class="absolute inset-0 bg-zinc-900/30"
        wire:click="closeModal"
    ></div>

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
                {{ match ($modo) {
                    'editar' => 'Editar Contrato',
                    'renovar' => 'Renovar Contrato por Prestación',
                    'indefinido' => 'Pasar a Contrato Indefinido',
                    default => 'Registrar Contrato',
                } }}
            </h2>
            <button wire:click="closeModal" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto px-4 py-5">
            @if ($vigenteDelEmpleado)
                <div class="rounded border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-800 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-200">
                    <i class="bi bi-info-circle-fill"></i>
                    El contrato vigente ({{ $vigenteDelEmpleado->etiquetaNumero() }} {{ $vigenteDelEmpleado->tipoContrato?->nombre }}, desde {{ $vigenteDelEmpleado->fecha_inicio->format('d/m/Y') }})
                    se finalizará el día anterior al inicio del nuevo y quedará en el historial. Los datos del empleado no cambian.
                </div>
            @endif

            @if ($modo === 'renovar')
                <div class="rounded border border-orange-200 bg-orange-50 px-3 py-2 text-sm text-orange-800 dark:border-orange-800 dark:bg-orange-900/20 dark:text-orange-200">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Será su 2.º contrato por prestación de servicios. Según el Ministerio de Trabajo no podrá tener un tercero: al vencer deberá pasar a indefinido.
                </div>
            @endif

            <div class="space-y-1">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Empleado <span class="text-red-600">*</span></label>
                <select wire:model.live="form.id_empleado" @disabled($form->id || in_array($modo, ['renovar', 'indefinido']))
                        class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($empleados as $empleado)
                        <option value="{{ $empleado->id }}">{{ $empleado->codigo_empleado }} — {{ $empleado->persona?->nombreCompleto() }}</option>
                    @endforeach
                </select>
                @error('form.id_empleado') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Empresa que contrata <span class="text-red-600">*</span></label>
                <select wire:model="form.id_empresa"
                        class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                    @endforeach
                </select>
                @error('form.id_empresa') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Tipo de Contrato <span class="text-red-600">*</span></label>
                <select wire:model="form.id_tipo_contrato" @disabled(in_array($modo, ['renovar', 'indefinido']))
                        class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Seleccione...</option>
                    @foreach ($tiposContrato as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
                @error('form.id_tipo_contrato') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Fecha Inicio <span class="text-red-600">*</span></label>
                    <input type="date" wire:model="form.fecha_inicio"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.fecha_inicio') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Fecha Fin</label>
                    <span class="block text-xs text-zinc-500 dark:text-zinc-400">Obligatoria en prestación de servicios</span>
                    <input type="date" wire:model="form.fecha_fin"
                           class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                    @error('form.fecha_fin') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Observaciones</label>
                <textarea wire:model="form.observaciones" rows="4"
                          class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
                @error('form.observaciones') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Documento (PDF o imagen)</label>
                <input type="file" wire:model="documento" accept=".pdf,image/*"
                       class="w-full rounded border border-zinc-400 bg-white px-3 py-2 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                <div wire:loading wire:target="documento" class="text-xs text-zinc-500 dark:text-zinc-400">Subiendo...</div>
                @error('documento') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
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
