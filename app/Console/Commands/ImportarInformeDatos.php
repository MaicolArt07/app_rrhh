<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\Cargo;
use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Persona;
use App\Models\Tipo;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Completa los datos de los empleados con la carpeta informe-datos/:
 * - "Cuentas Banco Bisa": CI expedido, celular, correo personal, cuenta y banco.
 * - "Correos para Foro": correo corporativo @deltax.la.
 *
 * Los PDF se convierten a JSON con tools/parsear_informe_datos.py. Un dato solo se
 * registra si el CI y el nombre identifican al mismo empleado, y nunca se pisa un
 * dato que ya existe con otro valor: esos casos quedan en el reporte.
 */
class ImportarInformeDatos extends Command
{
    protected $signature = 'rrhh:importar-informe-datos
        {json=storage/app/importaciones/informe-datos-2026-10.json : Datos extraídos de los PDF}
        {--dry-run : Muestra los cambios sin guardarlos}';

    protected $description = 'Registra CI expedido, celular, correo personal y corporativo, y cuenta bancaria de cada empleado (carpeta informe-datos)';

    /** Notas del PDF que indican un banco distinto de Bisa. */
    private const BANCOS = [
        'bcp' => 'Banco BCP',
        'economico' => 'Banco Económico',
        'ganadero' => 'Banco Ganadero',
    ];

    private const EXPEDIDOS = ['SC' => 'SC', 'LP' => 'LP', 'CB' => 'CB', 'CBBA' => 'CB', 'OR' => 'OR', 'PT' => 'PT',
        'TJ' => 'TJ', 'CH' => 'CH', 'BE' => 'BE', 'PD' => 'PD', 'PY' => 'PY'];

    private array $cambios = [];
    private array $revisar = [];

