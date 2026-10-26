<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            // N.º de contrato del empleado dentro del mismo tipo (1.º, 2.º prestación…).
            $table->unsignedSmallInteger('numero_contrato')->default(1)->after('id_tipo_contrato');
            // Contrato al que reemplaza (renovación o paso a indefinido).
            $table->foreignId('id_contrato_anterior')->nullable()->after('numero_contrato')
                ->constrained('contratos')->nullOnDelete();
        });

        $this->numerarContratosExistentes();

        // Solo se usan dos tipos de contrato; los demás se desactivan si nadie los usa.
        DB::table('tipos')
            ->where('categoria', 'tipo_contrato')
            ->whereNotIn('codigo', ['indefinido', 'prestacion'])
            ->whereNotIn('id', DB::table('contratos')->select('id_tipo_contrato'))
            ->update(['estado' => false]);

        foreach ([
            ['tipo_contrato', 'prestacion', 'Prestación de Servicios', 1],
            ['tipo_notificacion', 'segundo_contrato', 'Segundo Contrato por Prestación', 6],
        ] as [$categoria, $codigo, $nombre, $orden]) {
            if (! DB::table('tipos')->where('categoria', $categoria)->where('codigo', $codigo)->exists()) {
                DB::table('tipos')->insert([
                    'categoria' => $categoria,
                    'codigo' => $codigo,
                    'nombre' => $nombre,
                    'orden' => $orden,
                    'estado' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_contrato_anterior');
            $table->dropColumn('numero_contrato');
        });
    }

    private function numerarContratosExistentes(): void
    {
        $contratos = DB::table('contratos')->orderBy('id_empleado')->orderBy('fecha_inicio')->orderBy('id')->get();

        foreach ($contratos->groupBy('id_empleado') as $delEmpleado) {
            $porTipo = [];
            $anterior = null;

            foreach ($delEmpleado as $contrato) {
                $porTipo[$contrato->id_tipo_contrato] = ($porTipo[$contrato->id_tipo_contrato] ?? 0) + 1;

                DB::table('contratos')->where('id', $contrato->id)->update([
                    'numero_contrato' => $porTipo[$contrato->id_tipo_contrato],
                    'id_contrato_anterior' => $anterior,
                ]);

                $anterior = $contrato->id;
            }
        }
    }
};
