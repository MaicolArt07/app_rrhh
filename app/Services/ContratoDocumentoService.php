<?php

namespace App\Services;

use App\Models\Contrato;
use App\Support\NumeroALetras;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Genera el contrato en PDF con el formato de la carpeta contratos/ que
 * corresponde a la empresa y al tipo de contrato. Solo se completan los datos
 * del trabajador; el texto de cada formato no se modifica.
 */
class ContratoDocumentoService
{
    /** Retención sobre honorarios de prestación de servicios sin factura: 13% RC-IVA + 3% IT. */
    public const RETENCION_RC_IVA = 0.13;
    public const RETENCION_IT = 0.03;

    /** [código de tipo de contrato][código de empresa] => formato */
    private const FORMATOS = [
        'prestacion' => [
            'DC' => ['vista' => 'prestacion-dc', 'papel' => 'a4', 'titulo' => 'Contrato de Prestación de Servicios'],
            'DX' => ['vista' => 'prestacion-dx', 'papel' => 'a4', 'titulo' => 'Contrato Prestación de Servicios'],
        ],
        'indefinido' => [
            'DC' => ['vista' => 'indefinido-dc', 'papel' => 'letter', 'titulo' => 'Contrato Indefinido'],
            'DX' => ['vista' => 'indefinido-dx', 'papel' => 'letter', 'titulo' => 'Contrato Indefinido'],
        ],
    ];

    /** Campos que pide cada tipo de contrato: clave => etiqueta. */
    private const CAMPOS = [
        'comunes' => [
            'nombre' => 'Nombre completo',
            'ci' => 'Número de CI',
            'expedido' => 'Lugar de expedición del CI',
            'cargo' => 'Cargo / servicio',
            'monto' => 'Monto',
            'inicio' => 'Fecha de inicio del contrato',
        ],
        'prestacion' => [
            'fin' => 'Fecha fin del contrato',
            'celular' => 'Celular',
            'email' => 'Correo electrónico',
            'domicilio' => 'Dirección',
        ],
        'indefinido' => [
            'estado_civil' => 'Estado civil',
            'profesion' => 'Profesión',
            'domicilio' => 'Residencia',
            'dependencia' => 'Dependencia directa (jefe)',
            'funciones' => 'Funciones del puesto',
            'herederos' => 'Herederos',
        ],
    ];

    /**
     * Formato que se usará para el contrato, o null si no hay uno para su empresa y tipo.
     *
     * @return array{vista: string, papel: string, titulo: string, empresa: string, tipo: string}|null
     */
    public function formato(Contrato $contrato): ?array
    {
        $contrato->loadMissing(['empresa', 'empleado.empresa', 'tipoContrato']);
        $tipo = $contrato->tipoContrato?->codigo;
        $empresa = $contrato->empresa ?? $contrato->empleado?->empresa;

        $formato = self::FORMATOS[$tipo][$empresa?->codigo] ?? null;

        return $formato ? $formato + ['empresa' => $empresa->nombre, 'tipo' => $contrato->tipoContrato->nombre] : null;
    }

    /**
     * Etiquetas de los datos que faltan para poder generar el contrato.
     *
     * @return list<string>
     */
    public function faltantes(Contrato $contrato): array
    {
        return array_values($this->camposFaltantes($contrato));
    }

