# Sistema de Gestión de Recursos Humanos (RRHH)

Sistema para gestión de empleados, cargos, asistencia, planillas de pago, contratos, documentos, vacaciones, horas extra, liquidaciones, evaluaciones, notificaciones, anticipos y reglamentos internos.

## 1. Stack Tecnológico

| Tecnología | Uso |
|---|---|
| **Laravel 11.x** | Backend / Framework principal |
| **Livewire 3.x** | Componentes dinámicos sin recargar página (SPA-like) |
| **Tailwind CSS 3.x** | Estilos y diseño responsivo |
| **Spatie laravel-permission** | Roles y permisos (admin, RRHH, gerente, empleado, etc.) |
| **Spatie laravel-activitylog** | Auditoría de cambios (quién modificó qué y cuándo) |
| **Spatie laravel-medialibrary** | Gestión de archivos (DPI, contratos, certificados, reglamentos en PDF) |
| **Spatie laravel-backup** | Respaldo automático de la base de datos |
| **MySQL / PostgreSQL** | Base de datos |
| **Livewire + Alpine.js** | Interactividad ligera en el front |
| **Laravel Excel (Maatwebsite)** | Exportar reportes y planillas a Excel |
| **DomPDF o Spatie Browsershot** | Exportar boletas de pago y reportes a PDF |
| **Laravel Scheduler (cron)** | Revisar diariamente contratos/documentos por vencer y generar notificaciones |
| **Laravel Notifications** | Notificaciones internas y por correo |
| **Laravel Sanctum** *(opcional)* | Si se necesita API para app móvil de marcaje |
| **Pest / PHPUnit** | Pruebas automatizadas |

---

## 2. Estructura de Carpetas (Laravel + Livewire)

```
app/
├── Livewire/
│   ├── Personas/
│   ├── Empleados/
│   ├── Cargos/
│   ├── Contratos/
│   ├── Documentos/
│   ├── Asistencia/
│   │   ├── Marcaje.php
│   │   └── ReporteAsistencia.php
│   ├── Vacaciones/
│   ├── HorasExtra/
│   ├── Planillas/
│   │   ├── Generar.php
│   │   ├── Detalle.php
│   │   └── Boleta.php
│   ├── Liquidaciones/
│   ├── Evaluaciones/
│   ├── Notificaciones/
│   ├── Anticipos/
│   ├── Reglamentos/
│   ├── Reportes/
│   ├── Gestiones/                // gestión de años/periodos y catálogo de meses
│   ├── Tipos/
│   └── Configuracion/
├── Models/
│   ├── Persona.php
│   ├── Empleado.php
│   ├── User.php
│   ├── Cargo.php
│   ├── Departamento.php
│   ├── Contrato.php
│   ├── DocumentoEmpleado.php
│   ├── Asistencia.php
│   ├── SaldoVacacion.php
│   ├── HoraExtraDetalle.php
│   ├── Planilla.php
│   ├── PlanillaDetalle.php
│   ├── ConceptoPago.php
│   ├── PlanillaDetalleConcepto.php
│   ├── Liquidacion.php
│   ├── EvaluacionDesempeno.php
│   ├── Notificacion.php
│   ├── AnticipoPrestamo.php
│   ├── AnticipoPago.php
│   ├── Reglamento.php
│   ├── ReglamentoLectura.php
│   ├── PermisoLaboral.php
│   ├── HistorialCargo.php
│   ├── Gestion.php                // año/periodo fiscal
│   ├── Mes.php                    // catálogo fijo de 12 meses
│   ├── Tipo.php
│   └── Feriado.php
├── Services/
│   ├── PlanillaService.php
│   ├── AsistenciaService.php
│   ├── VacacionService.php
│   ├── LiquidacionService.php
│   ├── NotificacionService.php
│   └── ReporteService.php
├── Console/Commands/
│   └── RevisarVencimientos.php
├── Policies/
└── Exports/

database/
├── migrations/
└── seeders/
    ├── TipoSeeder.php
    ├── GestionSeeder.php          // crea la gestión (año) actual y futuras
    └── MesSeeder.php              // carga los 12 meses una sola vez

resources/
├── views/
│   └── livewire/...
```

