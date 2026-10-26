<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Datos que se imprimen en los contratos descargables (contratos/*.pdf):
 * datos personales del trabajador y datos propios de cada contrato.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->string('ci_expedido', 10)->nullable()->after('numero_documento');
            $table->string('estado_civil', 30)->nullable()->after('genero');
            $table->string('profesion', 150)->nullable()->after('estado_civil');
        });

        Schema::table('contratos', function (Blueprint $table) {
            // Cargo o servicio y monto pactados en ese contrato (pueden diferir del actual).
            $table->string('cargo', 150)->nullable()->after('numero_contrato');
            $table->decimal('monto', 12, 2)->nullable()->after('cargo');
            // Solo contratos indefinidos.
            $table->string('dependencia', 150)->nullable()->after('monto');
            $table->text('funciones')->nullable()->after('dependencia');
            $table->text('herederos')->nullable()->after('funciones');
        });

        // Datos legales de las empresas tal como figuran en sus contratos.
        foreach ([
            'DC' => ['DELTA CARGO S.R.L.', '1025565029'],
            'DX' => ['DELTAEXPRESS S.A.', '373968026'],
        ] as $codigo => [$razonSocial, $nit]) {
            DB::table('empresas')->where('codigo', $codigo)->update([
                'razon_social' => DB::raw("COALESCE(razon_social, '{$razonSocial}')"),
                'nit' => DB::raw("COALESCE(nit, '{$nit}')"),
                'representante_legal' => DB::raw("COALESCE(representante_legal, 'Luis Fernando Ortíz Reque')"),
                'direccion' => DB::raw("COALESCE(direccion, 'Nueva Carretera a Warnes Urb. Trapero, Zona Nor Este, Santa Cruz de la Sierra')"),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('contratos', fn (Blueprint $table) => $table->dropColumn(['cargo', 'monto', 'dependencia', 'funciones', 'herederos']));
        Schema::table('personas', fn (Blueprint $table) => $table->dropColumn(['ci_expedido', 'estado_civil', 'profesion']));
    }
};
