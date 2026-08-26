<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planilla_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_planilla')->constrained('planillas')->cascadeOnDelete();
            $table->foreignId('id_empleado')->constrained('empleados')->restrictOnDelete();
            $table->foreignId('id_cargo')->constrained('cargos')->restrictOnDelete();
            $table->decimal('sueldo_base', 10, 2);
            $table->decimal('dias_trabajados', 5, 2)->default(0);
            $table->decimal('horas_extra', 5, 2)->default(0);
            $table->decimal('total_ingresos', 10, 2)->default(0);
            $table->decimal('total_deducciones', 10, 2)->default(0);
            $table->decimal('sueldo_liquido', 10, 2)->default(0);
            $table->decimal('total_pagado', 10, 2)->default(0);
            $table->enum('estado_pago', ['pendiente', 'pagado'])->default('pendiente');
            $table->date('fecha_pago')->nullable();
            $table->enum('metodo_pago', ['transferencia', 'efectivo', 'cheque'])->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planilla_detalles');
    }
};
