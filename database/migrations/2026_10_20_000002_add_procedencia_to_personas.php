<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Departamento y provincia del colaborador ("DPTO - Provincia" en la Base de Colaboradores).
 * Se usa para sugerir el lugar de expedición del CI en los contratos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->string('procedencia', 100)->nullable()->after('ci_expedido');
        });
    }

    public function down(): void
    {
        Schema::table('personas', fn (Blueprint $table) => $table->dropColumn('procedencia'));
    }
};
