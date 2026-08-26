<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liquidaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('id_tipo_liquidacion')->constrained('tipos')->restrictOnDelete();
            $table->date('fecha_liquidacion');
            $table->decimal('tiempo_laborado_anios', 5, 2);
            $table->decimal('salario_promedio', 10, 2);
            $table->decimal('monto_indemnizacion', 10, 2)->default(0);
            $table->decimal('monto_vacaciones_pendientes', 10, 2)->default(0);
            $table->decimal('monto_aguinaldo_proporcional', 10, 2)->default(0);
            $table->decimal('monto_bono14_proporcional', 10, 2)->default(0);
            $table->decimal('total_liquidacion', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liquidaciones');
    }
};
