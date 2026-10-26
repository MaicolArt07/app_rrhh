<div class="flex h-full flex-col bg-zinc-200 p-4 dark:bg-zinc-950">

    {{-- Resumen de alertas --}}
    @if ($resumenAlertas['por_vencer'] || $resumenAlertas['vencidos'] || $resumenAlertas['segundo'])
        <div class="mb-3 flex flex-wrap items-center gap-3 rounded border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm dark:border-yellow-700 dark:bg-yellow-900/20">
            <i class="bi bi-exclamation-triangle-fill text-lg text-yellow-600 dark:text-yellow-400"></i>
            @if ($resumenAlertas['vencidos'])
                <span class="font-semibold text-red-700 dark:text-red-400">{{ $resumenAlertas['vencidos'] }} vencido(s)</span>
            @endif
            @if ($resumenAlertas['por_vencer'])
                <span class="font-semibold text-yellow-700 dark:text-yellow-300">{{ $resumenAlertas['por_vencer'] }} por vencer en {{ \App\Models\Contrato::DIAS_ALERTA }} días</span>
            @endif
            @if ($resumenAlertas['segundo'])
                <span class="font-semibold text-orange-700 dark:text-orange-300">{{ $resumenAlertas['segundo'] }} en su 2.º contrato por prestación</span>
            @endif
            <button wire:click="$set('soloAlertas', true); buscar" class="ml-auto text-primary hover:underline cursor-pointer">Ver solo alertas</button>
        </div>
    @endif

    <div class="mb-3 flex flex-wrap items-center justify-between gap-3 rounded border border-zinc-300 bg-zinc-100 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-900">
        <button wire:click="openModal"
            class="flex items-center gap-2 rounded border border-zinc-400 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
            <i class="bi bi-plus-lg text-primary"></i> Nuevo
        </button>

        <div class="flex flex-1 flex-wrap items-center justify-end gap-2">
            <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200">
                <input type="checkbox" wire:model="soloAlertas" class="rounded border-zinc-400 dark:border-zinc-600">
                Solo alertas
            </label>

            <select wire:model="situacionFiltro"
                class="rounded border border-zinc-400 bg-white px-2 py-1.5 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                <option value="vigentes">Vigentes</option>
                <option value="historial">Historial (finalizados)</option>
                <option value="todos">Todos</option>
            </select>

            <select wire:model="empresaFiltro"
                class="rounded border border-zinc-400 bg-white px-2 py-1.5 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                <option value="">Todas las empresas</option>
                @foreach ($empresas as $empresa)
                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                @endforeach
            </select>

            <select wire:model="tipoFiltro"
                class="rounded border border-zinc-400 bg-white px-2 py-1.5 text-sm text-zinc-800 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100">
                <option value="">Todos los tipos</option>
                @foreach ($tiposContrato as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                @endforeach
            </select>

            <input type="text" wire:model="search" wire:keydown.enter="buscar"
                class="w-full max-w-xs rounded border border-zinc-400 bg-white px-2 py-1.5 text-sm text-zinc-800 placeholder:text-zinc-400 focus:border-primary focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                placeholder="Buscar por empleado...">

            <button wire:click="buscar"
                class="flex items-center gap-2 rounded border border-zinc-400 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                <i class="bi bi-search"></i> Buscar
            </button>

            @if ($buscado)
                <button wire:click="limpiarFiltros"
                    class="flex items-center gap-2 rounded border border-zinc-400 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 cursor-pointer dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                    <i class="bi bi-x-lg"></i> Limpiar
                </button>
            @endif
        </div>
    </div>

    @if (!$buscado)
        <div class="flex flex-1 flex-col items-center justify-center gap-3 rounded border border-dashed border-zinc-400 bg-zinc-100 text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
            <i class="bi bi-funnel text-4xl"></i>
            <p class="text-sm">Selecciona los filtros que necesites y presiona <strong>Buscar</strong> para ver los contratos.</p>
        </div>
    @else
        <div class="hidden flex-1 overflow-auto rounded border border-zinc-300 dark:border-zinc-700 md:block">
            <table class="min-w-full border-collapse text-sm">
                <thead class="sticky top-0 bg-zinc-300 dark:bg-zinc-800">
                <tr>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-left font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Empleado</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-left font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Empresa</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-left font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Tipo</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Inicio</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Fin</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Documento</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Situación</th>
                    <th class="border-b border-zinc-400 px-3 py-1.5 text-center font-semibold text-zinc-700 dark:border-zinc-600 dark:text-zinc-200">Opciones</th>
                </tr>
                </thead>

                <tbody class="bg-white dark:bg-zinc-900">
                @forelse ($contratos as $item)
                    @php
                        $dias = $item->diasParaVencer();
                        $filaClase = $item->vencido() ? 'bg-red-50 dark:bg-red-900/10' : ($item->porVencer() ? 'bg-yellow-50 dark:bg-yellow-900/10' : '');
                    @endphp
                    <tr wire:click="verDetalle({{ $item->id }})"
                        class="cursor-pointer border-b border-zinc-200 hover:bg-zinc-100 dark:border-zinc-800 dark:hover:bg-zinc-800 {{ $filaClase }}">
                        <td class="px-3 py-1.5 text-zinc-800 dark:text-zinc-100">
                            {{ $item->empleado->persona?->nombreCompleto() }}
                            <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $item->empleado->codigo_empleado }}</span>
                        </td>
                        <td class="px-3 py-1.5 text-zinc-800 dark:text-zinc-100">
                            @if ($item->empresa)
                                <span class="inline-block h-2.5 w-2.5 rounded-full" style="background: {{ $item->empresa->color_hex }}"></span>
                                {{ $item->empresa->nombre }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-1.5 text-zinc-800 dark:text-zinc-100">
                            <span class="mr-1 rounded bg-zinc-200 px-1.5 py-0.5 text-xs font-semibold text-zinc-700 dark:bg-zinc-700 dark:text-zinc-200">{{ $item->etiquetaNumero() }}</span>
                            {{ $item->tipoContrato->nombre ?? '—' }}
                            @if ($item->estado && $item->esUltimaPrestacion())
                                <span class="mt-1 block text-xs font-semibold text-orange-600 dark:text-orange-400">
                                    <i class="bi bi-exclamation-circle-fill"></i> Último permitido: al vencer debe pasar a indefinido
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-1.5 text-center text-zinc-800 dark:text-zinc-100">{{ $item->fecha_inicio->format('d/m/Y') }}</td>
                        <td class="px-3 py-1.5 text-center text-zinc-800 dark:text-zinc-100">
                            {{ $item->fecha_fin?->format('d/m/Y') ?? ($item->esIndefinido() ? 'Indefinido' : 'Sin fecha') }}
                            @if ($item->vencido())
                                <span class="block text-xs font-semibold text-red-600 dark:text-red-400">Vencido hace {{ abs($dias) }} día(s)</span>
                            @elseif ($item->porVencer())
                                <span class="block text-xs font-semibold text-yellow-700 dark:text-yellow-400">Vence en {{ $dias }} día(s)</span>
                            @elseif ($item->estado && $item->esPrestacion() && !$item->fecha_fin)
                                <span class="block text-xs font-semibold text-red-600 dark:text-red-400">Falta registrar la fecha fin</span>
                            @endif
                        </td>
                        <td class="px-3 py-1.5 text-center">
                            @if ($item->getFirstMediaUrl('documento'))
                                <a href="{{ $item->getFirstMediaUrl('documento') }}" target="_blank" class="text-zinc-500 hover:text-primary dark:text-zinc-400">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </a>
                            @else
                                <span class="text-zinc-400 dark:text-zinc-500">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-1.5 text-center">
                            @php $situacion = $item->situacion(); @endphp
                            <span class="rounded px-2 py-0.5 text-xs font-semibold {{ match ($situacion) {
                                'Vigente' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'Finalizado' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                default => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                            } }}">
                                {{ $situacion }}
                            </span>
                        </td>
                        <td class="px-3 py-1.5 text-center" onclick="event.stopPropagation()">
                            <div class="flex justify-center gap-3">
                                <button wire:click="openModal({{ $item->id }})" title="Editar"
                                        class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button wire:click="abrirDocumento({{ $item->id }})" title="Descargar contrato (PDF)"
                                        class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                    <i class="bi bi-file-earmark-arrow-down-fill"></i>
                                </button>
                                <button wire:click="verHistorial({{ $item->id_empleado }})" title="Historial de contratos"
                                        class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                @if ($item->estado && $item->esPrestacion())
                                    @unless ($item->esUltimaPrestacion())
                                        <button wire:click="renovar({{ $item->id }})" title="Renovar (2.º contrato por prestación)"
                                                class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    @endunless
                                    <button wire:click="pasarAIndefinido({{ $item->id }})" title="Pasar a contrato indefinido"
                                            class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                        <i class="bi bi-person-check-fill"></i>
                                    </button>
                                @endif
                                <button wire:click="delete({{ $item->id }})" title="{{ $item->estado ? 'Desactivar' : 'Activar' }}"
                                        class="{{ $item->estado ? 'text-zinc-500 hover:text-red-600 dark:text-zinc-400' : 'text-zinc-400 dark:text-zinc-600' }} cursor-pointer">
                                    @if($item->estado)
                                        <i class="bi bi-trash"></i>
                                    @else
                                        <i class="bi bi-x-circle"></i>
                                    @endif
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-3 text-center text-zinc-500 dark:text-zinc-400">No se encontraron contratos con esos filtros.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-2 md:hidden">
            @forelse ($contratos as $item)
                @php
                    $dias = $item->diasParaVencer();
                    $situacion = $item->situacion();
                @endphp
                <div x-data="{ open: false }"
                     class="rounded border border-zinc-300 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center gap-2">
                        <button type="button" x-on:click.stop="open = !open"
                            class="flex h-6 w-6 shrink-0 cursor-pointer items-center justify-center rounded-full border border-primary text-primary transition hover:bg-primary hover:text-white">
                            <i class="bi text-xs" :class="open ? 'bi-dash' : 'bi-plus'"></i>
                        </button>

                        <div wire:click="verDetalle({{ $item->id }})" class="min-w-0 flex-1 cursor-pointer">
                            <p class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $item->empleado->persona?->nombreCompleto() }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $item->empleado->codigo_empleado }} · {{ $item->empresa->nombre ?? '—' }}</p>
                        </div>

                        <div class="flex shrink-0 gap-3" onclick="event.stopPropagation()">
                            <button wire:click="openModal({{ $item->id }})" title="Editar"
                                    class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button wire:click="abrirDocumento({{ $item->id }})" title="Descargar contrato (PDF)"
                                    class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                <i class="bi bi-file-earmark-arrow-down-fill"></i>
                            </button>
                            <button wire:click="verHistorial({{ $item->id_empleado }})" title="Historial de contratos"
                                    class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                <i class="bi bi-clock-history"></i>
                            </button>
                            @if ($item->estado && $item->esPrestacion())
                                @unless ($item->esUltimaPrestacion())
                                    <button wire:click="renovar({{ $item->id }})" title="Renovar (2.º contrato por prestación)"
                                            class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                @endunless
                                <button wire:click="pasarAIndefinido({{ $item->id }})" title="Pasar a contrato indefinido"
                                        class="text-zinc-500 transition hover:text-primary dark:text-zinc-400 cursor-pointer">
                                    <i class="bi bi-person-check-fill"></i>
                                </button>
                            @endif
                            <button wire:click="delete({{ $item->id }})" title="{{ $item->estado ? 'Desactivar' : 'Activar' }}"
                                    class="{{ $item->estado ? 'text-zinc-500 hover:text-red-600 dark:text-zinc-400' : 'text-zinc-400 dark:text-zinc-600' }} cursor-pointer">
                                @if($item->estado)
                                    <i class="bi bi-trash"></i>
                                @else
                                    <i class="bi bi-x-circle"></i>
                                @endif
                            </button>
                        </div>
                    </div>

                    <div x-show="open" x-collapse x-cloak onclick="event.stopPropagation()"
                         class="mt-2 grid grid-cols-2 gap-2 border-t border-zinc-200 pt-2 pl-8 dark:border-zinc-800">
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Tipo</span>
                            <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $item->etiquetaNumero() }} {{ $item->tipoContrato->nombre ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Inicio</span>
                            <p class="text-sm text-zinc-800 dark:text-zinc-100">{{ $item->fecha_inicio->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Fin</span>
                            <p class="text-sm text-zinc-800 dark:text-zinc-100">
                                {{ $item->fecha_fin?->format('d/m/Y') ?? ($item->esIndefinido() ? 'Indefinido' : 'Sin fecha') }}
                                @if ($item->vencido())
                                    <span class="block text-xs font-semibold text-red-600 dark:text-red-400">Vencido hace {{ abs($dias) }} día(s)</span>
                                @elseif ($item->porVencer())
                                    <span class="block text-xs font-semibold text-yellow-700 dark:text-yellow-400">Vence en {{ $dias }} día(s)</span>
                                @elseif ($item->estado && $item->esPrestacion() && !$item->fecha_fin)
                                    <span class="block text-xs font-semibold text-red-600 dark:text-red-400">Falta registrar la fecha fin</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Documento</span>
                            <p class="text-sm text-zinc-800 dark:text-zinc-100">
                                @if ($item->getFirstMediaUrl('documento'))
                                    <a href="{{ $item->getFirstMediaUrl('documento') }}" target="_blank" class="text-zinc-500 hover:text-primary dark:text-zinc-400">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </a>
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Situación</span>
                            <p class="text-sm">
                                <span class="rounded px-2 py-0.5 text-xs font-semibold {{ match ($situacion) {
                                    'Vigente' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                    'Finalizado' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                    default => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                                } }}">
                                    {{ $situacion }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded border border-zinc-300 bg-white p-4 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                    No se encontraron contratos con esos filtros.
                </div>
            @endforelse
        </div>

        <div class="mt-3 dark:text-zinc-200">
            {{ $contratos->links() }}
        </div>
    @endif

    @if ($modalVisible)
        @include('livewire.contratos.modal.index')
    @endif

    @if ($historialVisible)
        @include('livewire.contratos.modal.historial')
    @endif

    @if ($documentoContrato && $vistaPreviaPdf)
        @include('livewire.contratos.modal.vista-previa')
    @elseif ($documentoContrato)
        @include('livewire.contratos.modal.documento')
    @endif

    @if ($viewingId && $contratoDetalle)
        @include('livewire.contratos.detalle')
    @endif

</div>
