# Módulo de Asistencia: correcciones y nuevas funcionalidades

> Documento de especificación para llevar al sistema DeltaX RRHH (Laravel 12 + Livewire) el proceso que hoy se hace en Excel con la macro `VERD REPORTE MACRO DE ASISTENCIA.xlsm` sobre el reporte del biométrico (`1_report.xls`).
> Referencia visual: la *Matriz de Asistencia Coloreada* del periodo 2026-08-01 a 2026-08-31.

---

## 1. Situación actual

### 1.1 Proceso en Excel (macro)

1. Se descarga del reloj biométrico el archivo crudo (`1_report.xls`, formato ZKTeco con 4 hojas).
2. En `VERD REPORTE MACRO DE ASISTENCIA.xlsm` se pulsa **IMPORTAR REPORTE** (`AnalizarReporte`), que genera:
   - `Matriz_Coloreada`: un empleado por fila y un día hábil por columna, con colores por tipo.
   - `Resumen`: total de atrasos, minutos de atraso y multa por empleado.
   - `Detalle_Atrasos`: cada atraso con su hora, minutos, número de atraso del mes y multa del día.
3. RR.HH. marca a mano vacaciones, remoto, bajas, etc. (Estilos de celda) y pulsa **ACTUALIZAR COLORES**, que recalcula los totales y la multa.

### 1.2 Estado del sistema Laravel

| Existe | No existe |
|---|---|
| Tablas `asistencias`, `turnos`, `empleado_turno`, `feriados`, `permisos_laborales`, `saldos_vacaciones` | Importador del reporte biométrico |
| CRUD manual de asistencia (`app/Livewire/LivewireAsistencia.php`) | Matriz coloreada / leyenda |
| Catálogo genérico `tipos` (categoría `estado_asistencia`: puntual, tarde, ausente, permiso, vacaciones) | Cálculo de atrasos, minutos y multa |
| `empleados.fecha_ingreso` y `fecha_baja` | Uso de la fecha de ingreso en asistencia |
| `turnos.tolerancia_minutos` (no se usa en ningún lado) | Asignación de días remotos por empleado |
| | Relación entre el ID del biométrico y el empleado |

---

## 2. Formato del archivo del biométrico (`1_report.xls`)

El importador debe leer la hoja **por nombre**: `Reporte de Asistencia` (es la 3.ª hoja, no la 1.ª).

| Celda / fila | Contenido |
|---|---|
| `C3` | Periodo: `2026-08-01 ~ 2026-08-31` |
| Fila 4 | Números de día; la columna A es el día 1 y la columna AE el día 31 |
| Fila *r* con `A = "ID:"` | `C` = ID del biométrico, `K` = nombre (sin espacios, p. ej. `PaolaCalzadilla`), `U` = departamento |
| Fila *r+1* | Marcaciones del día como texto concatenado de bloques `HH:MM` |

Ejemplos de celdas de marcación:

| Valor crudo | Marcaciones |
|---|---|
| `08:0016:32` | 08:00, 16:32 |
| `08:06` | 08:06 (una sola) |
| `09:1717:2017:20` | 09:17, 17:20, 17:20 (duplicada) |
| `08:0508:0508:05` | tres marcas de entrada, ninguna de salida |
| *(vacío)* | ninguna |

Las otras hojas (`Reporte de Turnos`, `Reporte Estadístico`, `Reporte de Excepciones`) no se usan para el cálculo.

---

## 3. Catálogo de tipos de asistencia (tabla nueva `tipos_asistencia`)

Se crea una tabla propia, porque cada tipo necesita un color y reglas de cálculo que el catálogo genérico `tipos` no tiene.

| Código | Nombre | Color | Hex | ¿Justificado? | ¿Cuenta para multa? | ¿Descuento en planilla? |
|---|---|---|---|---|---|---|
| `VACACION` | Vacación | Amarillo | `#FFFF00` | Sí | No | No |
| `FERIADO` | Feriado | Celeste | `#ADD8FF` | Sí | No | No |
| `REMOTO` | Remoto (día asignado por RR.HH.) | Gris | `#A6A6A6` | Sí | No | No |
| `TRABAJO_FUERA` | Trabajo fuera de instalaciones | Morado | `#B292BE` | Sí | No | No |
| `BAJA_PERMISO` | Baja / Permiso | Verde | `#C5E0B4` | Sí | No | No |
| `PUNTUAL` | Asistencia normal | Sin color | `#FFFFFF` | — | No | No |
| `ATRASO` | Atraso (> 08:10) | Naranja | `#ED7D31` | No | **Sí** | No |
| `INCOMPLETO` | Incompleto (falta una marcación) | Rosado | `#FFCCCF` | No | No | No |
| `SIN_MARCAR` | Sin marcar | Rojo | `#FF0000` | No | **Sí** | No |
| `NO_APLICA` | No aplica (antes del ingreso / después de la baja) | Gris claro | `#E7E6E6` | — | No | No |

