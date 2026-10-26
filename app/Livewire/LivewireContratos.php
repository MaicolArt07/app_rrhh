<?php

namespace App\Livewire;

use App\Livewire\Form\ContratoForm;
use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Tipo;
use App\Services\ContratoDocumentoService;
use App\Services\ContratoService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class LivewireContratos extends Component
{
    use WithPagination;
    use WithFileUploads;

    public ContratoForm $form;

    public $search = '';
    public $tipoFiltro = '';
    public $empresaFiltro = '';
    public $situacionFiltro = 'vigentes';
    public $soloAlertas = false;
    public $modalVisible = false;
    public bool $buscado = false;
    public $documento;
    public ?int $viewingId = null;

    /** nuevo | editar | renovar | indefinido */
    public $modo = 'nuevo';

    public $historialVisible = false;
    public $historialEmpleadoId = null;

    /** Descarga del contrato en PDF: datos que se imprimen y que el usuario puede completar. */
    public $documentoContratoId = null;
    public $doc = [];

    /** Claves (del formulario $doc) que faltan y por eso se muestran como editables. */
    public array $camposEditablesFaltantes = [];

    /** Vista previa del PDF ya generado, en base64, lista para incrustar en un <iframe>. */
    public ?string $vistaPreviaPdf = null;

    /** clave del servicio (ContratoDocumentoService::CAMPOS) => clave del formulario $doc */
    private const MAPA_CAMPOS_DOC = [
        'expedido' => 'ci_expedido',
        'estado_civil' => 'estado_civil',
        'profesion' => 'profesion',
        'domicilio' => 'direccion',
        'celular' => 'telefono',
        'email' => 'email',
        'cargo' => 'cargo',
        'monto' => 'monto',
        'dependencia' => 'dependencia',
        'funciones' => 'funciones',
        'herederos' => 'herederos',
    ];

    /** Departamento de la Base de Colaboradores del que se sugirió el CI expedido (null si no hubo sugerencia). */
    public $expedidoSugeridoDe = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
    }

    public function updating($propiedad)
    {
        if (in_array($propiedad, ['search', 'tipoFiltro', 'empresaFiltro', 'situacionFiltro', 'soloAlertas'])) {
            $this->resetPage();
        }
    }

    /** Al elegir el empleado de un contrato nuevo se propone su empresa. */
    public function updatedFormIdEmpleado($valor)
    {
        if (!$this->form->id && $valor) {
            $this->form->id_empresa = Empleado::whereKey($valor)->value('id_empresa') ?? '';
        }
    }

    public function buscar()
    {
        $this->resetPage();
        $this->buscado = true;
    }

    public function limpiarFiltros()
    {
        $this->reset('search', 'tipoFiltro', 'empresaFiltro', 'situacionFiltro', 'soloAlertas');
        $this->buscado = false;
    }

    public function verDetalle($id)
    {
        $this->viewingId = $id;
    }

    public function cerrarDetalle()
    {
        $this->viewingId = null;
    }

    public function editarDesdeDetalle($id)
    {
        $this->viewingId = null;
        $this->openModal($id);
    }

    public function render()
    {
        $contratos = null;
        $alertas = $this->servicio()->alertas();

        if ($this->buscado) {
            $query = Contrato::query()->with(['empleado.persona', 'empresa', 'tipoContrato', 'contratoSiguiente']);

            if (!empty($this->search)) {
                $query->whereHas('empleado', function ($q) {
                    $q->where('codigo_empleado', 'like', "%{$this->search}%")
                      ->orWhereHas('persona', function ($p) {
                          $p->where('primer_nombre', 'like', "%{$this->search}%")
                            ->orWhere('primer_apellido', 'like', "%{$this->search}%");
                      });
                });
            }

            if (!empty($this->empresaFiltro)) {
                $query->where('id_empresa', $this->empresaFiltro);
            }

            if (!empty($this->tipoFiltro)) {
                $query->where('id_tipo_contrato', $this->tipoFiltro);
            }

            match ($this->situacionFiltro) {
                'vigentes' => $query->where('estado', true),
                'historial' => $query->where('estado', false),
                default => null,
            };

            if ($this->soloAlertas) {
                $query->whereIn('id', $alertas->pluck('id'));
            }

            $contratos = $query->orderByDesc('fecha_inicio')->paginate(10);
        }

        return view('livewire.contratos.index', [
            'contratos' => $contratos,
            'contratoDetalle' => $this->viewingId
                ? Contrato::with(['empleado.persona.tipoDocumento', 'empleado.cargo', 'empresa', 'tipoContrato', 'contratoAnterior', 'contratoSiguiente'])->find($this->viewingId)
                : null,
            'empleados' => Empleado::with('persona')->where('estado', true)->get(),
            'tiposContrato' => Tipo::categoria('tipo_contrato')->activos()->orderBy('orden')->get(),
            'empresas' => Empresa::where('estado', true)->orderBy('nombre')->get(),
            'resumenAlertas' => [
                'por_vencer' => $alertas->filter(fn ($c) => $c->porVencer())->count(),
                'vencidos' => $alertas->filter(fn ($c) => $c->vencido())->count(),
                'segundo' => $alertas->filter(fn ($c) => $c->esUltimaPrestacion())->count(),
            ],
            'vigenteDelEmpleado' => $this->modalVisible && !$this->form->id && $this->form->id_empleado
                ? $this->servicio()->vigente((int) $this->form->id_empleado)
                : null,
            'historial' => $this->historialVisible
                ? Contrato::with(['empresa', 'tipoContrato', 'contratoSiguiente'])->where('id_empleado', $this->historialEmpleadoId)->orderBy('fecha_inicio')->get()
                : collect(),
            'empleadoHistorial' => $this->historialVisible ? Empleado::with('persona')->find($this->historialEmpleadoId) : null,
            'documentoContrato' => $documentoContrato = $this->documentoContratoId
                ? Contrato::with(['empleado.persona', 'empresa', 'tipoContrato'])->find($this->documentoContratoId)
                : null,
            'documentoFormato' => $documentoContrato ? $this->documentos()->formato($documentoContrato) : null,
            'documentoRetencion' => $documentoContrato && $documentoContrato->esPrestacion() && is_numeric($this->doc['monto'] ?? null)
                ? $this->documentos()->retencion((float) $this->doc['monto'])
                : null,
        ]);
    }

    private function documentos(): ContratoDocumentoService
    {
        return app(ContratoDocumentoService::class);
    }

    /**
     * Abre el flujo de descarga: si ya están todos los datos, pasa directo a la
     * vista previa del PDF; si falta algo, muestra el formulario pero solo con
     * los campos que realmente faltan.
     */
    public function abrirDocumento($id)
    {
        $contrato = Contrato::with(['empleado.persona', 'empleado.cargo', 'tipoContrato', 'empresa'])->findOrFail($id);

        if (!$this->documentos()->formato($contrato)) {
            $this->dispatch('toast', type: 'error', message: 'No hay un formato de contrato para '.($contrato->empresa?->nombre ?? 'esta empresa').' con contrato '.($contrato->tipoContrato?->nombre ?? '').'.');

            return;
        }

        $faltantes = $this->documentos()->camposFaltantes($contrato);

        // Datos que no se completan en este formulario rápido (van por «Editar»).
        $noEditablesAqui = array_intersect_key($faltantes, array_flip(['nombre', 'ci', 'inicio', 'fin']));
        if ($noEditablesAqui) {
            $this->dispatch('toast', type: 'error', message: 'Faltan datos que solo se completan desde «Editar»: '.implode(', ', $noEditablesAqui).'.');

            return;
        }

        $persona = $contrato->empleado->persona;
        $sugerido = $persona->ci_expedido ? null : ContratoDocumentoService::sugerirExpedido($persona->procedencia);
        $this->expedidoSugeridoDe = $sugerido ? $persona->procedencia : null;
        $this->resetValidation();
        $this->documentoContratoId = $contrato->id;
        $this->vistaPreviaPdf = null;
        $this->doc = [
            'ci_expedido' => (string) ($persona->ci_expedido ?: $sugerido),
            'estado_civil' => (string) $persona->estado_civil,
            'profesion' => (string) $persona->profesion,
            'direccion' => (string) $persona->direccion,
            'telefono' => (string) $persona->telefono,
            'email' => (string) $persona->email,
            'cargo' => $contrato->cargo ?: (string) $contrato->empleado->cargo?->nombre,
            'monto' => $contrato->monto !== null ? (string) $contrato->monto : ((float) $contrato->empleado->sueldo_actual ? (string) $contrato->empleado->sueldo_actual : ''),
            'dependencia' => (string) $contrato->dependencia,
            'funciones' => (string) $contrato->funciones,
            'herederos' => (string) $contrato->herederos,
        ];

        // Claves del $doc que corresponden a campos todavía vacíos: solo esas se muestran como editables.
        $this->camposEditablesFaltantes = collect($faltantes)
            ->keys()
            ->map(fn ($clave) => self::MAPA_CAMPOS_DOC[$clave] ?? null)
            ->filter()
            ->values()
            ->all();

        if (empty($this->camposEditablesFaltantes)) {
            // Ya está todo completo: directo a la vista previa, sin pedir nada.
            $this->generarVistaPrevia($contrato);
        }
    }

    public function cerrarDocumento()
    {
        $this->documentoContratoId = null;
        $this->doc = [];
        $this->camposEditablesFaltantes = [];
        $this->vistaPreviaPdf = null;
        $this->expedidoSugeridoDe = null;
        $this->resetValidation();
    }

    /** Guarda los datos completados (solo los que faltaban) y pasa a la vista previa. */
    public function descargarDocumento()
    {
        $contrato = Contrato::with(['empleado.persona', 'tipoContrato'])->findOrFail($this->documentoContratoId);
        $indefinido = $contrato->esIndefinido();
        $requerido = fn (bool $si) => $si ? 'required' : 'nullable';

        $this->validate([
            'doc.ci_expedido' => 'required|string|max:10',
            'doc.cargo' => 'required|string|max:150',
            'doc.monto' => 'required|numeric|min:1',
            'doc.direccion' => 'required|string|max:255',
            'doc.estado_civil' => $requerido($indefinido).'|string|max:30',
            'doc.profesion' => $requerido($indefinido).'|string|max:150',
            'doc.dependencia' => $requerido($indefinido).'|string|max:150',
            'doc.funciones' => $requerido($indefinido).'|string|max:5000',
            'doc.herederos' => $requerido($indefinido).'|string|max:1000',
            'doc.telefono' => $requerido(!$indefinido).'|string|max:50',
            'doc.email' => $requerido(!$indefinido).'|nullable|email|max:150',
        ], [
            'required' => 'Este dato es obligatorio para el contrato.',
            'doc.monto.numeric' => 'El monto debe ser un número.',
            'doc.email.email' => 'El correo no es válido.',
        ]);

        if ($contrato->esPrestacion() && !$contrato->fecha_fin) {
            $this->addError('doc.cargo', 'El contrato por prestación de servicios no tiene fecha fin: regístrela en «Editar» antes de descargar.');

            return;
        }

        $contrato->empleado->persona->update([
            'ci_expedido' => mb_strtoupper(trim($this->doc['ci_expedido'])),
            'estado_civil' => trim($this->doc['estado_civil']) ?: null,
            'profesion' => trim($this->doc['profesion']) ?: null,
            'direccion' => trim($this->doc['direccion']),
            'telefono' => trim($this->doc['telefono']) ?: null,
            'email' => trim($this->doc['email']) ?: null,
        ]);

        $contrato->update([
            'cargo' => trim($this->doc['cargo']),
            'monto' => round((float) $this->doc['monto'], 2),
            'dependencia' => trim($this->doc['dependencia']) ?: null,
            'funciones' => trim($this->doc['funciones']) ?: null,
            'herederos' => trim($this->doc['herederos']) ?: null,
        ]);

        $this->generarVistaPrevia($contrato->fresh());
    }

    /** Genera el PDF y lo deja listo para incrustarse como vista previa (no se descarga todavía). */
    private function generarVistaPrevia(Contrato $contrato): void
    {
        try {
            [, $pdf] = $this->documentos()->generar($contrato);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', type: 'error', message: $e->getMessage());

            return;
        }

        $this->vistaPreviaPdf = base64_encode($pdf);
    }

    /** El usuario ya vio la vista previa y confirma: ahora sí se descarga el archivo. */
    public function descargarDefinitivo()
    {
        $contrato = Contrato::with(['empleado.persona', 'tipoContrato'])->findOrFail($this->documentoContratoId);

        try {
            [$nombre, $pdf] = $this->documentos()->generar($contrato);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', type: 'error', message: $e->getMessage());

            return;
        }

        $this->cerrarDocumento();
        $this->dispatch('toast', type: 'success', message: 'Contrato descargado.');

        return response()->streamDownload(fn () => print($pdf), $nombre, ['Content-Type' => 'application/pdf']);
    }

    public function openModal($id = null)
    {
        $this->documento = null;
        $this->resetValidation();

        if ($id) {
            $this->form->fillForm(Contrato::findOrFail($id));
            $this->modo = 'editar';
        } else {
            $this->form->resetForm();
            $this->modo = 'nuevo';
        }

        $this->modalVisible = true;
    }

    /** Prepara un nuevo contrato por prestación a continuación del actual. */
    public function renovar($id)
    {
        $contrato = Contrato::with('tipoContrato')->findOrFail($id);

        if (!$contrato->esPrestacion() || !$this->servicio()->puedeRenovarPrestacion($contrato->id_empleado)) {
            $this->dispatch('toast', type: 'error', message: 'Ya tiene '.Contrato::MAX_PRESTACION.' contratos por prestación: solo puede pasar a indefinido.');

            return;
        }

        $this->prepararContinuacion($contrato, 'prestacion', 'renovar');
    }

    /** Prepara el paso a contrato indefinido conservando el historial. */
    public function pasarAIndefinido($id)
    {
        $this->prepararContinuacion(Contrato::findOrFail($id), 'indefinido', 'indefinido');
    }

    private function prepararContinuacion(Contrato $contrato, string $codigoTipo, string $modo)
    {
        $this->documento = null;
        $this->resetValidation();
        $this->form->resetForm();

        $this->form->id_empleado = $contrato->id_empleado;
        $this->form->id_empresa = $contrato->id_empresa ?? $contrato->empleado?->id_empresa;
        $this->form->id_tipo_contrato = Tipo::categoria('tipo_contrato')->where('codigo', $codigoTipo)->value('id');
        $this->form->fecha_inicio = ($contrato->fecha_fin?->copy()->addDay() ?? now())->format('Y-m-d');
        $this->modo = $modo;
        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->reglas(), $this->form->messages() + [
            'form.fecha_fin.required_if' => 'El contrato por prestación de servicios debe tener fecha fin.',
        ]);
        $this->validate(['documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'], [], ['documento' => 'documento']);

        try {
            if ($this->form->id) {
                $contrato = $this->servicio()->actualizar(Contrato::findOrFail($this->form->id), $this->form->toArray());
                $message = 'Contrato actualizado correctamente.';
            } else {
                $contrato = $this->servicio()->registrar($this->form->toArray());
                $message = $contrato->id_contrato_anterior
                    ? 'Contrato registrado. El contrato anterior quedó en el historial.'
                    : 'Contrato registrado correctamente.';

                if ($contrato->esUltimaPrestacion()) {
                    $this->dispatch('toast', type: 'warning', message: 'Es su '.$contrato->etiquetaNumero().' contrato por prestación: no podrá renovarse otra vez como prestación.');
                }
            }

            if ($this->documento) {
                $contrato->addMedia($this->documento->getRealPath())
                    ->usingFileName($this->documento->getClientOriginalName())
                    ->toMediaCollection('documento');
            }

            $this->dispatch('toast', type: 'success', message: $message);
            $this->modalVisible = false;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo guardar el contrato. Inténtalo nuevamente.');
        }
    }

    private function reglas(): array
    {
        $idPrestacion = Tipo::categoria('tipo_contrato')->where('codigo', 'prestacion')->value('id');

        return array_merge($this->form->rules(), [
            'form.fecha_fin' => ['nullable', 'date', 'after:form.fecha_inicio', 'required_if:form.id_tipo_contrato,'.$idPrestacion],
        ]);
    }

    private function servicio(): ContratoService
    {
        return app(ContratoService::class);
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->form->resetForm();
        $this->documento = null;
        $this->modalVisible = false;
    }

    public function verHistorial($idEmpleado)
    {
        $this->historialEmpleadoId = $idEmpleado;
        $this->historialVisible = true;
    }

    public function cerrarHistorial()
    {
        $this->historialVisible = false;
        $this->historialEmpleadoId = null;
    }

    public function delete($id)
    {
        try {
            $contrato = Contrato::findOrFail($id);

            if (!$contrato->estado && $this->servicio()->vigente($contrato->id_empleado, $contrato->id)) {
                $this->dispatch('toast', type: 'error', message: 'El empleado ya tiene otro contrato vigente.');

                return;
            }

            $contrato->estado = !$contrato->estado;
            $contrato->save();

            $message = $contrato->estado
                ? 'El contrato fue activado correctamente.'
                : 'El contrato fue desactivado correctamente.';

            $this->dispatch('toast', type: $contrato->estado ? 'success' : 'warning', message: $message);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo actualizar el estado del contrato.');
        }
    }
}
