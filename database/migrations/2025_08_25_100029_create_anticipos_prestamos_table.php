<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anticipos_prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('id_tipo_anticipo')->constrained('tipos')->restrictOnDelete();
            $table->decimal('monto_total', 10, 2);
            $table->unsignedSmallInteger('numero_cuotas');
            $table->decimal('monto_cuota', 10, 2);
            $table->decimal('saldo_pendiente', 10, 2);
            $table->date('fecha_otorgado');
            $table->enum('estado_anticipo', ['activo', 'pagado', 'cancelado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anticipos_prestamos');
    }
};
