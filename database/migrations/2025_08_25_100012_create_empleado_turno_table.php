<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleado_turno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('id_turno')->constrained('turnos')->cascadeOnDelete();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->unique(['id_empleado', 'id_turno']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleado_turno');
    }
};
