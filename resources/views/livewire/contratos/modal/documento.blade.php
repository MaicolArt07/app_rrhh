@php
    $indefinido = $documentoContrato->esIndefinido();
    $campo = 'w-full rounded border border-gray-300 bg-white px-3 py-2 text-zinc-800 focus:border-blue-500 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100';
    $etiqueta = 'font-semibold text-zinc-700 dark:text-zinc-200';
@endphp
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-blur-sm">
    <div class="w-full rounded-lg border border-gray-200 bg-white p-6 shadow-lg sm:max-w-3xl dark:border-zinc-700 dark:bg-zinc-900 max-h-[92vh] overflow-y-auto">

        <h2 class="mb-1 text-center text-xl font-semibold text-zinc-800 dark:text-zinc-100">Completar datos del contrato</h2>
        <p class="mb-4 text-center text-sm text-zinc-500 dark:text-zinc-400">{{ $documentoContrato->empleado->persona->nombreCompleto() }} · CI {{ $documentoContrato->empleado->persona->numero_documento }}</p>

        <div class="mb-4 rounded border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200">
            <i class="bi bi-exclamation-triangle-fill"></i>
            Faltan algunos datos para generar el contrato. Completa solo lo que se pide abajo; el resto ya está registrado.
        </div>

        <div class="mb-4 rounded border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-900 dark:border-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-200">
            <i class="bi bi-file-earmark-text-fill"></i>
            Formato: <b>{{ $documentoFormato['titulo'] }} — {{ $documentoFormato['empresa'] }}</b>
            ({{ $documentoContrato->etiquetaNumero() }} {{ $documentoFormato['tipo'] }})
            <span class="block text-xs">
                Fecha del contrato: {{ $documentoContrato->fecha_inicio->format('d/m/Y') }}@if ($documentoContrato->fecha_fin) al {{ $documentoContrato->fecha_fin->format('d/m/Y') }}@endif.
                El texto del formato no se modifica; solo se completan estos datos.
            </span>
        </div>

        <h3 class="mb-2 text-sm font-semibold uppercase text-zinc-500 dark:text-zinc-400">Datos personales</h3>
        <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
            @if (in_array('ci_expedido', $camposEditablesFaltantes))
            <div class="space-y-1">
                <label class="{{ $etiqueta }}">CI expedido en <span class="text-red-600">*</span></label>
                <input type="text" wire:model="doc.ci_expedido" list="lugares-ci" placeholder="SC, LP, CB…" class="{{ $campo }}">
                <datalist id="lugares-ci">
                    @foreach (['SC', 'LP', 'CB', 'OR', 'PT', 'TJ', 'CH', 'BE', 'PD'] as $lugar)<option value="{{ $lugar }}">@endforeach
                </datalist>
                @if ($expedidoSugeridoDe)
                    <span class="block text-xs text-amber-700 dark:text-amber-400">
                        <i class="bi bi-exclamation-triangle-fill"></i> Sugerido según la Base de Colaboradores ({{ $expedidoSugeridoDe }}). Verifíquelo con el carnet.
                    </span>
                @endif
                @error('doc.ci_expedido') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
            @endif

            @if ($indefinido)
                @if (in_array('estado_civil', $camposEditablesFaltantes))
                <div class="space-y-1">
                    <label class="{{ $etiqueta }}">Estado civil <span class="text-red-600">*</span></label>
                    <input type="text" wire:model="doc.estado_civil" list="estados-civiles" placeholder="soltera, casado…" class="{{ $campo }}">
                    <datalist id="estados-civiles">
                        @foreach (['soltero', 'soltera', 'casado', 'casada', 'divorciado', 'divorciada', 'viudo', 'viuda', 'en unión libre'] as $estado)<option value="{{ $estado }}">@endforeach
                    </datalist>
                    @error('doc.estado_civil') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
                @endif
                @if (in_array('profesion', $camposEditablesFaltantes))
                <div class="space-y-1 sm:col-span-2">
                    <label class="{{ $etiqueta }}">Profesión <span class="text-red-600">*</span></label>
                    <input type="text" wire:model="doc.profesion" placeholder="Ej. Comercio Internacional" class="{{ $campo }}">
                    <span class="text-xs text-zinc-500">Se imprime como «profesional en …».</span>
                    @error('doc.profesion') <span class="block text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
                @endif
            @else
                @if (in_array('telefono', $camposEditablesFaltantes))
                <div class="space-y-1">
                    <label class="{{ $etiqueta }}">Celular <span class="text-red-600">*</span></label>
                    <input type="text" wire:model="doc.telefono" class="{{ $campo }}">
                    @error('doc.telefono') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
                @endif
                @if (in_array('email', $camposEditablesFaltantes))
                <div class="space-y-1">
                    <label class="{{ $etiqueta }}">Correo electrónico <span class="text-red-600">*</span></label>
                    <input type="email" wire:model="doc.email" class="{{ $campo }}">
                    @error('doc.email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
                @endif
            @endif

            @if (in_array('direccion', $camposEditablesFaltantes))
            <div class="space-y-1 {{ $indefinido ? 'sm:col-span-2' : '' }}">
                <label class="{{ $etiqueta }}">{{ $indefinido ? 'Residencia' : 'Dirección' }} <span class="text-red-600">*</span></label>
                <input type="text" wire:model="doc.direccion" placeholder="Ej. B/La Morita C/8 N°4" class="{{ $campo }}">
                @error('doc.direccion') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
            @endif
        </div>

        <h3 class="mb-2 text-sm font-semibold uppercase text-zinc-500 dark:text-zinc-400">Datos del contrato</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @if (in_array('cargo', $camposEditablesFaltantes))
            <div class="space-y-1">
                <label class="{{ $etiqueta }}">{{ $indefinido ? 'Cargo' : 'Servicio que presta' }} <span class="text-red-600">*</span></label>
                <input type="text" wire:model="doc.cargo" class="{{ $campo }}">
                @error('doc.cargo') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
            @endif
            @if (in_array('monto', $camposEditablesFaltantes))
            <div class="space-y-1">
                <label class="{{ $etiqueta }}">{{ $indefinido ? 'Salario mensual (Bs)' : 'Honorarios (Bs)' }} <span class="text-red-600">*</span></label>
                <input type="number" step="0.01" min="0" wire:model.live.debounce.500ms="doc.monto" class="{{ $campo }}">
                @error('doc.monto') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
            @endif

            @if ($documentoRetencion)
                <div class="rounded border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900 sm:col-span-2 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200">
                    <b>Retención si el prestador no emite factura</b> (13% RC-IVA + 3% IT, cláusula cuarta):
                    honorarios Bs {{ number_format($documentoRetencion['bruto'], 2, ',', '.') }}
                    − RC-IVA Bs {{ number_format($documentoRetencion['rc_iva'], 2, ',', '.') }}
                    − IT Bs {{ number_format($documentoRetencion['it'], 2, ',', '.') }}
                    = <b>líquido Bs {{ number_format($documentoRetencion['liquido'], 2, ',', '.') }}</b>.
                    En el contrato se imprime el monto de honorarios.
                </div>
            @endif

            @if ($indefinido)
                @if (in_array('dependencia', $camposEditablesFaltantes))
                <div class="space-y-1 sm:col-span-2">
                    <label class="{{ $etiqueta }}">Dependencia directa de <span class="text-red-600">*</span></label>
                    <input type="text" wire:model="doc.dependencia" list="dependencias" placeholder="Gerente General, CEO…" class="{{ $campo }}">
                    <datalist id="dependencias"><option value="Gerente General"><option value="CEO"></datalist>
                    <span class="text-xs text-zinc-500">Se imprime «con dependencia directa del …».</span>
                    @error('doc.dependencia') <span class="block text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
                @endif
                @if (in_array('funciones', $camposEditablesFaltantes))
                <div class="space-y-1 sm:col-span-2">
                    <label class="{{ $etiqueta }}">Principales funciones del puesto <span class="text-red-600">*</span></label>
                    <textarea wire:model="doc.funciones" rows="6" class="{{ $campo }}" placeholder="Una función por línea. Para un detalle de la función anterior, empiece la línea con «-»."></textarea>
                    @error('doc.funciones') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
                @endif
                @if (in_array('herederos', $camposEditablesFaltantes))
                <div class="space-y-1 sm:col-span-2">
                    <label class="{{ $etiqueta }}">Herederos <span class="text-red-600">*</span></label>
                    <textarea wire:model="doc.herederos" rows="2" class="{{ $campo }}" placeholder="Uno por línea. Ej.: Silvia Rosio Jimenez Siles - CI 3190067 (madre)"></textarea>
                    @error('doc.herederos') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>
                @endif
            @endif
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button wire:click="cerrarDocumento" class="rounded bg-red-600 px-4 py-2 text-white transition hover:bg-red-700 cursor-pointer">Cancelar</button>
            <button wire:click="descargarDocumento" wire:loading.attr="disabled" wire:target="descargarDocumento"
                    class="rounded bg-indigo-600 px-4 py-2 text-white transition hover:bg-indigo-700 disabled:opacity-60 cursor-pointer">
                <span wire:loading.remove wire:target="descargarDocumento"><i class="bi bi-eye-fill"></i> Guardar y ver contrato</span>
                <span wire:loading wire:target="descargarDocumento">Generando…</span>
            </button>
        </div>
    </div>
</div>
