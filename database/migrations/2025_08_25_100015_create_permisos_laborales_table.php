<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos_laborales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('id_tipo_permiso')->constrained('tipos')->restrictOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('dias_solicitados', 5, 2);
            $table->boolean('goce_sueldo')->default(true);
            $table->enum('estado_solicitud', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->foreignId('id_aprobado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_laborales');
    }
};
