<?php

namespace App\Support;

/**
 * Convierte montos a letras en español, como se escriben en los contratos:
 * 4009.62 → "cuatro mil nueve" y centavos "62".
 */
class NumeroALetras
{
    private const UNIDADES = ['', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve',
        'veinte', 'veintiuno', 'veintidós', 'veintitrés', 'veinticuatro', 'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho', 'veintinueve'];

    private const DECENAS = ['', '', '', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];

    private const CENTENAS = ['', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos',
        'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];

    /** Parte entera en letras (en minúsculas). */
    public static function entero(int $numero): string
    {
        if ($numero === 0) {
            return 'cero';
        }

        $partes = [];
        $millones = intdiv($numero, 1_000_000);
        $miles = intdiv($numero % 1_000_000, 1000);
        $resto = $numero % 1000;

        if ($millones) {
            $partes[] = $millones === 1 ? 'un millón' : self::apocope(self::menorMil($millones)).' millones';
        }

        if ($miles) {
            $partes[] = $miles === 1 ? 'mil' : self::apocope(self::menorMil($miles)).' mil';
        }

        if ($resto) {
            $partes[] = self::menorMil($resto);
        }

        return implode(' ', $partes);
    }

    /** Centavos con dos dígitos ("62", "00"). */
    public static function centavos(float $monto): string
    {
        return str_pad((string) (int) round(($monto - floor($monto)) * 100), 2, '0', STR_PAD_LEFT);
    }

    private static function menorMil(int $n): string
    {
        if ($n === 100) {
            return 'cien';
        }

        $centena = intdiv($n, 100);
        $resto = $n % 100;
        $texto = self::CENTENAS[$centena];

        if ($resto) {
            $texto .= ($texto ? ' ' : '').self::menorCien($resto);
        }

        return $texto;
    }

    private static function menorCien(int $n): string
    {
        if ($n < 30) {
            return self::UNIDADES[$n];
        }

        $decena = self::DECENAS[intdiv($n, 10)];
        $unidad = $n % 10;

        return $unidad ? "{$decena} y ".self::UNIDADES[$unidad] : $decena;
    }

    /** "uno" delante de "mil"/"millones" se escribe "un" ("veintiún mil"). */
    private static function apocope(string $texto): string
    {
        return preg_replace(['/veintiuno$/', '/uno$/'], ['veintiún', 'un'], $texto);
    }
}
