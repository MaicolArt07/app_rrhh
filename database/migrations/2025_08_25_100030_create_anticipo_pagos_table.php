<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anticipo_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_anticipo')->constrained('anticipos_prestamos')->cascadeOnDelete();
            $table->foreignId('id_planilla_detalle')->nullable()->constrained('planilla_detalles')->nullOnDelete();
            $table->decimal('monto_pagado', 10, 2);
            $table->date('fecha_pago');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anticipo_pagos');
    }
};