    public function handle(): int
    {
        $ruta = base_path($this->argument('json'));

        if (! is_file($ruta)) {
            $this->error("No se encontró {$ruta}. Genérelo con: python tools/parsear_informe_datos.py");

            return self::FAILURE;
        }

        $datos = json_decode(file_get_contents($ruta), true);
        $empleados = Empleado::with('persona')->get();

        DB::beginTransaction();

        try {
            // Primero el listado oficial de correos corporativos: tiene prioridad sobre
            // direcciones @deltax.la que aparezcan en la columna de correo personal del banco.
            foreach ($datos['corporativos'] as $fila) {
                $this->aplicarCorreoCorporativo($fila, $empleados);
            }

            foreach ($datos['banco'] as $fila) {
                $this->aplicarBanco($fila, $empleados);
            }

            $this->option('dry-run') ? DB::rollBack() : DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('No se guardó ningún cambio: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info($this->option('dry-run') ? 'SIMULACIÓN: no se guardó ningún cambio.' : 'Datos registrados.');
        $this->line('<options=bold>Cambios ('.count($this->cambios).')</>');
        $this->table(['Empleado', 'Dato', 'Antes', 'Ahora'], $this->cambios);
        $this->line('<options=bold>Para revisar ('.count($this->revisar).')</>');
        $this->table(['Empleado / fila', 'Motivo'], $this->revisar);

        return self::SUCCESS;
    }

    private function aplicarBanco(array $fila, Collection $empleados): void
    {
        [$ci, $expedido] = $this->separarCi((string) ($fila['ci'] ?? ''));
        $nombre = trim(str_replace('✨', '', $fila['nombre']));
        $porCi = $empleados->first(fn ($e) => $this->ciBase($e->persona->numero_documento) === $this->ciBase($ci));
        $porNombre = $this->buscarPorNombre($nombre, $empleados);

        $empleado = match (true) {
            $porCi && $this->mismoNombre($nombre, $porCi->persona->nombreCompleto()) => $porCi,
            $porCi && ! $porNombre => null,
            default => $porNombre,
        };

        if ($porCi && $porNombre && $porCi->id !== $porNombre->id) {
            $this->revisar[] = ["{$nombre} (CI banco {$ci})", "El CI del banco es de {$porCi->persona->nombreCompleto()} en la base; no se aplicó nada a esa persona. Datos asignados por nombre a {$porNombre->persona->nombreCompleto()} (CI base {$porNombre->persona->numero_documento})."];
        } elseif ($porCi && ! $empleado) {
            $this->revisar[] = ["{$nombre} (CI banco {$ci})", "El CI coincide con {$porCi->persona->nombreCompleto()} pero el nombre no; no se aplicó."];
        }

        if (! $empleado) {
            if (strtolower($fila['estado'] ?? '') === 'activo' && ! $porCi) {
                $this->revisar[] = [$nombre, "Activo en el banco pero no está registrado como empleado (CI {$ci})."];
            }

            return;
        }

        $persona = $empleado->persona;
        $etiqueta = $persona->nombreCompleto();
        $ciCoincide = $this->ciBase($persona->numero_documento) === $this->ciBase($ci);

        if (! $ciCoincide && $ci !== '') {
            $this->revisar[] = [$etiqueta, "CI distinto: base {$persona->numero_documento} / banco {$ci}. Se mantiene el de la base y no se toma el expedido."];
        }

        if ($expedido && $ciCoincide) {
            $this->asignar($persona, 'ci_expedido', $expedido, $etiqueta, 'CI expedido');
        }

        if ($fila['celular'] ?? null) {
            $this->asignar($persona, 'telefono', $fila['celular'], $etiqueta, 'Celular');
        }

        if ($correo = $fila['correo'] ?? null) {
            $correo = Str::lower(trim($correo, " ,;"));
            if (! filter_var($correo, FILTER_VALIDATE_EMAIL) || preg_match('/\.(con|cmo)$|@gmai\./', $correo)) {
                $this->revisar[] = [$etiqueta, "Correo personal con error de escritura ({$correo}); no se registró."];
            } elseif (str_ends_with($correo, '@deltax.la')) {
                $this->asignar($empleado, 'email_corporativo', $correo, $etiqueta, 'Correo corporativo');
            } else {
                $this->asignar($persona, 'email', $correo, $etiqueta, 'Correo personal');
            }
        }

        $cuenta = trim((string) ($fila['cuenta'] ?? ''));
        if ($cuenta !== '' && $cuenta !== '-') {
            $banco = $this->banco((string) ($fila['nota'] ?? ''));
            $this->asignar($empleado, 'numero_cuenta_bancaria', preg_replace('/\s*-\s*/', '-', $cuenta), $etiqueta, 'Cuenta bancaria');
            $this->asignar($empleado, 'banco', $banco, $etiqueta, 'Banco');

            if ($banco !== 'Banco Bisa' && str_contains($cuenta, '-401-')) {
                $this->revisar[] = [$etiqueta, "La cuenta {$cuenta} tiene formato de Banco Bisa, pero el PDF indica {$banco}. Verificar."];
            }
        } elseif ($fila['nota'] ?? null) {
            $this->revisar[] = [$etiqueta, "Sin cuenta en el PDF (nota: {$fila['nota']})."];
        }

        // Diferencias de estado entre el banco y la Base de Colaboradores (no se cambian aquí).
        $activoBanco = strtolower($fila['estado'] ?? '') === 'activo';
        if ($activoBanco !== (bool) $empleado->estado) {
            $this->revisar[] = [$etiqueta, 'Estado distinto: banco '.($activoBanco ? 'Activo' : 'Inactivo').' / base '.($empleado->estado ? 'Activo' : 'Inactivo').'. Se mantiene el de la base.'];
        }
    }

    private function aplicarCorreoCorporativo(array $fila, Collection $empleados): void
    {
        if (! $fila['correo']) {
            return;
        }

        $tokens = $this->tokens($fila['nombre']);
        $candidatos = $empleados->filter(function ($e) use ($tokens) {
            $suyos = $this->tokens($e->persona->nombreCompleto());

            // El primer nombre del listado y al menos un apellido deben coincidir.
            return in_array($tokens[0], $suyos, true) && count(array_intersect(array_slice($tokens, 1), $suyos)) >= 1;
        });

        if ($candidatos->count() > 1) {
            $candidatos = $candidatos->where('estado', true);
        }

        if ($candidatos->count() !== 1) {
            $this->revisar[] = [$fila['nombre'], $candidatos->isEmpty()
                ? "Correo {$fila['correo']}: no se encontró el empleado."
                : "Correo {$fila['correo']}: coincide con varios empleados; no se registró."];

            return;
        }

        $empleado = $candidatos->first();
        $this->asignar($empleado, 'email_corporativo', Str::lower(trim($fila['correo'])), $empleado->persona->nombreCompleto(), 'Correo corporativo');
    }

    /** Registra el valor si el campo está vacío; si ya tiene otro valor, lo deja para revisar. */
    private function asignar($modelo, string $campo, string $valor, string $etiqueta, string $dato): void
    {
        $actual = $modelo->{$campo};

        if ((string) $actual === $valor) {
            return;
        }

        if (filled($actual)) {
            $this->revisar[] = [$etiqueta, "{$dato}: ya registrado «{$actual}», el informe dice «{$valor}». Se mantiene el registrado."];

            return;
        }

        $modelo->update([$campo => $valor]);
        $this->cambios[] = [$etiqueta, $dato, '—', $valor];
    }

    /** "4485590 Cbba" → ["4485590", "CB"]; "9037014-1V SC" → ["9037014-1V", "SC"]; "E-10132031" → ["E-10132031", null]. */
    private function separarCi(string $texto): array
    {
        $partes = preg_split('/\s+/', trim($texto));
        $sufijo = count($partes) > 1 ? strtoupper(array_pop($partes)) : null;

        return [implode(' ', $partes), self::EXPEDIDOS[$sufijo] ?? null];
    }

    private function ciBase(?string $ci): string
    {
        return strtoupper(explode(' ', trim((string) $ci))[0]);
    }

    private function banco(string $nota): string
    {
        $nota = Str::lower(Str::ascii($nota));

        foreach (self::BANCOS as $clave => $nombre) {
            if (str_contains($nota, $clave)) {
                return $nombre;
            }
        }

        return 'Banco Bisa';
    }

    private function buscarPorNombre(string $nombre, Collection $empleados): ?Empleado
    {
        $candidatos = $empleados->filter(fn ($e) => $this->mismoNombre($nombre, $e->persona->nombreCompleto()));

        return $candidatos->count() === 1 ? $candidatos->first() : null;
    }

    /** Al menos tres palabras en común (o todas las del nombre más corto, si tiene dos). */
    private function mismoNombre(string $a, string $b): bool
    {
        $ta = $this->tokens($a);
        $tb = $this->tokens($b);
        $comunes = count(array_intersect($ta, $tb));

        return $comunes >= 3 || ($comunes >= 2 && $comunes === min(count($ta), count($tb)));
    }

    /** @return list<string> */
    private function tokens(string $nombre): array
    {
        $limpio = preg_replace('/[^a-z ]/', '', Str::lower(Str::ascii($nombre)));

        return array_values(array_filter(explode(' ', $limpio)));
    }
}
