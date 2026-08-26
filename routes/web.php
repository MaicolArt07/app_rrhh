<?php

use App\Livewire\LivewireCargos;
use App\Livewire\LivewireDepartamentos;
use App\Livewire\LivewireEmpleados;
use App\Livewire\LivewireGestiones;
use App\Livewire\LivewireHome;
use App\Livewire\LivewirePermissions;
use App\Livewire\LivewirePersonas;
use App\Livewire\LivewireRoles;
use App\Livewire\LivewireTipos;
use App\Livewire\LivewireUsers;
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

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

});

require __DIR__.'/auth.php';
