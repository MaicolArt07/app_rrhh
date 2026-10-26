<?php

use App\Models\Asistencia;
use Database\Seeders\TipoAsistenciaSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Módulo de asistencia con reporte biométrico (docs/modulo-asistencia-correcciones.md §3 y §6).
 */
return new class extends Migration
{
    public const PERMISOS = ['Importar Asistencia', 'Editar Matriz Asistencia', 'Asignar Dias Remotos'];

    public function up(): void
    {
        Schema::create('tipos_asistencia', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 80);
            $table->char('color_hex', 7);
            $table->boolean('es_justificado')->default(false);
            $table->boolean('es_sancionable')->default(false);
            $table->boolean('seleccionable_manual')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('dias_remotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('observacion', 255)->nullable();
            $table->foreignId('id_asignado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->unique(['id_empleado', 'fecha']);
        });

        Schema::create('importaciones_biometrico', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_archivo');
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('total_empleados')->default(0);
            $table->unsignedInteger('total_no_vinculados')->default(0);
            $table->string('estado', 20)->default('procesado');
            $table->timestamps();
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->string('codigo_biometrico', 20)->nullable()->unique()->after('codigo_empleado');
        });

        Schema::table('asistencias', function (Blueprint $table) {
            $table->foreignId('id_tipo_asistencia')->nullable()->after('id_estado_asistencia')
                ->constrained('tipos_asistencia')->restrictOnDelete();
            $table->json('marcaciones')->nullable()->after('hora_salida');
            $table->string('marcaciones_crudas', 100)->nullable()->after('marcaciones');
            $table->unsignedSmallInteger('minutos_atraso')->default(0)->after('id_tipo_asistencia');
            $table->boolean('es_sancionable')->default(false)->after('minutos_atraso');
            $table->unsignedSmallInteger('numero_evento_mes')->nullable()->after('es_sancionable');
            $table->decimal('multa_dia', 8, 2)->default(0)->after('numero_evento_mes');
            $table->boolean('tipo_asignado_manual')->default(false)->after('multa_dia');
            $table->foreignId('id_usuario_modifico')->nullable()->after('tipo_asignado_manual')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('id_importacion')->nullable()->after('id_usuario_modifico')
                ->constrained('importaciones_biometrico')->nullOnDelete();
            $table->enum('origen', ['biometrico', 'manual'])->default('manual')->after('id_importacion');
        });

        // Las filas del biométrico se clasifican con tipos_asistencia; el estado
        // del catálogo genérico queda como dato opcional para compatibilidad.
        Schema::table('asistencias', function (Blueprint $table) {
            $table->unsignedBigInteger('id_estado_asistencia')->nullable()->change();
        });

        $this->sembrarTipos();
        $this->mapearAsistenciasExistentes();
        $this->crearPermisos();
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_importacion');
            $table->dropConstrainedForeignId('id_usuario_modifico');
            $table->dropConstrainedForeignId('id_tipo_asistencia');
            $table->dropColumn([
                'marcaciones', 'marcaciones_crudas', 'minutos_atraso', 'es_sancionable',
                'numero_evento_mes', 'multa_dia', 'tipo_asignado_manual', 'origen',
            ]);
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->dropUnique(['codigo_biometrico']);
            $table->dropColumn('codigo_biometrico');
        });

        Schema::dropIfExists('importaciones_biometrico');
        Schema::dropIfExists('dias_remotos');
        Schema::dropIfExists('tipos_asistencia');

        Permission::whereIn('name', self::PERMISOS)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function sembrarTipos(): void
    {
        foreach (TipoAsistenciaSeeder::TIPOS as $orden => [$codigo, $nombre, $color, $justificado, $sancionable, $seleccionable]) {
            DB::table('tipos_asistencia')->insert([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'color_hex' => $color,
                'es_justificado' => $justificado,
                'es_sancionable' => $sancionable,
                'seleccionable_manual' => $seleccionable,
                'orden' => $orden,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function mapearAsistenciasExistentes(): void
    {
        foreach (Asistencia::MAPEO_ESTADOS as $codigoEstado => $codigoTipo) {
            $idEstado = DB::table('tipos')->where('categoria', 'estado_asistencia')->where('codigo', $codigoEstado)->value('id');
            $idTipo = DB::table('tipos_asistencia')->where('codigo', $codigoTipo)->value('id');

            if ($idEstado && $idTipo) {
                DB::table('asistencias')->where('id_estado_asistencia', $idEstado)->update(['id_tipo_asistencia' => $idTipo]);
            }
        }
    }

    private function crearPermisos(): void
    {
        foreach (self::PERMISOS as $nombre) {
            Permission::firstOrCreate(['name' => $nombre, 'guard_name' => 'web']);
        }

        foreach (['Super Admin', 'RRHH'] as $rol) {
            Role::where('name', $rol)->first()?->givePermissionTo(self::PERMISOS);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
