<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-3xl dark:border-zinc-700 dark:bg-zinc-900 max-h-[90vh] overflow-y-auto">

        <h2 class="mb-1 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">Historial de Contratos</h2>
        <p class="mb-4 text-center text-sm text-zinc-500 dark:text-zinc-400">
            {{ $empleadoHistorial?->persona?->nombreCompleto() }} · {{ $empleadoHistorial?->codigo_empleado }}
            · Ingreso {{ $empleadoHistorial?->fecha_ingreso?->format('d/m/Y') }}
        </p>

        <table class="min-w-full border border-gray-300 text-sm dark:border-zinc-700">
            <thead class="bg-gray-100 dark:bg-zinc-800">
            <tr>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">N.º</th>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">EMPRESA</th>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">TIPO</th>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">INICIO</th>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">FIN</th>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">SITUACIÓN</th>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">DOCUMENTO</th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900">
            @foreach ($historial as $contrato)
                <tr>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $contrato->etiquetaNumero() }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $contrato->empresa?->nombre ?? '—' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $contrato->tipoContrato?->nombre }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $contrato->fecha_inicio->format('d/m/Y') }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $contrato->fecha_fin?->format('d/m/Y') ?? '—' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $contrato->situacion() }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        @if ($contrato->getFirstMediaUrl('documento'))
                            <a href="{{ $contrato->getFirstMediaUrl('documento') }}" target="_blank" title="Contrato firmado" class="text-blue-600 dark:text-blue-400">
                                <i class="bi bi-file-earmark-pdf-fill text-lg"></i>
                            </a>
                        @endif
                        <button wire:click="abrirDocumento({{ $contrato->id }})" title="Descargar contrato (PDF)" class="text-indigo-600 dark:text-indigo-400 cursor-pointer">
                            <i class="bi bi-file-earmark-arrow-down-fill text-lg"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
            Contratos por prestación de servicios: {{ $historial->filter(fn ($c) => $c->esPrestacion())->count() }} de {{ \App\Models\Contrato::MAX_PRESTACION }} permitidos.
        </p>

        <div class="mt-6 flex justify-end">
            <button wire:click="cerrarHistorial"
                    class="rounded bg-zinc-600 px-4 py-2 text-white transition hover:bg-zinc-700 cursor-pointer">
                Cerrar
            </button>
        </div>
    </div>
</div>
