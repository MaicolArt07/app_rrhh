<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Tablas que apuntan al catálogo de áreas (antes departamentos).
     */
    private array $tablas = ['empleados', 'cargos'];

    public function up(): void
    {
        $this->renombrar('departamentos', 'areas', 'id_departamento', 'id_area');

        DB::table('permissions')->where('name', 'Ver Departamentos')->update(['name' => 'Ver Áreas']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $this->renombrar('areas', 'departamentos', 'id_area', 'id_departamento');

        DB::table('permissions')->where('name', 'Ver Áreas')->update(['name' => 'Ver Departamentos']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function renombrar(string $tablaOrigen, string $tablaDestino, string $columnaOrigen, string $columnaDestino): void
    {
        // Algunas bases (p. ej. la local, restaurada desde un volcado) no tienen
        // las claves foráneas creadas, así que solo se eliminan si existen.
        foreach ($this->tablas as $tabla) {
            if ($this->existeForanea($tabla, "{$tabla}_{$columnaOrigen}_foreign")) {
                Schema::table($tabla, fn (Blueprint $table) => $table->dropForeign("{$tabla}_{$columnaOrigen}_foreign"));
            }
        }

        if (Schema::hasTable($tablaOrigen)) {
            Schema::rename($tablaOrigen, $tablaDestino);
        }

        foreach ($this->tablas as $tabla) {
            // MariaDB < 10.5 no soporta RENAME INDEX: se elimina el índice viejo y
            // la clave foránea nueva crea el suyo con el nombre correcto.
            if ($this->existeIndice($tabla, "{$tabla}_{$columnaOrigen}_foreign")) {
                Schema::table($tabla, fn (Blueprint $table) => $table->dropIndex("{$tabla}_{$columnaOrigen}_foreign"));
            }

            if (Schema::hasColumn($tabla, $columnaOrigen)) {
                Schema::table($tabla, fn (Blueprint $table) => $table->renameColumn($columnaOrigen, $columnaDestino));
            }

            Schema::table($tabla, function (Blueprint $table) use ($columnaDestino, $tablaDestino) {
                $table->foreign($columnaDestino)->references('id')->on($tablaDestino)->restrictOnDelete();
            });
        }
    }

    private function existeForanea(string $tabla, string $nombre): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $tabla)
            ->where('CONSTRAINT_NAME', $nombre)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }

    private function existeIndice(string $tabla, string $nombre): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $tabla)
            ->where('INDEX_NAME', $nombre)
            ->exists();
    }
};
