<?php

/*
 * Reglas del módulo de asistencia (docs/modulo-asistencia-correcciones.md §4).
 */
return [

    // Horario por defecto cuando el empleado no tiene turno asignado (§4.3).
    'hora_entrada' => '08:00',
    'tolerancia_minutos' => 10,

    // Marcas repetidas con esta diferencia o menos se unen en una sola (§4.4).
    'minutos_duplicado' => 5,

    // Multa: los primeros N eventos sancionables del mes se perdonan y cada
    // evento a partir del siguiente cuesta este monto (§4.3).
    'eventos_perdonados' => 3,
    'multa_por_evento' => 20,

    // Hoja del reporte del reloj ZKTeco que contiene las marcaciones (§2).
    'hoja_reporte' => 'Reporte de Asistencia',
];
