<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('login page loads', function () {
    $this->get('/login')->assertOk();
});

test('admin can log in and reach home', function () {
    $admin = User::where('email', 'admin@rrhh.local')->first();

    $this->actingAs($admin)
        ->get('/')
        ->assertOk();
});

test('admin can access tipos module', function () {
    $admin = User::where('email', 'admin@rrhh.local')->first();

    $this->actingAs($admin)
        ->get('/tipos')
        ->assertOk()
        ->assertSee('tipo_documento');
});

test('admin can access gestiones module', function () {
    $admin = User::where('email', 'admin@rrhh.local')->first();

    $this->actingAs($admin)
        ->get('/gestiones')
        ->assertOk();
});

test('admin can access personas module and create a persona', function () {
    $admin = User::where('email', 'admin@rrhh.local')->first();
    $tipoDocumento = \App\Models\Tipo::where('categoria', 'tipo_documento')->first();

    $this->actingAs($admin)
        ->get('/personas')
        ->assertOk();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewirePersonas::class)
        ->set('form.primer_nombre', 'Juan')
        ->set('form.primer_apellido', 'Pérez')
        ->set('form.id_tipo_documento', $tipoDocumento->id)
        ->set('form.numero_documento', '1234567890101')
        ->set('form.genero', 'masculino')
        ->call('save')
        ->assertHasNoErrors();

    expect(\App\Models\Persona::where('numero_documento', '1234567890101')->exists())->toBeTrue();
});

test('admin can manage departamentos, cargos and empleados end to end', function () {
    $admin = User::where('email', 'admin@rrhh.local')->first();

    $this->actingAs($admin)->get('/departamentos')->assertOk();
    $this->actingAs($admin)->get('/cargos')->assertOk();
    $this->actingAs($admin)->get('/empleados')->assertOk();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewireDepartamentos::class)
        ->set('form.nombre', 'Tecnología')
        ->call('save')
        ->assertHasNoErrors();

    $departamento = \App\Models\Departamento::where('nombre', 'Tecnología')->firstOrFail();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewireCargos::class)
        ->set('form.id_departamento', $departamento->id)
        ->set('form.nombre', 'Desarrollador')
        ->set('form.sueldo_base', 6000)
        ->call('save')
        ->assertHasNoErrors();

    $cargo = \App\Models\Cargo::where('nombre', 'Desarrollador')->firstOrFail();

    $tipoDocumento = \App\Models\Tipo::where('categoria', 'tipo_documento')->first();
    $persona = \App\Models\Persona::create([
        'primer_nombre' => 'Ana',
        'primer_apellido' => 'García',
        'id_tipo_documento' => $tipoDocumento->id,
        'numero_documento' => '9998887776',
        'genero' => 'femenino',
        'estado' => true,
    ]);

    $estadoActivo = \App\Models\Tipo::where('categoria', 'estado_empleado')->where('codigo', 'activo')->first();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewireEmpleados::class)
        ->set('form.id_persona', $persona->id)
        ->set('form.codigo_empleado', 'EMP-TEST-1')
        ->set('form.id_cargo', $cargo->id)
        ->set('form.id_departamento', $departamento->id)
        ->set('form.fecha_ingreso', '2026-01-01')
        ->set('form.sueldo_actual', 6000)
        ->set('form.id_estado_empleado', $estadoActivo->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(\App\Models\Empleado::where('codigo_empleado', 'EMP-TEST-1')->exists())->toBeTrue();
});

