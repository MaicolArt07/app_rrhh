<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planilla_detalle_conceptos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_planilla_detalle')->constrained('planilla_detalles')->cascadeOnDelete();
            $table->foreignId('id_concepto_pago')->constrained('conceptos_pago')->restrictOnDelete();
            $table->decimal('monto', 10, 2);
            $table->string('observacion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planilla_detalle_conceptos');
    }
};
