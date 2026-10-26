<div class="p-6 space-y-6">

    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <h2 class="mb-1 text-lg font-semibold text-zinc-800 dark:text-zinc-100">
            <i class="bi bi-upload me-1"></i> Importar reporte del reloj biométrico
        </h2>
        <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">
            Seleccione el archivo crudo del reloj (por ejemplo <span class="font-mono">1_report.xls</span>). Se lee la hoja
            «{{ config('asistencia.hoja_reporte') }}». Si reimporta un periodo, los días cambiados a mano en la matriz se conservan.
        </p>

        <input type="file" wire:model="archivo" accept=".xls,.xlsx"
               class="w-full max-w-lg rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
        <div wire:loading wire:target="archivo" class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Leyendo archivo...</div>
        @error('archivo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

        @if ($resultado)
            <div class="mt-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-200">
                <i class="bi bi-check-circle-fill"></i>
                Se importaron {{ $resultado['empleados'] }} empleados. {{ $resultado['no_vinculados'] }} bloques del reloj quedaron sin vincular.
                <a href="{{ route('asistencia.matriz', ['mes' => $resultado['mes']]) }}" wire:navigate class="ml-2 font-semibold text-blue-700 underline dark:text-blue-300">Ver la matriz</a>
            </div>
        @endif
    </div>

    @if ($reporte)
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4 flex flex-wrap items-center gap-4 text-sm">
                <span class="text-zinc-800 dark:text-zinc-100"><strong>Periodo:</strong> {{ $periodo }}</span>
                <span class="text-zinc-800 dark:text-zinc-100"><strong>Bloques en el archivo:</strong> {{ $totalBloques }}</span>
                <span class="text-green-700 dark:text-green-400"><strong>Vinculados:</strong> {{ $totalVinculados }}</span>
                @if ($sinVincularConMarcas)
                    <span class="text-red-600 dark:text-red-400"><strong>Con marcas sin vincular:</strong> {{ $sinVincularConMarcas }}</span>
                @endif
                <label class="ml-auto flex items-center gap-2 text-zinc-700 dark:text-zinc-200">
                    <input type="checkbox" wire:model.live="mostrarSinMarcas" class="rounded border-gray-300 dark:border-zinc-600">
                    Mostrar bloques sin marcas y sin vincular
                </label>
            </div>

            <p class="mb-3 text-xs text-zinc-500 dark:text-zinc-400">
                Revise el empleado de cada ID del reloj. Los marcados «sugerido» se encontraron por el nombre; el vínculo se guarda
                en el empleado y se usará en las próximas importaciones. Deje «No vincular» para ignorar un bloque.
            </p>

            @error('vinculos') <p class="mb-3 text-sm text-red-600">{{ $message }}</p> @enderror

            <div class="overflow-x-auto rounded-lg">
                <table class="min-w-full border border-gray-300 text-sm dark:border-zinc-700">
                    <thead class="bg-gray-100 dark:bg-zinc-800">
                    <tr>
                        <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ID RELOJ</th>
                        <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">NOMBRE EN EL RELOJ</th>
                        <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">DÍAS CON MARCA</th>
                        <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">EMPLEADO</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900">
                    @foreach ($bloques as $bloque)
                        @php
                            $codigo = $bloque['codigo'];
                            $idVinculado = $vinculos[$codigo] ?? '';
                            $duplicado = $idVinculado && in_array((int) $idVinculado, $duplicados);
                        @endphp
                        <tr wire:key="bloque-{{ $codigo }}" class="{{ $duplicado ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                            <td class="border border-gray-300 px-3 py-2 text-center font-mono text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $codigo }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $bloque['nombre'] ?: '—' }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                                {{ $bloque['dias_con_marca'] }}
                                @if ($idVinculado && $bloque['dias_con_marca'] === 0)
                                    <span class="block text-xs text-orange-600 dark:text-orange-400">Sin marcas: todos los días saldrán «Sin marcar». Si trabaja en remoto, asigne sus días remotos.</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-3 py-2 dark:border-zinc-700">
                                <div class="flex items-center gap-2">
                                    <select wire:model.live="vinculos.{{ $codigo }}"
                                            class="w-full rounded border border-gray-300 bg-white px-2 py-1 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                        <option value="">— No vincular —</option>
                                        @foreach ($empleados as $empleado)
                                            <option value="{{ $empleado->id }}">{{ $empleado->persona?->nombreCompleto() }}{{ $empleado->estado ? '' : ' (inactivo)' }}</option>
                                        @endforeach
                                    </select>
                                    @if (($origenVinculo[$codigo] ?? '') === 'sugerido' && $idVinculado)
                                        <span class="whitespace-nowrap rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">sugerido</span>
                                    @elseif (($origenVinculo[$codigo] ?? '') === 'vinculado' && $idVinculado)
                                        <span class="whitespace-nowrap rounded bg-green-100 px-2 py-0.5 text-xs text-green-700 dark:bg-green-900/40 dark:text-green-300">vinculado</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-end gap-3">
                <button wire:click="cancelar"
                        class="rounded bg-red-600 px-4 py-2 text-white transition hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 cursor-pointer">
                    Cancelar
                </button>
                <button wire:click="importar" wire:loading.attr="disabled" wire:target="importar"
                        class="rounded bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 disabled:opacity-60 dark:bg-blue-500 dark:hover:bg-blue-600 cursor-pointer">
                    <span wire:loading.remove wire:target="importar">Confirmar importación</span>
                    <span wire:loading wire:target="importar">Importando...</span>
                </button>
            </div>
        </div>
    @endif

    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <h3 class="mb-3 text-sm font-semibold text-zinc-700 dark:text-zinc-200">Últimas importaciones</h3>
        <table class="min-w-full border border-gray-300 text-sm dark:border-zinc-700">
            <thead class="bg-gray-100 dark:bg-zinc-800">
            <tr>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">FECHA</th>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ARCHIVO</th>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">PERIODO</th>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">EMPLEADOS</th>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">SIN VINCULAR</th>
                <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">USUARIO</th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900">
            @forelse ($importaciones as $imp)
                <tr>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $imp->created_at->format('d/m/Y H:i') }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $imp->nombre_archivo }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        <a href="{{ route('asistencia.matriz', ['mes' => $imp->periodo_inicio->format('Y-m')]) }}" wire:navigate class="text-blue-600 hover:underline dark:text-blue-400">
                            {{ $imp->periodo_inicio->format('d/m/Y') }} – {{ $imp->periodo_fin->format('d/m/Y') }}
                        </a>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $imp->total_empleados }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $imp->total_no_vinculados }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $imp->usuario?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-3 text-center text-gray-500 dark:text-zinc-400">Todavía no se importó ningún reporte.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