---

## 3. Convenciones generales de base de datos

Todas las tablas del sistema (salvo las propias de paquetes como Spatie) incluyen como mínimo `estado` (boolean), `created_at` y `updated_at`.

### 3.1 Convención de nombres para llaves foráneas (FK)

**Toda llave foránea empieza con el prefijo `id_`** (nunca `algo_id`). La llave primaria de cada tabla sigue siendo `id`.

### 3.2 Regla para campos de "tipo" con más de 3 opciones

Si un campo de clasificación tiene 3 opciones o menos, se deja como `enum`. Si tiene más de 3, se usa la tabla catálogo `tipos`, referenciada con una FK `id_algo`.

**Categorías (`categoria`) usadas en el catálogo `tipos`:**

| Categoría | Ejemplos de valores |
|---|---|
| `tipo_documento` | dpi, pasaporte, licencia, partida_nacimiento |
| `estado_empleado` | activo, inactivo, suspendido, baja |
| `tipo_permiso` | vacaciones, enfermedad, personal, luto, maternidad_paternidad |
| `tipo_planilla` | quincenal, mensual, aguinaldo, bono14 |
| `estado_planilla` | borrador, revisada, aprobada, pagada |
| `estado_asistencia` | puntual, tarde, ausente, permiso, vacaciones |
| `tipo_contrato` | indefinido, temporal, por_obra, practicante, medio_tiempo |
| `tipo_documento_empleado` | licencia_conducir, certificado_medico, antecedentes_penales, titulo_academico, carnet_salud |
| `tipo_hora_extra` | diurna (valor_referencia 1.25), nocturna (1.50), dia_feriado (2.00) |
| `tipo_liquidacion` | renuncia, despido_justificado, despido_injustificado, mutuo_acuerdo |
| `resultado_evaluacion` | excelente, bueno, regular, deficiente |
| `tipo_notificacion` | contrato_por_vencer, documento_por_vencer, reglamento_nuevo, planilla_lista, permiso_solicitado, permiso_aprobado |
| `tipo_anticipo` | anticipo_sueldo, prestamo_personal, prestamo_estudio, otro |

### 3.3 Tablas de Gestión (año) y Mes — catálogos de periodo reutilizables

Para no repetir strings de periodo (`"2026-08"`) ni años sueltos en cada tabla, se manejan dos tablas catálogo independientes de `tipos` (porque su naturaleza es distinta: son periodos de tiempo, no clasificaciones), y se referencian como FK en cualquier tabla que necesite agrupar información por año o por mes.

**`gestiones`** (año o periodo fiscal)
| Campo | Tipo | Nota |
|---|---|---|
| id | bigint | |
| anio | int unique | ej. 2026 |
| fecha_inicio | date | normalmente 01/01 |
| fecha_fin | date | normalmente 31/12 |
| estado | boolean | permite "cerrar" una gestión pasada |
| created_at / updated_at | | |

**`meses`** (catálogo fijo, se carga una sola vez con 12 filas)
| Campo | Tipo | Nota |
|---|---|---|
| id | bigint | |
| numero | int | 1 al 12 |
| nombre | string | Enero, Febrero, ... Diciembre |
| abreviatura | string nullable | Ene, Feb, ... |
| estado | boolean | |
| created_at / updated_at | | |

**Tablas que usan `id_gestion` / `id_mes` como FK:**

| Tabla | Campos agregados | Para qué |
|---|---|---|
| `planillas` | `id_gestion`, `id_mes` (nullable) | reemplaza el string `periodo`; `id_mes` queda `null` en planillas anuales como aguinaldo/bono14 |
| `saldos_vacaciones` | `id_gestion` | reemplaza el campo suelto `anio` |
| `evaluaciones_desempeno` | `id_gestion` | reemplaza el string `periodo_evaluado` (el semestre se maneja con un enum aparte) |
| `feriados` | `id_gestion` | el listado de feriados se administra por año |
| `asistencias` | `id_gestion`, `id_mes` | acelera reportes mensuales/anuales sin usar funciones de fecha en cada consulta |
| `horas_extra_detalle` | `id_gestion`, `id_mes` | igual que asistencias, para agrupar el costo de horas extra por periodo |

