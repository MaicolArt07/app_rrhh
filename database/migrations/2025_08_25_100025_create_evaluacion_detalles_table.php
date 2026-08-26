<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_evaluacion')->constrained('evaluaciones_desempeno')->cascadeOnDelete();
            $table->string('criterio');
            $table->decimal('puntuacion', 5, 2);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion_detalles');
    }
};