### Estructura propuesta

```php
Schema::create('tipos_asistencia', function (Blueprint $table) {
    $table->id();
    $table->string('codigo', 30)->unique();
    $table->string('nombre', 80);
    $table->char('color_hex', 7);
    $table->boolean('es_justificado')->default(false); // no genera atraso ni descuento
    $table->boolean('es_sancionable')->default(false); // suma al contador de la multa
    $table->boolean('seleccionable_manual')->default(true); // aparece en el selector de la matriz
    $table->unsignedSmallInteger('orden')->default(0);
    $table->boolean('estado')->default(true);
    $table->timestamps();
});
```

- Se siembra con un `TipoAsistenciaSeeder` a partir de la tabla de arriba.
- `asistencias` recibe `id_tipo_asistencia` (FK). Los códigos actuales de `tipos.estado_asistencia` se mapean así: `puntual → PUNTUAL`, `tarde → ATRASO`, `ausente → SIN_MARCAR`, `permiso → BAJA_PERMISO`, `vacaciones → VACACION`.
- Después de migrar se ajustan `LivewireHome` (el ausentismo usa el código `ausente`) y `PlanillaService` (usa `puntual`/`tarde`).

---

## 4. Reglas de negocio

### 4.1 Días que se evalúan

- Solo se evalúan los días hábiles, de lunes a viernes.
- **Nuevo ingreso:** se cuentan solo los días desde `empleados.fecha_ingreso` y hasta `fecha_baja`, si existe. Los días fuera de ese rango se muestran como `NO_APLICA` (gris claro, "—") y **no cuentan para nada**: ni sin marcar, ni atrasos, ni multa, ni días trabajados.
  - Caso real: *Karengonzales* (ID 55) no tiene ninguna marca del 3 al 21 de agosto. Hoy la macro pinta esos días de rojo. Con la regla nueva solo se evalúa desde su ingreso.
- Los empleados activos con ingreso dentro del periodo **siempre aparecen** en el reporte. La macro elimina hoy a quienes no tienen ninguna marca en el mes; esa exclusión se reemplaza por ordenarlos al final.

### 4.2 Clasificación de cada día (orden de prioridad)

Para cada empleado y cada día hábil se aplica la **primera** regla que se cumpla:

1. Si el día está fuera del rango ingreso–baja: `NO_APLICA`.
2. Si hay un tipo asignado a mano en la matriz por RR.HH.: ese tipo (queda registrado quién lo cambió).
3. Si la fecha está en la tabla `feriados` (activo, `aplica_todos = true`): `FERIADO`.
4. Si hay un `permisos_laborales` **aprobado** que cubre la fecha:
   - de tipo `vacaciones`: `VACACION`;
   - de otro tipo (enfermedad, personal, luto, maternidad/paternidad): `BAJA_PERMISO`.
5. Si RR.HH. asignó ese día como remoto a ese empleado (tabla `dias_remotos`): `REMOTO`.
6. Según las marcaciones del biométrico, primero limpiadas (ver §4.4):
   - ninguna marca: `SIN_MARCAR`;
   - una sola marca: `INCOMPLETO`;
   - dos marcas o más, con entrada después de la hora límite: `ATRASO`;
   - dos marcas o más, con entrada a tiempo: `PUNTUAL`.

**Días remotos:** no existe una modalidad híbrida ni una modalidad remota fija por empleado. RR.HH. asigna **día por día** las fechas en que cada empleado trabaja en remoto. Un día remoto asignado **no genera descuento por no marcar ni atraso**. Si ese día sí hay marcaciones, se muestran en la celda solo como información. Lo mismo aplica a los días cubiertos por un permiso aprobado (paso 4).

### 4.3 Atrasos y multa