> En todas estas tablas el campo `fecha` (o `fecha_inicio`/`fecha_fin`) sigue existiendo para el detalle exacto del día; `id_gestion`/`id_mes` son un apoyo para reportes y filtros rápidos, no un reemplazo de la fecha real.

---

## 4. Diseño de Base de Datos

### 4.1 Módulo de Personas (base de Empleados y Usuarios)

**`personas`**: id, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, id_tipo_documento *(FK tipos)*, numero_documento, nit, fecha_nacimiento, genero (enum), direccion, telefono, email, foto (media).

**Relaciones:** `empleados.id_persona`, `users.id_persona` (nullable).

---

### 4.2 Módulo de Empleados y Cargos

**`departamentos`**: id, nombre, descripcion.

**`cargos`**: id, id_departamento, nombre, sueldo_base, descripcion_funciones.

**`empleados`**: id, id_persona, codigo_empleado, id_cargo, id_departamento, fecha_ingreso, fecha_baja, sueldo_actual, id_estado_empleado *(FK tipos)*, numero_cuenta_bancaria, banco, soft deletes.

**`historial_cargos`**: id, id_empleado, id_cargo_anterior, id_cargo_nuevo, sueldo_anterior, sueldo_nuevo, fecha_cambio, motivo.

---

### 4.3 Módulo de Contratos

**`contratos`**: id, id_empleado, id_tipo_contrato *(FK tipos)*, fecha_inicio, fecha_fin nullable, documento (media), notificado_vencimiento, observaciones.

---

### 4.4 Módulo de Documentos de Empleado

**`documentos_empleado`**: id, id_empleado, id_tipo_documento_empleado *(FK tipos)*, numero_documento, fecha_emision, fecha_vencimiento, archivo (media), notificado_vencimiento.

---

### 4.5 Módulo de Asistencia (Entrada / Salida)

**`turnos`**: id, nombre, hora_entrada, hora_salida, tolerancia_minutos.

**`empleado_turno`** *(pivote)*: id_empleado, id_turno.

**`asistencias`**
| Campo | Tipo | Nota |
|---|---|---|
| id | bigint | |
| id_empleado | FK → `empleados` | |
| **id_gestion** | FK → `gestiones` | |
| **id_mes** | FK → `meses` | |
| fecha | date | fecha exacta del marcaje |
| hora_entrada | datetime nullable | |
| hora_salida | datetime nullable | |
| hora_entrada_marcada_manual | boolean | |
| latitud / longitud | decimal nullable | |
| id_estado_asistencia | FK → `tipos` | puntual, tarde, ausente, permiso, vacaciones |
| horas_trabajadas | decimal | |
| horas_extra | decimal | |
| observaciones | text nullable | |

**`feriados`**: id, id_gestion *(FK gestiones)*, fecha, descripcion, aplica_todos.

**`permisos_laborales`**: id, id_empleado, id_tipo_permiso *(FK tipos)*, fecha_inicio, fecha_fin, dias_solicitados, goce_sueldo, estado_solicitud (enum), id_aprobado_por, documento_soporte (media).

---

### 4.6 Módulo de Vacaciones

**`saldos_vacaciones`**
| Campo | Tipo | Nota |
|---|---|---|
| id | bigint | |
| id_empleado | FK → `empleados` | |
| **id_gestion** | FK → `gestiones` | reemplaza el campo suelto `anio` |
| dias_generados | decimal(5,2) | |
| dias_tomados | decimal(5,2) | |
| dias_disponibles | decimal(5,2) | |
| fecha_ultimo_calculo | date | |

---

### 4.7 Módulo de Horas Extra

