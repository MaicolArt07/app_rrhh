<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planillas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_gestion')->constrained('gestiones')->restrictOnDelete();
            $table->foreignId('id_mes')->nullable()->constrained('meses')->restrictOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->foreignId('id_tipo_planilla')->constrained('tipos')->restrictOnDelete();
            $table->foreignId('id_estado_planilla')->constrained('tipos')->restrictOnDelete();
            $table->decimal('total_planilla', 12, 2)->default(0);
            $table->foreignId('id_generado_por')->constrained('users')->restrictOnDelete();
            $table->foreignId('id_aprobado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planillas');
    }
};
