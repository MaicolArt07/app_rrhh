<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Empresas del grupo DeltaX. Cada empleado pertenece a una empresa, y cada
 * contrato guarda la empresa con la que se firmó (base para contratos,
 * certificados de trabajo, memorandos y planillas por empresa).
 */
return new class extends Migration
{
    public const EMPRESAS = [
        ['DC', 'Delta Cargo', '#F39200'],
        ['DX', 'Delta Express', '#111827'],
    ];

    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('nombre', 150)->unique();
            $table->string('razon_social', 200)->nullable();
            $table->string('nit', 30)->nullable();
            $table->string('representante_legal', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->char('color_hex', 7)->default('#6B7280');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        foreach (self::EMPRESAS as [$codigo, $nombre, $color]) {
            DB::table('empresas')->insert([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'color_hex' => $color,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('empleados', function (Blueprint $table) {
            $table->foreignId('id_empresa')->nullable()->after('id_persona')->constrained('empresas')->restrictOnDelete();
        });

        Schema::table('contratos', function (Blueprint $table) {
            $table->foreignId('id_empresa')->nullable()->after('id_empleado')->constrained('empresas')->restrictOnDelete();
        });

        Permission::firstOrCreate(['name' => 'Ver Empresas', 'guard_name' => 'web']);
        foreach (['Super Admin', 'RRHH'] as $rol) {
            Role::where('name', $rol)->first()?->givePermissionTo('Ver Empresas');
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Schema::table('contratos', fn (Blueprint $table) => $table->dropConstrainedForeignId('id_empresa'));
        Schema::table('empleados', fn (Blueprint $table) => $table->dropConstrainedForeignId('id_empresa'));
        Schema::dropIfExists('empresas');

        Permission::where('name', 'Ver Empresas')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
