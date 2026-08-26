<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conceptos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo_concepto', ['ingreso', 'deduccion']);
            $table->boolean('es_fijo')->default(false);
            $table->decimal('valor_por_defecto', 10, 2)->nullable();
            $table->boolean('es_porcentaje')->default(false);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conceptos_pago');
    }
};
