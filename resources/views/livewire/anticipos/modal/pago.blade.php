<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-sm dark:border-zinc-700 dark:bg-zinc-900">

        <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">
            Registrar Pago
        </h2>

        <div class="space-y-1">
            <label class="font-semibold text-zinc-700 dark:text-zinc-200">Monto Pagado <span class="text-red-600">*</span></label>
            <input type="number" step="0.01" wire:model="montoPago"
                   class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
            @error('montoPago') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button wire:click="cerrarPago"
                    class="rounded bg-red-600 px-4 py-2 text-white transition hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 cursor-pointer">
                Cancelar
            </button>
            <button wire:click="registrarPago"
                class="rounded bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 cursor-pointer">
                Registrar
            </button>
        </div>

    </div>
</div>
