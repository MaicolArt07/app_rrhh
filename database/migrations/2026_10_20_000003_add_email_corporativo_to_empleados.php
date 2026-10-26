<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Correo corporativo (@deltax.la). El correo personal sigue en personas.email.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->string('email_corporativo', 150)->nullable()->after('codigo_biometrico');
        });
    }

    public function down(): void
    {
        Schema::table('empleados', fn (Blueprint $table) => $table->dropColumn('email_corporativo'));
    }
};
