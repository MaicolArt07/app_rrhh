<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reglamento_lecturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_reglamento')->constrained('reglamentos')->cascadeOnDelete();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnDelete();
            $table->dateTime('fecha_lectura');
            $table->string('ip_confirmacion', 45)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->unique(['id_reglamento', 'id_empleado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglamento_lecturas');
    }
};
