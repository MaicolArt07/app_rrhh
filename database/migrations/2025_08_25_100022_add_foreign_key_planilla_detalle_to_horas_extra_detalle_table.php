<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horas_extra_detalle', function (Blueprint $table) {
            $table->foreign('id_planilla_detalle')->references('id')->on('planilla_detalles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('horas_extra_detalle', function (Blueprint $table) {
            $table->dropForeign(['id_planilla_detalle']);
        });
    }
};
