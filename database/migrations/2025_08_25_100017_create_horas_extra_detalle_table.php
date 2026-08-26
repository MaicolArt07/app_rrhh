<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horas_extra_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('id_asistencia')->nullable()->constrained('asistencias')->nullOnDelete();
            $table->foreignId('id_gestion')->constrained('gestiones')->restrictOnDelete();
            $table->foreignId('id_mes')->constrained('meses')->restrictOnDelete();
            $table->date('fecha');
            $table->foreignId('id_tipo_hora_extra')->constrained('tipos')->restrictOnDelete();
            $table->decimal('horas', 5, 2);
            $table->decimal('valor_hora_base', 10, 2);
            $table->decimal('monto_calculado', 10, 2);
            // id_planilla_detalle: FK agregada en 2025_08_25_100022 una vez existe planilla_detalles
            $table->unsignedBigInteger('id_planilla_detalle')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horas_extra_detalle');
    }
};
