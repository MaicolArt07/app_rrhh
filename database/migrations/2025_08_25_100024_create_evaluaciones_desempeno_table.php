<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_desempeno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('id_evaluador')->constrained('users')->restrictOnDelete();
            $table->foreignId('id_gestion')->constrained('gestiones')->restrictOnDelete();
            $table->enum('semestre', ['primero', 'segundo']);
            $table->date('fecha_evaluacion');
            $table->decimal('puntuacion_total', 5, 2)->default(0);
            $table->foreignId('id_resultado_evaluacion')->constrained('tipos')->restrictOnDelete();
            $table->text('comentarios')->nullable();
            $table->text('plan_mejora')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_desempeno');
    }
};
