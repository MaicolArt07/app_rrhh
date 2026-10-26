<?php

namespace Database\Seeders;

use App\Models\Tipo;
use Illuminate\Database\Seeder;

class TipoSeeder extends Seeder
{
    public function run(): void
    {
        $catalogo = [
            'tipo_documento' => [
                ['dpi', 'DPI'],
                ['pasaporte', 'Pasaporte'],
                ['licencia', 'Licencia'],
                ['partida_nacimiento', 'Partida de Nacimiento'],
            ],
            'estado_empleado' => [
                ['activo', 'Activo'],
                ['inactivo', 'Inactivo'],
                ['suspendido', 'Suspendido'],
                ['baja', 'Baja'],
            ],
            'tipo_permiso' => [
                ['vacaciones', 'Vacaciones'],
                ['enfermedad', 'Enfermedad'],
                ['personal', 'Personal'],
                ['luto', 'Luto'],
                ['maternidad_paternidad', 'Maternidad / Paternidad'],
            ],
            'tipo_planilla' => [
                ['quincenal', 'Quincenal'],
                ['mensual', 'Mensual'],
                ['aguinaldo', 'Aguinaldo'],
                ['bono14', 'Bono 14'],
            ],
            'estado_planilla' => [
                ['borrador', 'Borrador'],
                ['revisada', 'Revisada'],
                ['aprobada', 'Aprobada'],
                ['pagada', 'Pagada'],
            ],
            'estado_asistencia' => [
                ['puntual', 'Puntual'],
                ['tarde', 'Tarde'],
                ['ausente', 'Ausente'],
                ['permiso', 'Permiso'],
                ['vacaciones', 'Vacaciones'],
            ],
            'tipo_contrato' => [
                ['indefinido', 'Indefinido'],
                ['prestacion', 'Prestación de Servicios'],
            ],
            'tipo_documento_empleado' => [
                ['licencia_conducir', 'Licencia de Conducir'],
                ['certificado_medico', 'Certificado Médico'],
                ['antecedentes_penales', 'Antecedentes Penales'],
                ['titulo_academico', 'Título Académico'],
                ['carnet_salud', 'Carnet de Salud'],
            ],
            'tipo_hora_extra' => [
                ['diurna', 'Diurna', 1.25],
                ['nocturna', 'Nocturna', 1.50],
                ['dia_feriado', 'Día Feriado', 2.00],
            ],
            'tipo_liquidacion' => [
                ['renuncia', 'Renuncia'],
                ['despido_justificado', 'Despido Justificado'],
                ['despido_injustificado', 'Despido Injustificado'],
                ['mutuo_acuerdo', 'Mutuo Acuerdo'],
            ],
            'resultado_evaluacion' => [
                ['excelente', 'Excelente'],
                ['bueno', 'Bueno'],
                ['regular', 'Regular'],
                ['deficiente', 'Deficiente'],
            ],
            'tipo_notificacion' => [
                ['contrato_por_vencer', 'Contrato por Vencer'],
                ['documento_por_vencer', 'Documento por Vencer'],
                ['reglamento_nuevo', 'Reglamento Nuevo'],
                ['planilla_lista', 'Planilla Lista'],
                ['permiso_solicitado', 'Permiso Solicitado'],
                ['permiso_aprobado', 'Permiso Aprobado'],
                ['segundo_contrato', 'Segundo Contrato por Prestación'],
            ],
            'tipo_anticipo' => [
                ['anticipo_sueldo', 'Anticipo de Sueldo'],
                ['prestamo_personal', 'Préstamo Personal'],
                ['prestamo_estudio', 'Préstamo de Estudio'],
                ['otro', 'Otro'],
            ],
        ];

        foreach ($catalogo as $categoria => $valores) {
            foreach ($valores as $orden => $valor) {
                Tipo::updateOrCreate(
                    ['categoria' => $categoria, 'codigo' => $valor[0]],
                    [
                        'nombre' => $valor[1],
                        'valor_referencia' => $valor[2] ?? null,
                        'orden' => $orden,
                        'estado' => true,
                    ]
                );
            }
        }
    }
}
