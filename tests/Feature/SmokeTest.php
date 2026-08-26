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

test('admin can access users, roles, permissions modules', function () {
    $admin = User::where('email', 'admin@rrhh.local')->first();

    $this->actingAs($admin)->get('/users')->assertOk();
    $this->actingAs($admin)->get('/roles')->assertOk();
    $this->actingAs($admin)->get('/permissions')->assertOk();
});

test('guest is redirected away from protected routes', function () {
    $this->get('/tipos')->assertRedirect('/login');
});