- **El límite de tiempo se evalúa solo en la entrada.** La hora de salida no se evalúa: no hay sanción por salir temprano.
- **Hora límite de entrada:** la entrada es a las 08:00, con 10 minutos de tolerancia hasta las 08:10. Una entrada **después de las 08:10** ya es atraso: a las 08:10 no lo es, a las 08:11 sí. Estos valores se toman de `turnos.hora_entrada + tolerancia_minutos` del turno del empleado; si el empleado no tiene turno, se usa 08:00 + 10 minutos por defecto.
- **Minutos de atraso** = hora de entrada − 08:10.
- **Eventos sancionables del mes** = días `ATRASO` + días `SIN_MARCAR` (ninguna de las dos marcaciones en el día), contando solo los días evaluables.
- **`INCOMPLETO` no suma a la multa:** si el empleado hizo una de las dos marcaciones, no se sanciona.
- **Multa (Bs)** = `max(0, eventos_sancionables − 3) × 20`. Los 3 primeros eventos del mes se perdonan y desde el 4.º cada uno cuesta Bs 20.
- Los días justificados (vacación, feriado, remoto asignado, trabajo fuera, baja/permiso) nunca suman.
- El detalle guarda, para cada evento, su número dentro del mes y la multa de ese día: 0 para los tres primeros y 20 desde el cuarto.
- **La multa es aparte de la planilla.** Ni los atrasos ni los días sin marcar descuentan sueldo en la planilla. El módulo de asistencia solo calcula y reporta la multa, y no modifica `PlanillaService`.

Ejemplo con agosto 2026: *PaolaCalzadilla* tiene 6 atrasos y 1 día sin marcar (14/08), es decir 7 eventos, así que (7−3)×20 = **Bs 80**. Su día incompleto (12/08) no suma. La referencia actual daba Bs 60 porque no contaba el día sin marcar; esa diferencia es esperada con la regla nueva.

### 4.4 Limpieza de marcaciones (correcciones)

1. **Separar el texto** en bloques de 5 caracteres `HH:MM`.
2. **Quitar duplicados:** las marcas repetidas con 5 minutos o menos de diferencia se unen en una sola. Por ejemplo, `08:05 08:05 08:05` queda como una sola marca y el día pasa a `INCOMPLETO`; hoy la macro lo toma como día completo.
3. **Entrada** = primera marca; **salida** = última marca después de quitar duplicados. Solo la entrada se compara con la hora límite.
4. **Si hay una sola marca**, el día es `INCOMPLETO`: no suma a la multa ni registra minutos de atraso, sea una entrada o una salida.

   Hoy la macro toma una marca suelta de `16:37` como una entrada con **507 minutos de atraso**. Por eso *Raquelromero* aparece con 1020 minutos de atraso en la referencia.

---

## 5. Correcciones detectadas en la macro actual

| # | Problema | Corrección en el sistema |
|---|---|---|
| 1 | Lee `Worksheets(1)`, pero en `1_report.xls` la hoja de marcaciones es la 3.ª (`Reporte de Asistencia`), así que falla al leer el periodo | Buscar la hoja por nombre |
| 2 | No considera la fecha de ingreso: los días previos salen en rojo | Tipo `NO_APLICA` (§4.1) |
| 3 | Elimina del reporte a los empleados sin ninguna marca | Mostrarlos al final |
| 4 | Una marca suelta de salida (p. ej. `16:37`) se cuenta como atraso de cientos de minutos | §4.4 punto 4 |
| 5 | Las marcas duplicadas (`08:0508:0508:05`) se toman como día completo | §4.4 punto 2 |
| 6 | Feriados escritos en el código, solo para 2026 | Usar la tabla `feriados` |
| 7 | Hora límite 08:10 fija en el código | Usar `turnos` (hora de entrada + tolerancia) |
| 8 | Remoto y vacaciones se marcan pintando celdas a mano | Vacaciones y bajas salen de los permisos aprobados; remoto sale de los días asignados por RR.HH. (`dias_remotos`); los demás casos, del selector en la matriz |
| 9 | Una marca suelta de entrada tardía cuenta como atraso y suma a la multa | `INCOMPLETO` nunca suma a la multa |
| 10 | `EscribirIncompletos` existe pero nunca se llama | Detalle de incompletos y sin marcar en el sistema |
| 11 | El comentario dice "morado remoto" pero el código usa gris | Los colores quedan definidos en `tipos_asistencia` |
| 12 | Los días sin ninguna marcación no suman a la multa | `SIN_MARCAR` suma a la multa (§4.3) |

