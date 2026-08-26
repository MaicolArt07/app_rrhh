<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos', function (Blueprint $table) {
            $table->id();
            $table->string('categoria');
            $table->string('codigo');
            $table->string('nombre');
            $table->decimal('valor_referencia', 10, 4)->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->unique(['categoria', 'codigo']);
            $table->index('categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos');
    }
};
