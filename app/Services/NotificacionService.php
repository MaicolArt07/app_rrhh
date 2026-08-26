<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\Tipo;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificacionService
{
    public function crear(User $usuario, string $codigoTipo, string $titulo, string $mensaje, ?Model $referencia = null): Notificacion
    {
        $tipo = Tipo::categoria('tipo_notificacion')->where('codigo', $codigoTipo)->firstOrFail();

        return Notificacion::create([
            'id_usuario_destino' => $usuario->id,
            'id_tipo_notificacion' => $tipo->id,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'leido' => false,
            'referencia_tipo' => $referencia ? get_class($referencia) : null,
            'referencia_id' => $referencia?->getKey(),
        ]);
    }

    public function notificarUsuariosConPermiso(string $permiso, string $codigoTipo, string $titulo, string $mensaje, ?Model $referencia = null): void
    {
        $usuarios = User::whereHas('roles.permissions', fn ($q) => $q->where('name', $permiso))
            ->orWhereHas('permissions', fn ($q) => $q->where('name', $permiso))
            ->get()
            ->unique('id');

        foreach ($usuarios as $usuario) {
            $this->crear($usuario, $codigoTipo, $titulo, $mensaje, $referencia);
        }
    }
}