**`horas_extra_detalle`**
| Campo | Tipo | Nota |
|---|---|---|
| id | bigint | |
| id_empleado | FK → `empleados` | |
| id_asistencia | FK nullable → `asistencias` | |
| **id_gestion** | FK → `gestiones` | |
| **id_mes** | FK → `meses` | |
| fecha | date | |
| id_tipo_hora_extra | FK → `tipos` | diurna, nocturna, dia_feriado |
| horas | decimal(5,2) | |
| valor_hora_base | decimal(10,2) | |
| monto_calculado | decimal(10,2) | |
| id_planilla_detalle | FK nullable → `planilla_detalles` | |

---

### 4.8 Módulo de Planillas (Pagos)

**`conceptos_pago`**: id, nombre, tipo_concepto (enum), es_fijo, valor_por_defecto, es_porcentaje.

**`planillas`**
| Campo | Tipo | Nota |
|---|---|---|
| id | bigint | |
| **id_gestion** | FK → `gestiones` | reemplaza el string `periodo` |
| **id_mes** | FK nullable → `meses` | `null` si es una planilla anual (aguinaldo/bono14) |
| fecha_inicio | date | útil para distinguir primera/segunda quincena |
| fecha_fin | date | |
| id_tipo_planilla | FK → `tipos` | quincenal, mensual, aguinaldo, bono14 |
| id_estado_planilla | FK → `tipos` | borrador, revisada, aprobada, pagada |
| total_planilla | decimal | |
| id_generado_por | FK → `users` | |
| id_aprobado_por | FK nullable → `users` | |

**`planilla_detalles`**: id, id_planilla, id_empleado, id_cargo, sueldo_base, dias_trabajados, horas_extra, total_ingresos, total_deducciones, sueldo_liquido, total_pagado, estado_pago (enum), fecha_pago, metodo_pago (enum).

**`planilla_detalle_conceptos`**: id, id_planilla_detalle, id_concepto_pago, monto, observacion.

---

### 4.9 Módulo de Liquidaciones e Indemnización

**`liquidaciones`**: id, id_empleado, id_tipo_liquidacion *(FK tipos)*, fecha_liquidacion, tiempo_laborado_anios, salario_promedio, monto_indemnizacion, monto_vacaciones_pendientes, monto_aguinaldo_proporcional, monto_bono14_proporcional, total_liquidacion, observaciones.

---

### 4.10 Módulo de Evaluaciones de Desempeño

**`evaluaciones_desempeno`**
| Campo | Tipo | Nota |
|---|---|---|
| id | bigint | |
| id_empleado | FK → `empleados` | |
| id_evaluador | FK → `users` | |
| **id_gestion** | FK → `gestiones` | reemplaza el string `periodo_evaluado` |
| semestre | enum | primero, segundo *(2 opciones → se deja como enum)* |
| fecha_evaluacion | date | |
| puntuacion_total | decimal(5,2) | |
| id_resultado_evaluacion | FK → `tipos` | excelente, bueno, regular, deficiente |
| comentarios | text nullable | |
| plan_mejora | text nullable | |

**`evaluacion_detalles`** *(opcional)*: id, id_evaluacion, criterio, puntuacion.

---

### 4.11 Módulo de Reglamentos

**`reglamentos`**: id, titulo, contenido, version, vigente_desde.

**`reglamento_lecturas`**: id, id_reglamento, id_empleado, fecha_lectura, ip_confirmacion.

---

### 4.12 Módulo de Notificaciones

**`notificaciones`**: id, id_usuario_destino *(FK users)*, id_tipo_notificacion *(FK tipos)*, titulo, mensaje, leido, fecha_leido, referencia_tipo, referencia_id.

---

### 4.13 Módulo de Anticipos y Préstamos

**`anticipos_prestamos`**: id, id_empleado, id_tipo_anticipo *(FK tipos)*, monto_total, numero_cuotas, monto_cuota, saldo_pendiente, fecha_otorgado, estado_anticipo (enum), observaciones.

**`anticipo_pagos`**: id, id_anticipo, id_planilla_detalle, monto_pagado, fecha_pago.

---

### 4.14 Roles y Permisos (Spatie laravel-permission)

**`users`**: id, id_persona *(FK nullable → personas)*, email, password.

