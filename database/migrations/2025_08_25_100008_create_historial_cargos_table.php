<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_cargos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('id_cargo_anterior')->nullable()->constrained('cargos')->nullOnDelete();
            $table->foreignId('id_cargo_nuevo')->constrained('cargos')->restrictOnDelete();
            $table->decimal('sueldo_anterior', 10, 2)->nullable();
            $table->decimal('sueldo_nuevo', 10, 2);
            $table->date('fecha_cambio');
            $table->text('motivo')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_cargos');
    }
};
