<?php

use App\Livewire\LivewireAsistencia;
use App\Livewire\LivewireCargos;
use App\Livewire\LivewireContratos;
use App\Livewire\LivewireDepartamentos;
use App\Livewire\LivewireDocumentos;
use App\Livewire\LivewireEmpleados;
use App\Livewire\LivewireEvaluaciones;
use App\Livewire\LivewireFeriados;
use App\Livewire\LivewireAnticipos;
use App\Livewire\LivewireConceptosPago;
use App\Livewire\LivewireGestiones;
use App\Livewire\LivewireHome;
use App\Livewire\LivewireHorasExtra;
use App\Livewire\LivewireLiquidaciones;
use App\Livewire\LivewirePermisosLaborales;
use App\Livewire\LivewirePermissions;
use App\Livewire\LivewirePersonas;
use App\Livewire\LivewirePlanillas;
use App\Livewire\LivewireNotificaciones;
use App\Livewire\LivewireReglamentos;
use App\Livewire\LivewireRoles;
use App\Livewire\LivewireSaldosVacaciones;
use App\Livewire\LivewireTipos;
use App\Livewire\LivewireTurnos;
use App\Livewire\LivewireUsers;
use App\Http\Controllers\BoletaController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RedirectToFirstPermission;

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::get('/', LivewireHome::class)
    ->middleware(['auth', RedirectToFirstPermission::class])
    ->name('home');
    
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    // Route::get('/', function () {
    //     return view('dashboard');
    // })->name('home');

    Route::get('/', LivewireHome::class)->middleware('can:Ver Inicio')->name('home');


    Route::get('/users', LivewireUsers::class)->middleware('can:Ver Usuarios')->name('users');
    Route::get('/roles', LivewireRoles::class)->middleware('can:Ver Roles')->name('roles');
    Route::get('/permissions', LivewirePermissions::class)->middleware('can:Ver Permisos')->name('permissions');
    Route::get('/tipos', LivewireTipos::class)->middleware('can:Ver Tipos')->name('tipos');
    Route::get('/gestiones', LivewireGestiones::class)->middleware('can:Ver Gestiones')->name('gestiones');
    Route::get('/personas', LivewirePersonas::class)->middleware('can:Ver Personas')->name('personas');
    Route::get('/departamentos', LivewireDepartamentos::class)->middleware('can:Ver Departamentos')->name('departamentos');
    Route::get('/cargos', LivewireCargos::class)->middleware('can:Ver Cargos')->name('cargos');
    Route::get('/empleados', LivewireEmpleados::class)->middleware('can:Ver Empleados')->name('empleados');
    Route::get('/contratos', LivewireContratos::class)->middleware('can:Ver Contratos')->name('contratos');
    Route::get('/documentos', LivewireDocumentos::class)->middleware('can:Ver Documentos')->name('documentos');
    Route::get('/turnos', LivewireTurnos::class)->middleware('can:Ver Asistencia')->name('turnos');
    Route::get('/feriados', LivewireFeriados::class)->middleware('can:Ver Asistencia')->name('feriados');
    Route::get('/asistencia', LivewireAsistencia::class)->middleware('can:Ver Asistencia')->name('asistencia');
    Route::get('/permisos-laborales', LivewirePermisosLaborales::class)->middleware('can:Ver Asistencia')->name('permisos-laborales');
    Route::get('/vacaciones', LivewireSaldosVacaciones::class)->middleware('can:Ver Vacaciones')->name('vacaciones');
    Route::get('/horas-extra', LivewireHorasExtra::class)->middleware('can:Ver Horas Extra')->name('horas-extra');
    Route::get('/conceptos-pago', LivewireConceptosPago::class)->middleware('can:Ver Planillas')->name('conceptos-pago');
    Route::get('/planillas', LivewirePlanillas::class)->middleware('can:Ver Planillas')->name('planillas');
    Route::get('/planillas/boleta/{detalle}', [BoletaController::class, 'download'])->middleware('can:Ver Planillas')->name('planillas.boleta');
    Route::get('/anticipos', LivewireAnticipos::class)->middleware('can:Ver Anticipos')->name('anticipos');
    Route::get('/liquidaciones', LivewireLiquidaciones::class)->middleware('can:Ver Liquidaciones')->name('liquidaciones');
    Route::get('/evaluaciones', LivewireEvaluaciones::class)->middleware('can:Ver Evaluaciones')->name('evaluaciones');
    Route::get('/reglamentos', LivewireReglamentos::class)->middleware('can:Ver Reglamentos')->name('reglamentos');
    Route::get('/notificaciones', LivewireNotificaciones::class)->middleware('can:Ver Notificaciones')->name('notificaciones');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

});

require __DIR__.'/auth.php';
