<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reglamentos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->longText('contenido');
            $table->string('version');
            $table->date('vigente_desde');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglamentos');
    }
};
