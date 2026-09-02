<div class="p-6">

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <button wire:click="openModal"
            class="rounded bg-green-600 px-3 py-2 text-white transition hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 cursor-pointer">
            <i class="bi bi-plus-lg text-xl"></i>
        </button>

        <div class="flex flex-1 flex-wrap justify-end gap-3">
            <select wire:model.live="estadoFiltro"
                class="rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="pagado">Pagados</option>
                <option value="cancelado">Cancelados</option>
            </select>

            <input type="text" wire:model.live="search"
                class="w-full max-w-xs rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                placeholder="Buscar empleado...">
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg">
        <table class="min-w-full border border-gray-300 text-sm shadow-sm dark:border-zinc-700">
            <thead class="bg-gray-100 dark:bg-zinc-800">
            <tr>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">EMPLEADO</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">TIPO</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">MONTO TOTAL</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">CUOTA</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">SALDO PENDIENTE</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">ESTADO</th>
                <th class="border border-gray-300 px-3 py-2 text-center text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">OPCIONES</th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900">
            @forelse ($anticipos as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        {{ $item->empleado->persona?->nombreCompleto() }}
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $item->empleado->codigo_empleado }}</span>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">{{ $item->tipoAnticipo->nombre ?? '—' }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">Q {{ number_format($item->monto_total, 2) }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">Q {{ number_format($item->monto_cuota, 2) }} x {{ $item->numero_cuotas }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center font-semibold text-zinc-800 dark:border-zinc-700 dark:text-zinc-100">Q {{ number_format($item->saldo_pendiente, 2) }}</td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        @php
                            $badge = match($item->estado_anticipo) {
                                'pagado' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                                'cancelado' => 'bg-gray-200 text-gray-600 dark:bg-zinc-800 dark:text-zinc-400',
                                default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                            };
                        @endphp
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $badge }}">{{ ucfirst($item->estado_anticipo) }}</span>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-center dark:border-zinc-700">
                        @if ($item->estado_anticipo === 'activo')
                            <div class="flex justify-center gap-3">
                                <button wire:click="abrirPago({{ $item->id }})"
                                        class="text-green-600 transition hover:text-green-700 dark:text-green-400 cursor-pointer" title="Registrar pago">
                                    <i class="bi bi-cash-coin text-xl"></i>
                                </button>
                                <button wire:click="cancelar({{ $item->id }})"
                                        class="text-red-600 transition hover:text-red-700 dark:text-red-400 cursor-pointer" title="Cancelar">
                                    <i class="bi bi-x-circle-fill text-xl"></i>
                                </button>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-3 text-center text-gray-500 dark:text-zinc-400">No hay anticipos registrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 dark:text-zinc-200">
        {{ $anticipos->links() }}
    </div>

    @if ($modalVisible)
        @include('livewire.anticipos.modal.index')
    @endif

    @if ($pagoVisible)
        @include('livewire.anticipos.modal.pago')
    @endif

</div>