test('admin can create a contrato and a documento for an empleado', function () {
    $admin = User::where('email', 'admin@rrhh.local')->first();

    $this->actingAs($admin)->get('/contratos')->assertOk();
    $this->actingAs($admin)->get('/documentos')->assertOk();

    $departamento = \App\Models\Departamento::create(['nombre' => 'Operaciones', 'estado' => true]);
    $cargo = \App\Models\Cargo::create([
        'id_departamento' => $departamento->id,
        'nombre' => 'Operario',
        'sueldo_base' => 4500,
        'estado' => true,
    ]);
    $tipoDocumento = \App\Models\Tipo::where('categoria', 'tipo_documento')->first();
    $persona = \App\Models\Persona::create([
        'primer_nombre' => 'Carlos',
        'primer_apellido' => 'López',
        'id_tipo_documento' => $tipoDocumento->id,
        'numero_documento' => '5554443332',
        'genero' => 'masculino',
        'estado' => true,
    ]);
    $estadoActivo = \App\Models\Tipo::where('categoria', 'estado_empleado')->where('codigo', 'activo')->first();
    $empleado = \App\Models\Empleado::create([
        'id_persona' => $persona->id,
        'codigo_empleado' => 'EMP-TEST-2',
        'id_cargo' => $cargo->id,
        'id_departamento' => $departamento->id,
        'fecha_ingreso' => '2026-01-01',
        'sueldo_actual' => 4500,
        'id_estado_empleado' => $estadoActivo->id,
        'estado' => true,
    ]);

    $tipoContrato = \App\Models\Tipo::where('categoria', 'tipo_contrato')->first();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewireContratos::class)
        ->set('form.id_empleado', $empleado->id)
        ->set('form.id_tipo_contrato', $tipoContrato->id)
        ->set('form.fecha_inicio', '2026-01-01')
        ->call('save')
        ->assertHasNoErrors();

    expect(\App\Models\Contrato::where('id_empleado', $empleado->id)->exists())->toBeTrue();

    $tipoDocEmpleado = \App\Models\Tipo::where('categoria', 'tipo_documento_empleado')->first();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewireDocumentos::class)
        ->set('form.id_empleado', $empleado->id)
        ->set('form.id_tipo_documento_empleado', $tipoDocEmpleado->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(\App\Models\DocumentoEmpleado::where('id_empleado', $empleado->id)->exists())->toBeTrue();
});

test('admin can manage turnos, feriados, asistencia and permisos laborales', function () {
    $admin = User::where('email', 'admin@rrhh.local')->first();

    $this->actingAs($admin)->get('/turnos')->assertOk();
    $this->actingAs($admin)->get('/feriados')->assertOk();
    $this->actingAs($admin)->get('/asistencia')->assertOk();
    $this->actingAs($admin)->get('/permisos-laborales')->assertOk();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewireTurnos::class)
        ->set('form.nombre', 'Turno Mañana')
        ->set('form.hora_entrada', '08:00')
        ->set('form.hora_salida', '17:00')
        ->call('save')
        ->assertHasNoErrors();

    expect(\App\Models\Turno::where('nombre', 'Turno Mañana')->exists())->toBeTrue();

    $gestionActual = \App\Models\Gestion::where('anio', now()->year)->first();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewireFeriados::class)
        ->set('form.id_gestion', $gestionActual->id)
        ->set('form.fecha', now()->addDays(10)->format('Y-m-d'))
        ->set('form.descripcion', 'Día de prueba')
        ->call('save')
        ->assertHasNoErrors();

    expect(\App\Models\Feriado::where('descripcion', 'Día de prueba')->exists())->toBeTrue();

    $departamento = \App\Models\Departamento::create(['nombre' => 'Ventas', 'estado' => true]);
    $cargo = \App\Models\Cargo::create(['id_departamento' => $departamento->id, 'nombre' => 'Vendedor', 'sueldo_base' => 4000, 'estado' => true]);
    $tipoDocumento = \App\Models\Tipo::where('categoria', 'tipo_documento')->first();
    $persona = \App\Models\Persona::create([
        'primer_nombre' => 'Marta', 'primer_apellido' => 'Ruiz',
        'id_tipo_documento' => $tipoDocumento->id, 'numero_documento' => '1112223334',
        'genero' => 'femenino', 'estado' => true,
    ]);
    $estadoActivo = \App\Models\Tipo::where('categoria', 'estado_empleado')->where('codigo', 'activo')->first();
    $empleado = \App\Models\Empleado::create([
        'id_persona' => $persona->id, 'codigo_empleado' => 'EMP-TEST-3', 'id_cargo' => $cargo->id,
        'id_departamento' => $departamento->id, 'fecha_ingreso' => '2026-01-01', 'sueldo_actual' => 4000,
        'id_estado_empleado' => $estadoActivo->id, 'estado' => true,
    ]);
    $estadoAsistencia = \App\Models\Tipo::where('categoria', 'estado_asistencia')->where('codigo', 'puntual')->first();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewireAsistencia::class)
        ->set('form.id_empleado', $empleado->id)
        ->set('form.fecha', now()->format('Y-m-d'))
        ->set('form.hora_entrada', '08:00')
        ->set('form.hora_salida', '17:00')
        ->set('form.id_estado_asistencia', $estadoAsistencia->id)
        ->call('save')
        ->assertHasNoErrors();

    $asistencia = \App\Models\Asistencia::where('id_empleado', $empleado->id)->first();
    expect($asistencia)->not->toBeNull();
    expect((float) $asistencia->horas_trabajadas)->toBe(9.0);
    expect($asistencia->id_gestion)->not->toBeNull();
    expect($asistencia->id_mes)->not->toBeNull();

    $tipoPermiso = \App\Models\Tipo::where('categoria', 'tipo_permiso')->first();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewirePermisosLaborales::class)
        ->set('form.id_empleado', $empleado->id)
        ->set('form.id_tipo_permiso', $tipoPermiso->id)
        ->set('form.fecha_inicio', now()->addDays(5)->format('Y-m-d'))
        ->set('form.fecha_fin', now()->addDays(6)->format('Y-m-d'))
        ->call('save')
        ->assertHasNoErrors();

    $permiso = \App\Models\PermisoLaboral::where('id_empleado', $empleado->id)->first();
    expect($permiso)->not->toBeNull();
    expect($permiso->estado_solicitud)->toBe('pendiente');
    expect($permiso->dias_solicitados)->toBe('2.00');

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewirePermisosLaborales::class)
        ->call('aprobar', $permiso->id);

    expect($permiso->fresh()->estado_solicitud)->toBe('aprobado');
});

