<?php

namespace App\Console\Commands;

use App\Models\Contrato;
use App\Models\DocumentoEmpleado;
use App\Services\NotificacionService;
use Illuminate\Console\Command;

class RevisarVencimientos extends Command
{
    protected $signature = 'rrhh:revisar-vencimientos {--dias=30 : Días de anticipación para notificar}';

    protected $description = 'Revisa contratos y documentos de empleado próximos a vencer y genera notificaciones';

    public function handle(NotificacionService $notificaciones): int
    {
        $dias = (int) $this->option('dias');
        $limite = now()->addDays($dias);

        $contratos = Contrato::with(['empleado.persona', 'tipoContrato'])
            ->where('estado', true)
            ->where('notificado_vencimiento', false)
            ->whereNotNull('fecha_fin')
            ->whereBetween('fecha_fin', [now(), $limite])
            ->get();

        foreach ($contratos as $contrato) {
            $nombre = $contrato->empleado->persona?->nombreCompleto() ?? $contrato->empleado->codigo_empleado;
            $vence = $contrato->fecha_fin->format('d/m/Y');

            if ($contrato->esUltimaPrestacion()) {
                $titulo = 'Vence el segundo contrato por prestación';
                $mensaje = "El {$contrato->etiquetaNumero()} contrato por prestación de servicios de {$nombre} vence el {$vence}. "
                    .'No puede renovarse otra vez como prestación (Ministerio de Trabajo): debe pasar a contrato indefinido.';
            } else {
                $titulo = 'Contrato por vencer';
                $mensaje = "El contrato de {$nombre} vence el {$vence}.";
            }

            $notificaciones->notificarUsuariosConPermiso('Ver Contratos', 'contrato_por_vencer', $titulo, $mensaje, $contrato);
            $contrato->update(['notificado_vencimiento' => true]);
        }

        $documentos = DocumentoEmpleado::with('empleado.persona')
            ->where('estado', true)
            ->where('notificado_vencimiento', false)
            ->whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [now(), $limite])
            ->get();

        foreach ($documentos as $documento) {
            $nombre = $documento->empleado->persona?->nombreCompleto() ?? $documento->empleado->codigo_empleado;
            $notificaciones->notificarUsuariosConPermiso(
                'Ver Documentos',
                'documento_por_vencer',
                'Documento por vencer',
                "El documento de {$nombre} vence el {$documento->fecha_vencimiento->format('d/m/Y')}.",
                $documento
            );
            $documento->update(['notificado_vencimiento' => true]);
        }

        $this->info("Revisión completa: {$contratos->count()} contratos y {$documentos->count()} documentos notificados.");

        return self::SUCCESS;
    }
}