### Errores existentes en la app que conviene corregir en el mismo trabajo

- `LivewirePermisosLaborales` (líneas 56-57) y `LivewireSaldosVacaciones` (líneas 52-53) buscan en `personas.nombres` / `apellidos`, columnas que no existen (las reales son `primer_nombre`, `primer_apellido`, etc.). La búsqueda por nombre da error SQL.
- `PlanillaService` (líneas 55-62): si un empleado no tiene asistencias, le cuenta **todos** los días del periodo como trabajados. No se cambia en este trabajo, porque la asistencia no descuenta en la planilla (§4.3). Solo se ajusta el mapeo de códigos cuando `asistencias` pase a usar `tipos_asistencia` (§3).
- `LivewirePermisosLaborales` (línea 90): `dias_solicitados` cuenta días calendario e incluye fines de semana y feriados.

---

## 6. Cambios en la base de datos

| Tabla | Cambio |
|---|---|
| `tipos_asistencia` | **Nueva** (§3) |
| `empleados` | + `codigo_biometrico` varchar(20) unique nullable (el ID del reloj: 2, 10, 21…) |
| `dias_remotos` | **Nueva**: `id_empleado`, `fecha` date, `observacion` nullable, `id_asignado_por` (users), `estado`, timestamps; unique(`id_empleado`, `fecha`). Cada fila es un día remoto asignado por RR.HH. a un empleado |
| `importaciones_biometrico` | **Nueva**: `nombre_archivo`, `periodo_inicio`, `periodo_fin`, `id_usuario`, `total_empleados`, `total_no_vinculados`, `estado`, timestamps |
| `asistencias` | + `id_tipo_asistencia` (FK); + `marcaciones` json (marcas limpias); + `marcaciones_crudas` varchar; + `minutos_atraso` smallint default 0; + `es_sancionable` bool; + `numero_evento_mes` smallint nullable; + `multa_dia` decimal(8,2) default 0; + `tipo_asignado_manual` bool; + `id_usuario_modifico` nullable; + `id_importacion` nullable; + `origen` enum(`biometrico`, `manual`) |

Se mantiene el `unique(id_empleado, fecha)` de `asistencias`: si se reimporta el mismo periodo, se actualizan las filas existentes (upsert) **sin pisar** los tipos asignados a mano (`tipo_asignado_manual = true`).

---

## 7. Componentes a construir

| Pieza | Ruta propuesta | Responsabilidad |
|---|---|---|
| Lector del archivo | `app/Services/Asistencia/LectorReporteBiometrico.php` | Abrir el `.xls` con PhpSpreadsheet, ubicar la hoja `Reporte de Asistencia`, leer el periodo y los bloques por empleado |
| Clasificador | `app/Services/Asistencia/ClasificadorAsistencia.php` | Aplicar §4.2 y §4.4 a un día concreto (función pura y testeable) |
| Servicio de asistencia | `app/Services/Asistencia/AsistenciaService.php` | Orquestar la importación, vincular por `codigo_biometrico`, hacer el upsert y calcular los totales y la multa (§4.3) |
| Pantalla de importación | `app/Livewire/LivewireImportarBiometrico.php` | Subir el archivo, previsualizar, listar los IDs del reloj **sin vincular** para asignarlos a un empleado, y confirmar |
| Matriz coloreada | `app/Livewire/LivewireMatrizAsistencia.php` | Grilla de empleados por días con leyenda, filtros por periodo, departamento y empleado, y clic en una celda para cambiar el tipo (reemplaza "Actualizar colores") |
| Resumen y detalle | Pestañas en la misma matriz | Resumen (atrasos, minutos, incompletos, sin marcar, eventos sancionables, multa) y detalle de eventos con subtotal |
| Exportación | `app/Exports/MatrizAsistenciaExport.php` | Excel con los mismos colores (Maatwebsite + estilos) |
| Catálogo | CRUD simple de `tipos_asistencia` | Permite cambiar nombre y color; los códigos base no se pueden borrar |
| Días remotos | `app/Livewire/LivewireDiasRemotos.php` | RR.HH. elige un empleado y una o varias fechas y las asigna como remoto; también puede ver y anular las asignaciones. Una asignación hecha después de importar reclasifica ese día en la matriz |
| Empleado | Formulario actual | Campo de código biométrico |