**Roles sugeridos:** Super Admin, RRHH, Gerente/Supervisor, Empleado.

**Permisos sugeridos:** `empleados.*`, `contratos.*`, `documentos.*`, `planillas.generar`, `planillas.aprobar`, `asistencia.marcar`, `vacaciones.aprobar`, `liquidaciones.generar`, `evaluaciones.gestionar`, `notificaciones.ver`, `anticipos.gestionar`, `reglamentos.gestionar`, `tipos.gestionar`, `personas.gestionar`, `gestiones.gestionar`.

---

### 4.15 Auditoría (Spatie laravel-activitylog)

Tabla `activity_log` automática — registra creación/edición/eliminación en todos los módulos anteriores.

---

## 5. Módulos del Sistema (por secciones)

1. **Personas** — datos generales, base de empleados y usuarios.
2. **Empleados y Cargos** — CRUD, departamentos, cargos, historial de cargos.
3. **Contratos** — alta, renovación y alertas de vencimiento.
4. **Documentos de Empleado** — carga y alertas de vencimiento.
5. **Asistencia** — marcaje, turnos, feriados, permisos/vacaciones (solicitud), enlazado a `gestiones`/`meses`.
6. **Vacaciones** — saldo por empleado y `gestion` (año).
7. **Horas Extra** — registro diferenciado por porcentaje, enlazado a `gestiones`/`meses`.
8. **Planillas y Pagos** — generación por `gestion`/`mes`, sueldo líquido, boleta en PDF, aguinaldo/bono14.
9. **Liquidaciones e Indemnización** — cálculo al finalizar la relación laboral.
10. **Evaluaciones de Desempeño** — histórico por `gestion` y semestre.
11. **Reglamentos Internos** — publicación y control de lectura/aceptación.
12. **Notificaciones** — alertas de contratos, documentos, planillas y permisos.
13. **Anticipos y Préstamos** — otorgamiento y descuento automático en planilla.
14. **Reportes** — filtrables por `gestion`/`mes` en lugar de rangos de fecha manuales.
15. **Catálogo de Tipos** — administración centralizada de todas las categorías.
16. **Gestiones y Meses** — administración de años/periodos fiscales y catálogo de meses.
17. **Roles, Permisos y Seguridad** — Spatie + bitácora de auditoría.
18. **Dashboard General** — empleados activos, nómina del mes, ausentismo, contratos/documentos por vencer, anticipos activos.
19. **Configuración General** — datos de la empresa, respaldo (Spatie Backup) y exportación completa de historial.

---

## 6. Puntos aún abiertos para decidir contigo

1. **Multi-sucursal** — tabla `sucursales` + `id_sucursal` en `empleados`, si aplica.
2. **Control de acceso a nivel de datos** — Policies por departamento, si un gerente debe ver solo el suyo.
3. **Frecuencia y canal de notificaciones** — ¿solo dentro del sistema, o también correo/WhatsApp?
4. **Fórmula exacta de indemnización y % de horas extra** — dependen de la legislación laboral de tu país; los campos y el catálogo (`valor_referencia` en `tipos`) ya están listos, solo falta la fórmula final.

---

## 7. Flujo recomendado de implementación

1. Autenticación + Roles y permisos (Spatie)
2. Tabla `tipos` + seeder inicial
3. Tablas `gestiones` y `meses` + seeders
4. Tabla `personas`
5. Departamentos, Cargos y Empleados
6. Contratos y Documentos de Empleado
7. Asistencia + Vacaciones + Horas Extra (ya enlazados a `gestiones`/`meses`)
8. Conceptos de pago + Planillas
9. Anticipos y Préstamos
10. Liquidaciones
11. Evaluaciones de Desempeño
12. Reglamentos + Notificaciones
13. Reportes + Dashboard
14. Ajustes finales de configuración y respaldo

Este orden respeta las dependencias: `gestiones` y `meses` deben existir **antes** de crear cualquier planilla, asistencia, saldo de vacaciones u horas extra, porque todas esas tablas ya los usan como FK.
