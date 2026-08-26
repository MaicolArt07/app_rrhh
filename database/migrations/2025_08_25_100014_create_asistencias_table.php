<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('id_gestion')->constrained('gestiones')->restrictOnDelete();
            $table->foreignId('id_mes')->constrained('meses')->restrictOnDelete();
            $table->date('fecha');
            $table->dateTime('hora_entrada')->nullable();
            $table->dateTime('hora_salida')->nullable();
            $table->boolean('hora_entrada_marcada_manual')->default(false);
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->foreignId('id_estado_asistencia')->constrained('tipos')->restrictOnDelete();
            $table->decimal('horas_trabajadas', 5, 2)->default(0);
            $table->decimal('horas_extra', 5, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->unique(['id_empleado', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
