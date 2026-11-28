<?php

use App\Models\Sede;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    if (
        ! app()->environment('testing')
        || config('database.default') !== 'sqlite'
        || DB::connection()->getDatabaseName() !== ':memory:'
    ) {
        throw new RuntimeException(
            'Estas pruebas requieren SQLite en memoria.'
        );
    }

    Schema::create('sede', function (Blueprint $table) {
        $table->id('idsede');
        $table->string('nombre', 80);
        $table->string('ciudad', 100);
        $table->string('direccion', 60);
        $table->string('detalles', 150);

        $table->enum('estadosede', [
            'Activo',
            'Inactivo',
        ])->default('Activo');
    });

    $this->admin = User::factory()->create([
        'rol' => 'administrador',
        'estado' => 'activo',
    ]);

    $this->sede = Sede::create([
        'nombre' => 'Sede Principal',
        'ciudad' => 'Tunja',
        'direccion' => 'Carrera 10',
        'detalles' => 'Sede de prueba',
        'estadosede' => 'Activo',
    ]);

    $this->datos = [
        'nombre' => 'Sede Actualizada',
        'ciudad' => 'Tunja',
        'direccion' => 'Carrera 15',
        'detalles' => 'Información actualizada',
    ];
});

test('administrator can access the sede edit page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.sedes.edit', $this->sede))
        ->assertOk()
        ->assertViewIs('admin.sedes.edit')
        ->assertSee('Sede Principal');
});

test('administrator can update a sede', function () {
    $this->actingAs($this->admin)
        ->put(
            route('admin.sedes.update', $this->sede),
            $this->datos
        )
        ->assertRedirect(route('admin.sedes.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('sede', [
        'idsede' => $this->sede->idsede,
        ...$this->datos,
        'estadosede' => 'Activo',
    ]);
});

test('sede update requires valid information', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.sedes.edit', $this->sede))
        ->put(route('admin.sedes.update', $this->sede), [
            'nombre' => '',
            'ciudad' => '',
            'direccion' => '',
            'detalles' => '',
        ])
        ->assertRedirect(route('admin.sedes.edit', $this->sede))
        ->assertSessionHasErrors([
            'nombre',
            'ciudad',
            'direccion',
            'detalles',
        ]);

    expect($this->sede->fresh()->nombre)
        ->toBe('Sede Principal');
});

test('sede update cannot modify its identifier or status', function () {
    $id = $this->sede->idsede;

    $this->actingAs($this->admin)
        ->put(route('admin.sedes.update', $this->sede), [
            ...$this->datos,
            'idsede' => 9999,
            'estadosede' => 'Inactivo',
        ])
        ->assertRedirect(route('admin.sedes.index'));

    $this->assertDatabaseHas('sede', [
        'idsede' => $id,
        'nombre' => 'Sede Actualizada',
        'estadosede' => 'Activo',
    ]);

    $this->assertDatabaseMissing('sede', [
        'idsede' => 9999,
    ]);
});

test('guest users cannot update a sede', function () {
    $this->put(
        route('admin.sedes.update', $this->sede),
        $this->datos
    )->assertRedirect(route('login'));

    expect($this->sede->fresh()->nombre)
        ->toBe('Sede Principal');
});

test('non administrators cannot edit or update sedes', function (
    string $rol
) {
    $user = User::factory()->create([
        'rol' => $rol,
        'estado' => 'activo',
    ]);

    $this->actingAs($user)
        ->get(route('admin.sedes.edit', $this->sede))
        ->assertForbidden();

    $this->put(
        route('admin.sedes.update', $this->sede),
        $this->datos
    )->assertForbidden();

    expect($this->sede->fresh()->nombre)
        ->toBe('Sede Principal');
})->with([
    'paciente',
    'auxiliar',
    'profesional',
]);

test('administrator cannot update a nonexistent sede', function () {
    $this->actingAs($this->admin)
        ->put(
            route('admin.sedes.update', 9999),
            $this->datos
        )
        ->assertNotFound();
});