Permisos nuevos en `RolesAndPermissionsSeeder`: `Importar Asistencia`, `Editar Matriz Asistencia` y `Asignar Dias Remotos`.

---

## 8. Casos de prueba (con datos reales de agosto 2026)

| Caso | Entrada | Resultado esperado |
|---|---|---|
| Atraso normal | Paola, 10/08 `08:3416:41` | `ATRASO`, 24 min |
| Límite exacto | Paola, 28/08 `08:1016:40` | `PUNTUAL` |
| Una marca de entrada | Paola, 12/08 `08:06` | `INCOMPLETO`, 0 min, no sancionable |
| Una marca de salida | Raquel, 19/08 `16:37` | `INCOMPLETO`, **0 min** (hoy la macro da 507), no sancionable |
| Marcas duplicadas | Armandina, 05/08 `08:0508:0508:05` | `INCOMPLETO` |
| Sin marcar | Paola, 14/08 vacío | `SIN_MARCAR`, sancionable |
| Feriado | 06/08 y 07/08 (tabla `feriados`) | `FERIADO` para todos |
| Nuevo ingreso | Karen (ID 55), días previos a su `fecha_ingreso` | `NO_APLICA`, fuera de todos los totales |
| Remoto asignado | RR.HH. asigna el 13/08 como remoto a un empleado que ese día no marcó | `REMOTO`, sin descuento ni multa |
| Remoto no asignado | El mismo empleado no marca el 14/08 y ese día no tiene asignación | `SIN_MARCAR`, sancionable |
| Multa total | Paola: 6 atrasos + 1 sin marcar + 1 incompleto | 7 eventos, Bs 80 |
| Multa | 3 eventos | Bs 0 |
| Multa | 4 eventos | Bs 20 |
| Multa | 6 eventos | Bs 60 |
| Reimportación | Se importa el mismo periodo dos veces y hay un día cambiado a mano a `VACACION` | Se conserva `VACACION` |

---

## 9. Decisiones de RR.HH.

- "Sin marcar" (ninguna de las dos marcaciones del día) **suma** a la multa.
- "Incompleto" (una sola marcación) **no suma** a la multa.
- No existe el tipo híbrido. RR.HH. asigna los días remotos día por día a cada empleado, y esos días no generan descuento por no marcar.
- La multa es aparte: **no hay ningún descuento en la planilla** por atrasos ni por días sin marcar.
- El límite de tiempo se aplica **solo a la hora de entrada**: 08:00 con tolerancia hasta las 08:10. Después de las 08:10 es atraso. La salida no se evalúa.

---

## 10. Implementación (estado: construido)

| Pieza | Archivo |
|---|---|
| Migración | `database/migrations/2026_09_29_000001_create_modulo_asistencia_biometrico.php` |
| Reglas configurables (horario por defecto, tolerancia, duplicados, multa) | `config/asistencia.php` |
| Lector, clasificador y servicio | `app/Services/Asistencia/` |
| Pantallas | Asistencia → Matriz de Asistencia, Importar Biométrico, Días Remotos, Tipos de Asistencia |
| Excel | `app/Exports/MatrizAsistenciaExport.php` (hojas Matriz, Resumen, Detalle de eventos, Sin marcar e incompletos) |
| Feriados 2026 que la macro tenía escritos en el código | `database/seeders/FeriadosBolivia2026Seeder.php` |

Decisiones tomadas al implementar:

- La matriz muestra a los empleados **vinculados a un ID del reloj** en la importación. Un empleado que no aparece en el reloj no se evalúa.
- Un empleado **inactivo** solo se evalúa si marcó algún día del periodo.
- Al importar, los IDs del reloj se vinculan con empleados por nombre ("PaolaCalzadilla" → Paola Mercedes Calzadilla Vidal). El vínculo lo confirma RR.HH. y queda guardado en `empleados.codigo_biometrico`.
- Aprobar un permiso, cambiar un feriado o asignar un día remoto reclasifica los días afectados y recalcula la multa automáticamente. El botón «Recalcular» de la matriz hace lo mismo para todo el mes.
- En la matriz, elegir «Remoto» registra el día en `dias_remotos`; elegir «Automático» vuelve a la clasificación por reglas.
- `PlanillaService` solo cuenta los marcajes manuales. La asistencia del biométrico no cambia los días trabajados de la planilla, para cumplir que no haya descuento (§4.3).