    /**
     * Igual que faltantes(), pero conserva la clave interna de cada campo
     * (nombre, ci, expedido, cargo, monto, inicio, fin, celular, email,
     * domicilio, estado_civil, profesion, dependencia, funciones, herederos).
     *
     * @return array<string, string> clave => etiqueta
     */
    public function camposFaltantes(Contrato $contrato): array
    {
        $tipo = $contrato->tipoContrato?->codigo;
        $valores = $this->valoresBase($contrato);
        $campos = self::CAMPOS['comunes'] + (self::CAMPOS[$tipo] ?? []);

        return array_filter(
            $campos,
            fn ($etiqueta, $clave) => blank($valores[$clave] ?? null),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Datos que se imprimen en el contrato.
     *
     * @return array{t: array<string, string>, c: array<string, mixed>}
     */
    public function datos(Contrato $contrato): array
    {
        $v = $this->valoresBase($contrato);
        $monto = (float) $v['monto'];
        $literal = NumeroALetras::entero((int) floor($monto));

        return [
            't' => [
                'nombre' => $v['nombre'],
                'nombre_mayus' => mb_strtoupper($v['nombre']),
                'ci' => $v['ci'],
                'expedido' => $v['expedido'],
                'ci_completo' => trim($v['ci'].' '.$v['expedido']),
                'estado_civil' => (string) $v['estado_civil'],
                'profesion' => (string) $v['profesion'],
                'domicilio' => (string) $v['domicilio'],
                'celular' => (string) $v['celular'],
                'email' => (string) $v['email'],
            ],
            'c' => [
                'cargo' => $v['cargo'],
                'monto' => number_format($monto, 2, ',', '.'),
                'centavos' => NumeroALetras::centavos($monto),
                'literal_mayus' => mb_strtoupper($literal),
                'literal_titulo' => Str::title($literal),
                'inicio' => $this->fecha($v['inicio'], 'del'),
                'inicio_de' => $this->fecha($v['inicio'], 'de'),
                'fin' => $this->fecha($v['fin'], 'del'),
                'fin_de' => $this->fecha($v['fin'], 'de'),
                'dependencia' => (string) $v['dependencia'],
                'funciones' => $this->funciones((string) $v['funciones']),
                'herederos' => $this->lineas((string) $v['herederos']),
            ],
        ];
    }

    /**
     * Sugiere el lugar de expedición del CI a partir del departamento del
     * colaborador ("Santa Cruz - Andrés Ibáñez" → "SC"). Es solo una sugerencia:
     * se confirma con el carnet antes de guardarla.
     */
    public static function sugerirExpedido(?string $procedencia): ?string
    {
        $departamento = Str::lower(Str::ascii(trim(explode('-', (string) $procedencia)[0])));

        return [
            'santa cruz' => 'SC', 'la paz' => 'LP', 'cochabamba' => 'CB', 'oruro' => 'OR', 'potosi' => 'PT',
            'tarija' => 'TJ', 'chuquisaca' => 'CH', 'beni' => 'BE', 'pando' => 'PD',
        ][$departamento] ?? null;
    }

    /**
     * Honorarios con la retención de impuestos (13% RC-IVA + 3% IT) que el
     * contratante aplica cuando el prestador de servicios no emite factura.
     *
     * @return array{bruto: float, rc_iva: float, it: float, liquido: float}
     */
    public function retencion(float $monto): array
    {
        $rcIva = round($monto * self::RETENCION_RC_IVA, 2);
        $it = round($monto * self::RETENCION_IT, 2);

        return ['bruto' => round($monto, 2), 'rc_iva' => $rcIva, 'it' => $it, 'liquido' => round($monto - $rcIva - $it, 2)];
    }

    /**
     * @return array{0: string, 1: string} nombre de archivo y contenido PDF
     */
    public function generar(Contrato $contrato): array
    {
        $formato = $this->formato($contrato);

        if (! $formato) {
            throw new \RuntimeException('No hay un formato de contrato para esta empresa y tipo de contrato.');
        }

        if ($faltan = $this->faltantes($contrato)) {
            throw new \RuntimeException('Faltan datos para el contrato: '.implode(', ', $faltan).'.');
        }

        $pdf = Pdf::loadView('contratos.documentos.'.$formato['vista'], $this->datos($contrato))
            ->setPaper($formato['papel'])
            ->setOption(['isFontSubsettingEnabled' => true, 'isPhpEnabled' => false]);

        return [$this->nombreArchivo($contrato, $formato), $pdf->output()];
    }

    /** "Junio´26 - Contrato Indefinido - Marcela Bravo Jiménez - DX.pdf", como en la carpeta contratos/. */
    public function nombreArchivo(Contrato $contrato, array $formato): string
    {
        $inicio = Carbon::parse($contrato->fecha_inicio);
        $empresa = $contrato->empresa ?? $contrato->empleado->empresa;

        return sprintf(
            '%s´%s - %s - %s - %s.pdf',
            ucfirst($inicio->locale('es')->translatedFormat('F')),
            $inicio->format('y'),
            $formato['titulo'],
            $contrato->empleado->persona->nombreCompleto(),
            $empresa->codigo
        );
    }

    /**
     * Valores de cada campo: los del contrato si existen; si no, los del empleado.
     *
     * @return array<string, mixed>
     */
    private function valoresBase(Contrato $contrato): array
    {
        $contrato->loadMissing(['empleado.persona', 'empleado.cargo', 'tipoContrato']);
        $empleado = $contrato->empleado;
        $persona = $empleado->persona;

        return [
            'nombre' => $persona->nombreCompleto(),
            'ci' => $persona->numero_documento,
            'expedido' => $persona->ci_expedido,
            'estado_civil' => $persona->estado_civil,
            'profesion' => $persona->profesion,
            'domicilio' => $persona->direccion,
            'celular' => $persona->telefono,
            'email' => $persona->email,
            'cargo' => $contrato->cargo ?: $empleado->cargo?->nombre,
            'monto' => $contrato->monto !== null ? (float) $contrato->monto : ((float) $empleado->sueldo_actual ?: null),
            'inicio' => $contrato->fecha_inicio,
            'fin' => $contrato->fecha_fin,
            'dependencia' => $contrato->dependencia,
            'funciones' => $contrato->funciones,
            'herederos' => $contrato->herederos,
        ];
    }

    /** "01 de junio del 2026" o "01 de junio de 2026", como aparece en cada formato. */
    private function fecha($fecha, string $conector): string
    {
        if (! $fecha) {
            return '';
        }

        $f = Carbon::parse($fecha)->locale('es');

        return $f->format('d').' de '.$f->translatedFormat('F').' '.$conector.' '.$f->format('Y');
    }

    /**
     * Funciones del puesto: una por línea; las líneas que empiezan con "-" o "○"
     * son detalle de la función anterior.
     *
     * @return list<array{texto: string, sub: list<string>}>
     */
    private function funciones(string $texto): array
    {
        $resultado = [];

        foreach ($this->lineas($texto, false) as $linea) {
            if (preg_match('/^\s*[-○◦]\s*(.+)$/u', $linea, $m) && $resultado) {
                $resultado[array_key_last($resultado)]['sub'][] = trim($m[1]);
            } else {
                $resultado[] = ['texto' => trim(preg_replace('/^\s*[•●*]\s*/u', '', $linea)), 'sub' => []];
            }
        }

        return $resultado;
    }

    /**
     * @return list<string>
     */
    private function lineas(string $texto, bool $quitarGuion = true): array
    {
        $lineas = array_filter(array_map('rtrim', preg_split('/\R/u', $texto)), fn ($l) => trim($l) !== '');

        return array_values(array_map(
            fn ($l) => $quitarGuion ? trim(preg_replace('/^\s*[-•●]\s*/u', '', $l)) : $l,
            $lineas
        ));
    }
}
