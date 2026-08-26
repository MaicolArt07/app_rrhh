<?php

namespace Database\Seeders;

use App\Models\Mes;
use Illuminate\Database\Seeder;

class MesSeeder extends Seeder
{
    public function run(): void
    {
        $meses = [
            [1, 'Enero', 'Ene'],
            [2, 'Febrero', 'Feb'],
            [3, 'Marzo', 'Mar'],
            [4, 'Abril', 'Abr'],
            [5, 'Mayo', 'May'],
            [6, 'Junio', 'Jun'],
            [7, 'Julio', 'Jul'],
            [8, 'Agosto', 'Ago'],
            [9, 'Septiembre', 'Sep'],
            [10, 'Octubre', 'Oct'],
            [11, 'Noviembre', 'Nov'],
            [12, 'Diciembre', 'Dic'],
        ];

        foreach ($meses as [$numero, $nombre, $abreviatura]) {
            Mes::updateOrCreate(
                ['numero' => $numero],
                ['nombre' => $nombre, 'abreviatura' => $abreviatura, 'estado' => true]
            );
        }
    }
}
