<div class="flex h-full w-full flex-1 flex-col gap-6 p-4">

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

        <div class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-2xl dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-black/20">
            <div class="flex items-center justify-between">
                <div class="text-lg font-semibold text-gray-700 dark:text-zinc-200">Empleados Activos</div>
                <i class="bi bi-people-fill text-2xl text-blue-500 dark:text-blue-400"></i>
            </div>
            <div class="mt-4 text-center text-4xl font-bold text-blue-600 dark:text-blue-400">
                {{ $empleadosActivos }}
            </div>
        </div>

        <div class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-2xl dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-black/20">
            <div class="flex items-center justify-between">
                <div class="text-lg font-semibold text-gray-700 dark:text-zinc-200">Nómina del Mes</div>
                <i class="bi bi-cash-stack text-2xl text-green-500 dark:text-green-400"></i>
            </div>
            <div class="mt-4 text-center text-3xl font-bold text-green-600 dark:text-green-400">
                Q {{ number_format($nominaDelMes, 2) }}
            </div>
        </div>

        <div class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-2xl dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-black/20">
            <div class="flex items-center justify-between">
                <div class="text-lg font-semibold text-gray-700 dark:text-zinc-200">Ausentismo (mes)</div>
                <i class="bi bi-person-dash-fill text-2xl text-red-500 dark:text-red-400"></i>
            </div>
            <div class="mt-4 text-center text-4xl font-bold text-red-600 dark:text-red-400">
                {{ $ausentismo }}%
            </div>
        </div>

        <div class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-2xl dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-black/20">
            <div class="flex items-center justify-between">
                <div class="text-lg font-semibold text-gray-700 dark:text-zinc-200">Anticipos Activos</div>
                <i class="bi bi-piggy-bank-fill text-2xl text-purple-500 dark:text-purple-400"></i>
            </div>
            <div class="mt-4 text-center text-3xl font-bold text-purple-600 dark:text-purple-400">
                {{ $anticiposActivos }}
            </div>
            <div class="text-center text-xs text-zinc-500 dark:text-zinc-400">Q {{ number_format($anticiposMontoPendiente, 2) }} pendiente</div>
        </div>

    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
            <h3 class="mb-3 flex items-center gap-2 text-lg font-semibold text-gray-700 dark:text-zinc-200">
                <i class="bi bi-file-earmark-text-fill text-yellow-500"></i> Contratos por vencer (30 días)
            </h3>
            <ul class="divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse ($contratosPorVencer as $contrato)
                    <li class="flex items-center justify-between py-2 text-sm">
                        <span class="text-zinc-800 dark:text-zinc-100">{{ $contrato->empleado->persona?->nombreCompleto() }}</span>
                        <span class="text-xs text-red-600 dark:text-red-400">{{ $contrato->fecha_fin->format('d/m/Y') }}</span>
                    </li>
                @empty
                    <li class="py-2 text-sm text-zinc-500 dark:text-zinc-400">No hay contratos por vencer.</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
            <h3 class="mb-3 flex items-center gap-2 text-lg font-semibold text-gray-700 dark:text-zinc-200">
                <i class="bi bi-folder-fill text-yellow-500"></i> Documentos por vencer (30 días)
            </h3>
            <ul class="divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse ($documentosPorVencer as $documento)
                    <li class="flex items-center justify-between py-2 text-sm">
                        <span class="text-zinc-800 dark:text-zinc-100">{{ $documento->empleado->persona?->nombreCompleto() }}</span>
                        <span class="text-xs text-red-600 dark:text-red-400">{{ $documento->fecha_vencimiento->format('d/m/Y') }}</span>
                    </li>
                @empty
                    <li class="py-2 text-sm text-zinc-500 dark:text-zinc-400">No hay documentos por vencer.</li>
                @endforelse
            </ul>
        </div>

    </div>

</div>
