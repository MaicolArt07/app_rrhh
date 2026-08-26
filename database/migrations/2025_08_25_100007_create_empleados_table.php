<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_persona')->constrained('personas')->restrictOnDelete();
            $table->string('codigo_empleado')->unique();
            $table->foreignId('id_cargo')->constrained('cargos')->restrictOnDelete();
            $table->foreignId('id_departamento')->constrained('departamentos')->restrictOnDelete();
            $table->date('fecha_ingreso');
            $table->date('fecha_baja')->nullable();
            $table->decimal('sueldo_actual', 10, 2);
            $table->foreignId('id_estado_empleado')->constrained('tipos')->restrictOnDelete();
            $table->string('numero_cuenta_bancaria')->nullable();
            $table->string('banco')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
