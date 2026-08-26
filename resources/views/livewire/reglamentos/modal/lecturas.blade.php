<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-lg dark:border-zinc-700 dark:bg-zinc-900 max-h-[90vh] overflow-y-auto">

        <h2 class="mb-4 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">
            Lecturas de "{{ $reglamentoLecturas->titulo ?? '' }}"
        </h2>

        <h3 class="mb-2 text-sm font-semibold text-zinc-600 dark:text-zinc-300">Han leído ({{ $reglamentoLecturas->lecturas->count() ?? 0 }})</h3>
        <ul class="mb-4 divide-y divide-gray-200 rounded border border-gray-200 dark:divide-zinc-700 dark:border-zinc-700">
            @forelse ($reglamentoLecturas->lecturas ?? [] as $lectura)
                <li class="flex items-center justify-between px-3 py-2 text-sm">
                    <span class="text-zinc-800 dark:text-zinc-100">{{ $lectura->empleado->persona?->nombreCompleto() }}</span>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $lectura->fecha_lectura->format('d/m/Y H:i') }}</span>
                </li>
            @empty
                <li class="px-3 py-2 text-sm text-zinc-500 dark:text-zinc-400">Nadie ha confirmado lectura aún.</li>
            @endforelse
        </ul>

        <h3 class="mb-2 text-sm font-semibold text-zinc-600 dark:text-zinc-300">Pendientes ({{ $empleadosSinLeer->count() }})</h3>
        <ul class="divide-y divide-gray-200 rounded border border-gray-200 dark:divide-zinc-700 dark:border-zinc-700">
            @forelse ($empleadosSinLeer as $empleado)
                <li class="flex items-center justify-between px-3 py-2 text-sm">
                    <span class="text-zinc-800 dark:text-zinc-100">{{ $empleado->persona?->nombreCompleto() }}</span>
                    <button wire:click="registrarLectura({{ $empleado->id }})" class="text-xs font-semibold text-blue-600 hover:underline dark:text-blue-400 cursor-pointer">
                        Marcar como leído
                    </button>
                </li>
            @empty
                <li class="px-3 py-2 text-sm text-zinc-500 dark:text-zinc-400">Todos los empleados han confirmado lectura.</li>
            @endforelse
        </ul>

        <div class="mt-6 flex justify-end">
            <button wire:click="cerrarLecturas"
                    class="rounded bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 cursor-pointer">
                Cerrar
            </button>
        </div>

    </div>
</div>
