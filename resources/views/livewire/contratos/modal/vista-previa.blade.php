<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="flex h-[92vh] w-full max-w-4xl flex-col rounded-lg border border-gray-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900">

        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3 dark:border-zinc-700">
            <div>
                <h2 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                    <i class="bi bi-eye-fill text-primary"></i> Vista previa del contrato
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $documentoContrato->empleado->persona->nombreCompleto() }} · {{ $documentoFormato['titulo'] ?? '' }}
                </p>
            </div>
            <button wire:click="cerrarDocumento" class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 overflow-hidden bg-zinc-200 dark:bg-zinc-950">
            <iframe
                src="data:application/pdf;base64,{{ $vistaPreviaPdf }}"
                class="h-full w-full"
                title="Vista previa del contrato"
            ></iframe>
        </div>

        <div class="flex items-center justify-between border-t border-zinc-200 px-5 py-3 dark:border-zinc-700">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                Revisa el documento. Si todo está correcto, descárgalo.
            </p>
            <div class="flex gap-3">
                <button wire:click="cerrarDocumento"
                        class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 cursor-pointer">
                    Cerrar
                </button>
                <button wire:click="descargarDefinitivo" wire:loading.attr="disabled" wire:target="descargarDefinitivo"
                        class="rounded border border-zinc-400 bg-white px-4 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700">
                    <span wire:loading.remove wire:target="descargarDefinitivo"><i class="bi bi-download text-primary"></i> Descargar</span>
                    <span wire:loading wire:target="descargarDefinitivo">Descargando…</span>
                </button>
            </div>
        </div>
    </div>
</div>
