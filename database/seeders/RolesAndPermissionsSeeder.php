<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos "Ver X" -> controlan el acceso a cada módulo/ruta.
        $verModulos = [
            'Ver Inicio',
            'Ver Personas',
            'Ver Empleados',
            'Ver Cargos',
            'Ver Áreas',
            'Ver Empresas',
            'Ver Contratos',
            'Ver Documentos',
            'Ver Asistencia',
            'Ver Vacaciones',
            'Ver Horas Extra',
            'Ver Planillas',
            'Ver Liquidaciones',
            'Ver Evaluaciones',
            'Ver Reglamentos',
            'Ver Notificaciones',
            'Ver Anticipos',
            'Ver Reportes',
            'Ver Tipos',
            'Ver Gestiones',
            'Ver Usuarios',
            'Ver Roles',
            'Ver Permisos',
            'Ver Configuracion',
        ];

        // Permisos de acciones específicas dentro de un módulo.
        $acciones = [
            'Marcar Asistencia',
            'Importar Asistencia',
            'Editar Matriz Asistencia',
            'Asignar Dias Remotos',
            'Aprobar Permisos Laborales',
            'Aprobar Vacaciones',
            'Generar Planillas',
            'Aprobar Planillas',
            'Generar Liquidaciones',
            'Gestionar Evaluaciones',
            'Gestionar Anticipos',
            'Gestionar Reglamentos',
            'Gestionar Tipos',
            'Gestionar Gestiones',
            'Gestionar Usuarios',
        ];

        $permissions = array_merge($verModulos, $acciones);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions($permissions);

        $rrhh = Role::firstOrCreate(['name' => 'RRHH']);
        $rrhh->syncPermissions([
            'Ver Inicio', 'Ver Personas', 'Ver Empleados', 'Ver Cargos', 'Ver Áreas', 'Ver Empresas',
            'Ver Contratos', 'Ver Documentos', 'Ver Asistencia', 'Ver Vacaciones', 'Ver Horas Extra',
            'Ver Planillas', 'Ver Liquidaciones', 'Ver Evaluaciones', 'Ver Reglamentos',
            'Ver Notificaciones', 'Ver Anticipos', 'Ver Reportes', 'Ver Tipos', 'Ver Gestiones',
            'Marcar Asistencia', 'Importar Asistencia', 'Editar Matriz Asistencia', 'Asignar Dias Remotos',
            'Aprobar Permisos Laborales', 'Aprobar Vacaciones',
            'Generar Planillas', 'Generar Liquidaciones', 'Gestionar Evaluaciones',
            'Gestionar Anticipos', 'Gestionar Reglamentos', 'Gestionar Tipos', 'Gestionar Gestiones',
        ]);

        $gerente = Role::firstOrCreate(['name' => 'Gerente/Supervisor']);
        $gerente->syncPermissions([
            'Ver Inicio', 'Ver Empleados', 'Ver Asistencia', 'Ver Vacaciones', 'Ver Horas Extra',
            'Ver Evaluaciones', 'Ver Reportes', 'Ver Notificaciones',
            'Aprobar Permisos Laborales', 'Aprobar Vacaciones', 'Gestionar Evaluaciones',
        ]);

        $empleado = Role::firstOrCreate(['name' => 'Empleado']);
        $empleado->syncPermissions([
            'Ver Inicio', 'Ver Vacaciones', 'Ver Notificaciones', 'Marcar Asistencia', 'Ver Reglamentos',
        ]);
    }
}
