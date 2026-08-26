<div class="p-6 space-y-6">

    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
        <h3 class="mb-3 flex items-center gap-2 text-lg font-semibold text-gray-700 dark:text-zinc-200">
            <i class="bi bi-building-fill text-blue-500"></i> Datos de la Empresa
        </h3>
        <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
            <div>
                <span class="text-zinc-500 dark:text-zinc-400">Nombre del sistema</span>
                <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $appName }}</p>
            </div>
            <div>
                <span class="text-zinc-500 dark:text-zinc-400">Entorno</span>
                <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ ucfirst($appEnv) }}</p>
            </div>
            <div>
                <span class="text-zinc-500 dark:text-zinc-400">Base de datos</span>
                <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ strtoupper($dbConnection) }}</p>
            </div>
        </div>
        <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
            Estos valores se configuran en el archivo <code>.env</code> del servidor.
        </p>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-700 dark:text-zinc-200">
                <i class="bi bi-hdd-fill text-green-500"></i> Respaldo de Base de Datos
            </h3>
            <button wire:click="generarRespaldo" wire:loading.attr="disabled"
                class="rounded bg-green-600 px-3 py-2 text-sm text-white transition hover:bg-green-700 disabled:opacity-50 dark:bg-green-500 dark:hover:bg-green-600 cursor-pointer">
                <span wire:loading.remove wire:target="generarRespaldo"><i class="bi bi-play-fill"></i> Generar Respaldo Ahora</span>
                <span wire:loading wire:target="generarRespaldo">Generando...</span>
            </button>
        </div>

        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full border border-gray-300 text-sm dark:border-zinc-700">
                <thead class="bg-gray-100 dark:bg-zinc-800">
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-left text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ARCHIVO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">TAMAÑO (MB)</th>
                    <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">FECHA</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @forelse ($backups as $backup)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $backup['nombre'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $backup['tamano'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $backup['fecha']->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-3 text-center text-gray-500 dark:text-zinc-400">No hay respaldos generados todavía.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between">
            <h3 class="flex items-center gap-2 text-lg font-semibold text-gray-700 dark:text-zinc-200">
                <i class="bi bi-clock-history text-purple-500"></i> Historial de Actividad (Auditoría)
            </h3>
            <button wire:click="exportarHistorial"
                class="rounded bg-blue-600 px-3 py-2 text-sm text-white transition hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 cursor-pointer">
                <i class="bi bi-file-earmark-excel-fill"></i> Exportar Historial Completo
            </button>
        </div>
        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
            Exporta hasta los últimos 5,000 registros de auditoría (creaciones, ediciones y eliminaciones) registrados por el sistema.
        </p>
    </div>

</div>
