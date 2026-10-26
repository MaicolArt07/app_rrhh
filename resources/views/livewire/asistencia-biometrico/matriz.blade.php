@php
    $justificados = ['VACACION', 'FERIADO', 'REMOTO', 'TRABAJO_FUERA', 'BAJA_PERMISO'];
    $etiquetaCorta = [
        'VACACION' => 'VACACIÓN', 'FERIADO' => 'FERIADO', 'REMOTO' => 'REMOTO',
        'TRABAJO_FUERA' => 'FUERA', 'BAJA_PERMISO' => 'PERMISO',
    ];
    $noAplica = $tipos['NO_APLICA'];
@endphp

<div class="p-6 space-y-4">

    {{-- Filtros y acciones --}}
    <div class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300">Mes</label>
            <input type="month" wire:model.live="mes"
                   class="rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
        </div>
        <div>
            <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300">Empresa</label>
            <select wire:model.live="empresaFiltro"
                    class="rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                <option value="">Todas las empresas</option>
                @foreach ($empresas as $empresa)
                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300">Área</label>
            <select wire:model.live="areaFiltro"
                    class="rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                <option value="">Todas las áreas</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-300">Empleado</label>
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Nombre, código o ID del reloj..."
                   class="w-full max-w-xs rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
        </div>
        <div class="flex gap-2">
            @can('Importar Asistencia')
                <a href="{{ route('asistencia.importar') }}" wire:navigate
                   class="rounded bg-green-600 px-3 py-2 text-sm text-white hover:bg-green-700">
                    <i class="bi bi-upload"></i> Importar
                </a>
            @endcan
            @if ($puedeEditar)
                <button wire:click="recalcular" wire:loading.attr="disabled" title="Aplicar feriados, permisos y días remotos registrados después de importar"
                        class="rounded bg-zinc-600 px-3 py-2 text-sm text-white hover:bg-zinc-700 cursor-pointer">
                    <i class="bi bi-arrow-clockwise"></i> Recalcular
                </button>
            @endif
            <button wire:click="exportar"
                    class="rounded bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700 cursor-pointer">
                <i class="bi bi-file-earmark-excel"></i> Exportar
            </button>
        </div>
    </div>

    {{-- Leyenda --}}
    <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs text-zinc-700 dark:text-zinc-200">
        @foreach ($tipos as $tipo)
            @continue($tipo->codigo === 'PUNTUAL')
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-block h-3.5 w-3.5 rounded-sm border border-zinc-400" style="background: {{ $tipo->color_hex }}"></span>
                {{ $tipo->nombre }}{{ $tipo->codigo === 'ATRASO' ? ' (entrada después de las '.\Illuminate\Support\Carbon::createFromTime(0)->addMinutes(\App\Services\Asistencia\ClasificadorAsistencia::minutos(config('asistencia.hora_entrada')) + config('asistencia.tolerancia_minutos'))->format('H:i').')' : '' }}
            </span>
        @endforeach
        <span class="inline-flex items-center gap-1.5"><i class="bi bi-pencil-fill text-[10px]"></i> Cambiado a mano</span>
    </div>

    {{-- Pestañas --}}
    <div class="flex gap-1 border-b border-zinc-200 dark:border-zinc-700">
        @foreach (['matriz' => 'Matriz', 'resumen' => 'Resumen y multa', 'detalle' => 'Detalle de eventos', 'incompletos' => 'Sin marcar e incompletos'] as $clave => $titulo)
            <button wire:click="$set('tab', '{{ $clave }}')"
                    class="rounded-t px-4 py-2 text-sm cursor-pointer {{ $tab === $clave ? 'border border-b-0 border-zinc-200 bg-white font-semibold text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' }}">
                {{ $titulo }}
            </button>
        @endforeach
        <span class="ml-auto self-center text-sm text-zinc-500 dark:text-zinc-400">{{ $tituloMes }} · {{ $filas->count() }} empleados</span>
    </div>

    @if ($filas->isEmpty())
        <div class="rounded border border-dashed border-zinc-300 p-8 text-center text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
            No hay asistencias importadas para {{ $tituloMes }}.
            @can('Importar Asistencia')
                <a href="{{ route('asistencia.importar') }}" wire:navigate class="text-blue-600 hover:underline dark:text-blue-400">Importar el reporte del reloj</a>.
            @endcan
        </div>
    @elseif ($tab === 'matriz')
        <div class="max-h-[70vh] overflow-auto rounded-lg border border-gray-300 dark:border-zinc-700">
            <table class="border-collapse text-[11px]">
                <thead class="sticky top-0 z-20 bg-gray-100 dark:bg-zinc-800">
                <tr>
                    <th class="sticky left-0 z-30 min-w-[44px] border border-gray-300 bg-gray-100 px-2 py-1 text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">ID</th>
                    <th class="sticky left-[44px] z-30 min-w-[170px] border border-gray-300 bg-gray-100 px-2 py-1 text-left text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">Nombre</th>
                    @foreach ($dias as $dia)
                        <th class="min-w-[54px] border border-gray-300 px-1 py-1 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">
                            {{ $dia->day }}<br><span class="font-normal text-zinc-500 dark:text-zinc-400">{{ ucfirst($dia->translatedFormat('D')) }}</span>
                        </th>
                    @endforeach
                    <th class="border border-gray-300 px-2 py-1 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Eventos</th>
                    <th class="border border-gray-300 px-2 py-1 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Multa</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($filas as $fila)
                    @php $empleado = $fila['empleado']; @endphp
                    <tr wire:key="fila-{{ $empleado->id }}">
                        <td class="sticky left-0 z-10 border border-gray-300 bg-white px-2 py-1 text-center text-zinc-800 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">{{ $empleado->codigo_biometrico ?? '—' }}</td>
                        <td class="sticky left-[44px] z-10 border border-gray-300 bg-white px-2 py-1 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            {{ $empleado->persona?->nombreCompleto() }}
                            <span class="block text-[10px] text-zinc-500 dark:text-zinc-400">{{ $empleado->area?->nombre }}</span>
                        </td>
                        @foreach ($dias as $dia)
                            @php
                                $asistencia = $fila['dias'][$dia->toDateString()] ?? null;
                                $tipo = $asistencia?->tipoAsistencia ?? $noAplica;
                                $marcas = $asistencia?->marcaciones ?? [];
                                $titulo = $tipo->nombre
                                    .($asistencia?->minutos_atraso ? " · {$asistencia->minutos_atraso} min" : '')
                                    .($asistencia?->numero_evento_mes ? " · evento n.º {$asistencia->numero_evento_mes}" : '')
                                    .($asistencia && (float) $asistencia->multa_dia > 0 ? ' · Bs '.number_format($asistencia->multa_dia, 0) : '')
                                    .($asistencia?->tipo_asignado_manual ? ' · cambiado a mano' : '')
                                    .($asistencia?->observaciones ? " · {$asistencia->observaciones}" : '');
                            @endphp
                            <td wire:key="c-{{ $empleado->id }}-{{ $dia->day }}"
                                @if ($asistencia && $puedeEditar) wire:click="abrirCelda({{ $asistencia->id }})" @endif
                                title="{{ $titulo }}"
                                class="relative border border-gray-300 px-1 py-1 text-center leading-tight dark:border-zinc-700 {{ $asistencia && $puedeEditar ? 'cursor-pointer hover:outline hover:outline-2 hover:outline-blue-500' : '' }}"
                                style="background: {{ $tipo->codigo === 'PUNTUAL' ? 'transparent' : $tipo->color_hex }}; color: {{ $tipo->codigo === 'PUNTUAL' ? 'inherit' : $tipo->colorTexto() }}">
                                @if (! $asistencia)
                                    —
                                @elseif (in_array($tipo->codigo, $justificados))
                                    <span class="font-semibold">{{ $etiquetaCorta[$tipo->codigo] }}</span>
                                    @if ($marcas)
                                        <span class="block text-[9px] opacity-75">{{ implode(' ', $marcas) }}</span>
                                    @endif
                                @else
                                    {!! implode('<br>', array_map('e', $marcas)) !!}
                                @endif
                                @if ($asistencia?->tipo_asignado_manual)
                                    <i class="bi bi-pencil-fill absolute right-0.5 top-0.5 text-[8px]"></i>
                                @endif
                            </td>
                        @endforeach
                        <td class="border border-gray-300 px-2 py-1 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['totales']['eventos'] }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-center font-semibold dark:border-zinc-700 {{ $fila['totales']['multa'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-800 dark:text-zinc-100' }}">
                            {{ number_format($fila['totales']['multa'], 0) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">
            Eventos sancionables = atrasos + días sin marcar. Los primeros {{ config('asistencia.eventos_perdonados') }} del mes se perdonan;
            desde el siguiente, Bs {{ config('asistencia.multa_por_evento') }} por evento. «Incompleto» no suma. La multa no se descuenta en la planilla.
            @if ($puedeEditar) Haga clic en un día para cambiar su tipo. @endif
        </p>

    @elseif ($tab === 'resumen')
        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full border border-gray-300 text-sm dark:border-zinc-700">
                <thead class="bg-gray-100 dark:bg-zinc-800">
                <tr>
                    @foreach (['ID', 'NOMBRE', 'ÁREA', 'ATRASOS', 'MIN. ATRASO', 'INCOMPLETOS', 'SIN MARCAR', 'EVENTOS', 'MULTA (Bs)'] as $encabezado)
                        <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">{{ $encabezado }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @foreach ($filas as $fila)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['empleado']->codigo_biometrico }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['empleado']->persona?->nombreCompleto() }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['empleado']->area?->nombre }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700" style="{{ $fila['totales']['atrasos'] ? 'background:'.$tipos['ATRASO']->color_hex.';color:'.$tipos['ATRASO']->colorTexto() : '' }}">{{ $fila['totales']['atrasos'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['totales']['minutos'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['totales']['incompletos'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['totales']['sin_marcar'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['totales']['eventos'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center font-semibold dark:border-zinc-700 {{ $fila['totales']['multa'] > 0 ? 'bg-red-600 text-white' : 'text-zinc-800 dark:text-zinc-100' }}">{{ number_format($fila['totales']['multa'], 0) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-semibold dark:bg-zinc-800">
                <tr>
                    <td colspan="3" class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">TOTAL</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $totalesGenerales['atrasos'] }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $totalesGenerales['minutos'] }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $totalesGenerales['incompletos'] }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $totalesGenerales['sin_marcar'] }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $totalesGenerales['eventos'] }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ number_format($totalesGenerales['multa'], 0) }}</td>
                </tr>
                </tfoot>
            </table>
        </div>

    @elseif ($tab === 'detalle')
        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full border border-gray-300 text-sm dark:border-zinc-700">
                <thead class="bg-gray-100 dark:bg-zinc-800">
                <tr>
                    @foreach (['NOMBRE', 'FECHA', 'DÍA', 'TIPO', 'ENTRADA', 'MIN. ATRASO', 'N.º EVENTO', 'MULTA DEL DÍA (Bs)'] as $encabezado)
                        <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">{{ $encabezado }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @foreach ($filas as $fila)
                    @php $eventos = $fila['dias']->where('es_sancionable', true)->sortBy('fecha'); @endphp
                    @continue($eventos->isEmpty())
                    @foreach ($eventos as $evento)
                        <tr>
                            <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['empleado']->persona?->nombreCompleto() }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $evento->fecha->format('d/m/Y') }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ ucfirst($evento->fecha->translatedFormat('l')) }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700" style="background: {{ $evento->tipoAsistencia->color_hex }}; color: {{ $evento->tipoAsistencia->colorTexto() }}">{{ $evento->tipoAsistencia->nombre }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $evento->hora_entrada?->format('H:i') ?? '—' }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $evento->minutos_atraso ?: '—' }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $evento->numero_evento_mes }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700 {{ (float) $evento->multa_dia > 0 ? 'bg-red-600 font-semibold text-white' : 'text-zinc-800 dark:text-zinc-100' }}">{{ number_format($evento->multa_dia, 0) }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-100 font-semibold dark:bg-zinc-800">
                        <td colspan="5" class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">TOTAL — {{ $fila['empleado']->persona?->nombreCompleto() }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['totales']['minutos'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['totales']['eventos'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700 {{ $fila['totales']['multa'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-800 dark:text-zinc-100' }}">{{ number_format($fila['totales']['multa'], 0) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    @elseif ($tab === 'incompletos')
        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full border border-gray-300 text-sm dark:border-zinc-700">
                <thead class="bg-gray-100 dark:bg-zinc-800">
                <tr>
                    @foreach (['NOMBRE', 'FECHA', 'DÍA', 'TIPO', 'MARCA REGISTRADA', '¿SUMA A LA MULTA?'] as $encabezado)
                        <th class="border border-gray-300 px-3 py-2 text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">{{ $encabezado }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900">
                @foreach ($filas as $fila)
                    @foreach ($fila['dias']->filter(fn ($a) => in_array($a->tipoAsistencia?->codigo, ['SIN_MARCAR', 'INCOMPLETO']))->sortBy('fecha') as $dia)
                        <tr>
                            <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $fila['empleado']->persona?->nombreCompleto() }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $dia->fecha->format('d/m/Y') }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ ucfirst($dia->fecha->translatedFormat('l')) }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700" style="background: {{ $dia->tipoAsistencia->color_hex }}; color: {{ $dia->tipoAsistencia->colorTexto() }}">{{ $dia->tipoAsistencia->nombre }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center font-mono text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ implode(' ', $dia->marcaciones ?? []) ?: '—' }}</td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $dia->es_sancionable ? 'Sí' : 'No' }}</td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Modal para cambiar el tipo de un día --}}
    @if ($celda)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
            <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-md dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="mb-1 text-center text-lg font-semibold text-zinc-800 dark:text-zinc-100">{{ $celda->empleado->persona?->nombreCompleto() }}</h2>
                <p class="mb-4 text-center text-sm text-zinc-500 dark:text-zinc-400">{{ ucfirst($celda->fecha->translatedFormat('l d/m/Y')) }}</p>

                <dl class="mb-4 grid grid-cols-2 gap-2 text-sm">
                    <dt class="text-zinc-500 dark:text-zinc-400">Marcaciones</dt>
                    <dd class="font-mono text-zinc-800 dark:text-zinc-100">{{ implode(' ', $celda->marcaciones ?? []) ?: 'Ninguna' }}</dd>
                    <dt class="text-zinc-500 dark:text-zinc-400">Tipo actual</dt>
                    <dd>
                        <span class="rounded px-2 py-0.5 text-xs font-semibold" style="background: {{ $celda->tipoAsistencia->color_hex }}; color: {{ $celda->tipoAsistencia->colorTexto() }}">{{ $celda->tipoAsistencia->nombre }}</span>
                        @if ($celda->minutos_atraso) <span class="text-xs text-zinc-500">({{ $celda->minutos_atraso }} min)</span> @endif
                    </dd>
                    @if ($celda->usuarioModifico)
                        <dt class="text-zinc-500 dark:text-zinc-400">Modificado por</dt>
                        <dd class="text-zinc-800 dark:text-zinc-100">{{ $celda->usuarioModifico->name }} · {{ $celda->updated_at->format('d/m/Y H:i') }}</dd>
                    @endif
                </dl>

                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="font-semibold text-zinc-700 dark:text-zinc-200">Asignar tipo</label>
                        <select wire:model="celdaTipo"
                                class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                            <option value="">Automático (según feriados, permisos y marcaciones)</option>
                            @foreach ($tipos->where('seleccionable_manual', true)->where('estado', true) as $tipo)
                                <option value="{{ $tipo->codigo }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                        @error('celdaTipo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">«Remoto» se guarda como día remoto asignado al empleado.</p>
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-zinc-700 dark:text-zinc-200">Observación</label>
                        <textarea wire:model="celdaObservacion" rows="2"
                                  class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
                        @error('celdaObservacion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="cerrarCelda" class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700 cursor-pointer">Cancelar</button>
                    <button wire:click="guardarCelda" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 cursor-pointer">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>
