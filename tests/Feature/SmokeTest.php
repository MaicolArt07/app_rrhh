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

test('admin can access users, roles, permissions modules', function () {
    $admin = User::where('email', 'admin@rrhh.local')->first();

    $this->actingAs($admin)->get('/users')->assertOk();
    $this->actingAs($admin)->get('/roles')->assertOk();
    $this->actingAs($admin)->get('/permissions')->assertOk();
});

test('guest is redirected away from protected routes', function () {
    $this->get('/tipos')->assertRedirect('/login');
});
