<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectToFirstPermission
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->can('Ver Inicio')) {
            return $next($request);
        }

        // Lista de permisos y sus rutas correspondientes
        $permissionsRoutes = [
            'Ver Inicio' => 'home',
            'Ver Personas' => 'personas',
            'Ver Empleados' => 'empleados',
            'Ver Cargos' => 'cargos',
            'Ver Departamentos' => 'departamentos',
            'Ver Contratos' => 'contratos',
            'Ver Documentos' => 'documentos',
            'Ver Asistencia' => 'asistencia',
            'Ver Vacaciones' => 'vacaciones',
            'Ver Horas Extra' => 'horas-extra',
            'Ver Planillas' => 'planillas',
            'Ver Liquidaciones' => 'liquidaciones',
            'Ver Evaluaciones' => 'evaluaciones',
            'Ver Reglamentos' => 'reglamentos',
            'Ver Notificaciones' => 'notificaciones',
            'Ver Anticipos' => 'anticipos',
            'Ver Reportes' => 'reportes',
            'Ver Tipos' => 'tipos',
            'Ver Gestiones' => 'gestiones',
            'Ver Usuarios' => 'users',
            'Ver Roles' => 'roles',
            'Ver Permisos' => 'permissions',
        ];

        foreach ($permissionsRoutes as $permission => $route) {
            if ($user->can($permission)) {
                return redirect()->route($route);
            }
        }

        // Si no tiene permisos
        Auth::logout();
        return redirect()->route('login')->with('error', 'No tienes permisos asignados.');
    }
}
