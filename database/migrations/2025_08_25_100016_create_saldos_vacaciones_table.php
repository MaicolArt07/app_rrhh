<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saldos_vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('id_gestion')->constrained('gestiones')->restrictOnDelete();
            $table->decimal('dias_generados', 5, 2)->default(0);
            $table->decimal('dias_tomados', 5, 2)->default(0);
            $table->decimal('dias_disponibles', 5, 2)->default(0);
            $table->date('fecha_ultimo_calculo')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->unique(['id_empleado', 'id_gestion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldos_vacaciones');
    }
};