test('admin can manage saldos de vacaciones and horas extra', function () {
    $admin = User::where('email', 'admin@rrhh.local')->first();

    $this->actingAs($admin)->get('/vacaciones')->assertOk();
    $this->actingAs($admin)->get('/horas-extra')->assertOk();

    $departamento = \App\Models\Departamento::create(['nombre' => 'Logística', 'estado' => true]);
    $cargo = \App\Models\Cargo::create(['id_departamento' => $departamento->id, 'nombre' => 'Bodeguero', 'sueldo_base' => 3600, 'estado' => true]);
    $tipoDocumento = \App\Models\Tipo::where('categoria', 'tipo_documento')->first();
    $persona = \App\Models\Persona::create([
        'primer_nombre' => 'Luis', 'primer_apellido' => 'Méndez',
        'id_tipo_documento' => $tipoDocumento->id, 'numero_documento' => '7778889990',
        'genero' => 'masculino', 'estado' => true,
    ]);
    $estadoActivo = \App\Models\Tipo::where('categoria', 'estado_empleado')->where('codigo', 'activo')->first();
    $empleado = \App\Models\Empleado::create([
        'id_persona' => $persona->id, 'codigo_empleado' => 'EMP-TEST-4', 'id_cargo' => $cargo->id,
        'id_departamento' => $departamento->id, 'fecha_ingreso' => '2026-01-01', 'sueldo_actual' => 3600,
        'id_estado_empleado' => $estadoActivo->id, 'estado' => true,
    ]);
    $gestionActual = \App\Models\Gestion::where('anio', now()->year)->first();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewireSaldosVacaciones::class)
        ->set('form.id_empleado', $empleado->id)
        ->set('form.id_gestion', $gestionActual->id)
        ->set('form.dias_generados', 15)
        ->set('form.dias_tomados', 5)
        ->call('save')
        ->assertHasNoErrors();

    $saldo = \App\Models\SaldoVacacion::where('id_empleado', $empleado->id)->first();
    expect($saldo)->not->toBeNull();
    expect((float) $saldo->dias_disponibles)->toBe(10.0);

    $tipoHoraExtra = \App\Models\Tipo::where('categoria', 'tipo_hora_extra')->where('codigo', 'diurna')->first();

    \Livewire\Livewire::actingAs($admin)
        ->test(\App\Livewire\LivewireHorasExtra::class)
        ->set('form.id_empleado', $empleado->id)
        ->set('form.fecha', now()->format('Y-m-d'))
        ->set('form.id_tipo_hora_extra', $tipoHoraExtra->id)
        ->set('form.horas', 2)
        ->set('form.valor_hora_base', 20)
        ->call('save')
        ->assertHasNoErrors();

    $detalle = \App\Models\HoraExtraDetalle::where('id_empleado', $empleado->id)->first();
    expect($detalle)->not->toBeNull();
    expect((float) $detalle->monto_calculado)->toBe(50.0);
});

test('admin can access users, roles, permissions modules', function () {
    $admin = User::where('email', 'admin@rrhh.local')->first();

    $this->actingAs($admin)->get('/users')->assertOk();
    $this->actingAs($admin)->get('/roles')->assertOk();
    $this->actingAs($admin)->get('/permissions')->assertOk();
});

test('guest is redirected away from protected routes', function () {
    $this->get('/tipos')->assertRedirect('/login');
});
