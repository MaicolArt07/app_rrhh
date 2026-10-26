<div class="flex h-full w-full flex-1 flex-col gap-6 p-4">

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

        <div class="flex flex-col justify-between rounded-xl border border-primary/40 bg-primary p-5 shadow-lg transition-all duration-300 hover:shadow-2xl">
            <div class="flex items-center justify-between gap-2">
                <div class="text-sm font-semibold text-white">Empleados Activos</div>
                <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-white/20">
                    <i class="bi bi-people-fill text-base text-white"></i>
                </div>
            </div>
            <div class="mt-3 text-center text-2xl font-bold text-white">
                {{ $empleadosActivos }}
            </div>
        </div>

        <div class="flex flex-col justify-between rounded-xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg transition-all duration-300 hover:shadow-2xl dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between gap-2">
                <div class="text-sm font-semibold text-zinc-200">Nómina del Mes</div>
                <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/20">
                    <i class="bi bi-cash-stack text-base text-primary"></i>
                </div>
            </div>
            <div class="mt-3 text-center text-xl font-bold text-primary">
                Q {{ number_format($nominaDelMes, 2) }}
            </div>
        </div>

        <div class="flex flex-col justify-between rounded-xl border border-zinc-300 bg-deltax-gray p-5 shadow-lg transition-all duration-300 hover:shadow-2xl dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center justify-between gap-2">
                <div class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Ausentismo (mes)</div>
                <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/15">
                    <i class="bi bi-person-dash-fill text-base text-primary"></i>
                </div>
            </div>
            <div class="mt-3 text-center text-2xl font-bold text-zinc-800 dark:text-zinc-100">
                {{ $ausentismo }}%
            </div>
        </div>

        <div class="flex flex-col justify-between rounded-xl border-2 border-primary bg-white p-5 shadow-lg transition-all duration-300 hover:shadow-2xl dark:bg-zinc-900">
            <div class="flex items-center justify-between gap-2">
                <div class="text-sm font-semibold text-gray-700 dark:text-zinc-200">Anticipos Activos</div>
                <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/15">
                    <i class="bi bi-piggy-bank-fill text-base text-primary"></i>
                </div>
            </div>
            <div class="mt-3 text-center text-xl font-bold text-primary">
                {{ $anticiposActivos }}
            </div>
            <div class="text-center text-xs text-zinc-500 dark:text-zinc-400">Q {{ number_format($anticiposMontoPendiente, 2) }} pendiente</div>
        </div>

    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div
            class="rounded-xl border border-zinc-200 bg-white p-5 shadow-lg dark:border-zinc-700 dark:bg-zinc-900 lg:col-span-2"
            wire:ignore
            x-data="{
                init() {
                    new Chart(this.$refs.nominaChart, {
                        type: 'bar',
                        data: {
                            labels: @js($nominaLabels),
                            datasets: [{
                                label: 'Nómina (Q)',
                                data: @js($nominaData),
                                backgroundColor: '#f39200',
                                borderRadius: 6,
                                maxBarThickness: 48,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, ticks: { callback: (v) => 'Q ' + v.toLocaleString() } },
                            },
                        },
                    });
                },
            }"
        >
            <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-zinc-200">
                <i class="bi bi-bar-chart-line-fill text-primary"></i> Nómina (últimos 6 meses)
            </h3>
            <div class="h-64">
                <canvas x-ref="nominaChart"></canvas>
            </div>
        </div>

        <div
            class="rounded-xl border border-zinc-200 bg-white p-5 shadow-lg dark:border-zinc-700 dark:bg-zinc-900"
            wire:ignore
            x-data="{
                init() {
                    const data = @js($empleadosPorArea);
                    new Chart(this.$refs.areaChart, {
                        type: 'doughnut',
                        data: {
                            labels: data.map((d) => d.label),
                            datasets: [{
                                data: data.map((d) => d.total),
                                backgroundColor: ['#f39200', '#111827', '#6b7280', '#facc15', '#a3a3a3', '#7c2d12'],
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
                        },
                    });
                },
            }"
        >
            <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-zinc-200">
                <i class="bi bi-diagram-3-fill text-primary"></i> Empleados por Área
            </h3>
            <div class="h-64">
                <canvas x-ref="areaChart"></canvas>
            </div>
        </div>

    </div>

    <div
        class="rounded-xl border border-zinc-200 bg-white p-5 shadow-lg dark:border-zinc-700 dark:bg-zinc-900"
        wire:ignore
        x-data="{
            init() {
                new Chart(this.$refs.asistenciaChart, {
                    type: 'line',
                    data: {
                        labels: @js($asistenciaLabels),
                        datasets: [
                            {
                                label: 'Presentes',
                                data: @js($asistenciaPresentes),
                                borderColor: '#f39200',
                                backgroundColor: 'rgba(243, 146, 0, 0.15)',
                                tension: 0.35,
                                fill: true,
                            },
                            {
                                label: 'Ausentes',
                                data: @js($asistenciaAusentes),
                                borderColor: '#dc2626',
                                backgroundColor: 'rgba(220, 38, 38, 0.1)',
                                tension: 0.35,
                                fill: true,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                    },
                });
            },
        }"
    >
        <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-zinc-200">
            <i class="bi bi-graph-up text-primary"></i> Asistencia (últimos 7 días)
        </h3>
        <div class="h-64">
            <canvas x-ref="asistenciaChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
            <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-zinc-200">
                <i class="bi bi-file-earmark-text-fill text-yellow-500"></i> Alertas de contratos
            </h3>
            <ul class="divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse ($contratosPorVencer as $contrato)
                    <li class="flex items-center justify-between gap-2 py-2 text-sm">
                        <span class="text-zinc-800 dark:text-zinc-100">
                            {{ $contrato->empleado->persona?->nombreCompleto() }}
                            @if ($contrato->esUltimaPrestacion())
                                <span class="block text-xs font-semibold text-orange-600 dark:text-orange-400">2.º contrato por prestación</span>
                            @endif
                        </span>
                        <span class="whitespace-nowrap text-xs {{ $contrato->vencido() ? 'font-semibold text-red-600 dark:text-red-400' : 'text-yellow-700 dark:text-yellow-400' }}">
                            @if ($contrato->fecha_fin)
                                {{ $contrato->vencido() ? 'Venció' : 'Vence' }} {{ $contrato->fecha_fin->format('d/m/Y') }}
                            @else
                                Sin fecha fin
                            @endif
                        </span>
                    </li>
                @empty
                    <li class="py-2 text-sm text-zinc-500 dark:text-zinc-400">No hay contratos por vencer.</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
            <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-zinc-200">
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
