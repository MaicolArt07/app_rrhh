<?php

namespace Database\Seeders;

use App\Models\Gestion;
use Illuminate\Database\Seeder;

class GestionSeeder extends Seeder
{
    public function run(): void
    {
        $anioActual = (int) date('Y');

        // Gestión anterior (para consultas históricas), actual y dos futuras.
        foreach (range($anioActual - 1, $anioActual + 2) as $anio) {
            Gestion::updateOrCreate(
                ['anio' => $anio],
                [
                    'fecha_inicio' => "{$anio}-01-01",
                    'fecha_fin' => "{$anio}-12-31",
                    'estado' => $anio <= $anioActual,
                ]
            );
        }
    }
}
