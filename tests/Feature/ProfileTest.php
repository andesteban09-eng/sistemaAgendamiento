<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Volt;
use Tests\Support\SigacTestSchema;

beforeEach(function () {
    SigacTestSchema::create();
});

function createPatientWithProfile(): User
{
    $user = User::factory()->create([
        'rol' => 'paciente',
        'estado' => 'activo',
    ]);

    DB::table('paciente')->insert([
        'tipodoc' => 'Cedula Ciudadania',
        'numdoc' => '1000000001',
        'telefono' => '3000000001',
        'direccion' => 'Direccion de prueba',
        'ciudad' => 'Tunja',
        'estadopaciente' => 'Activo',
        'idusuario' => $user->id,
    ]);

    return $user;
}

function createProfessionalWithProfile(): User
{
    $user = User::factory()->create([
        'rol' => 'profesional',
        'estado' => 'activo',
    ]);

    DB::table('profesionalsalud')->insert([
        'tipodoc' => 'Cedula Ciudadania',
        'numdoc' => '1000000002',
        'telefono' => '3000000002',
        'estadoprofesionalsalud' => 'Activo',
        'idusuario' => $user->id,
    ]);

    return $user;
}

test('profile page is displayed', function () {
    $user = createPatientWithProfile();

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSeeVolt('profile.update-profile-information-form')
        ->assertSeeVolt('profile.update-password-form');
});

test('patient profile information can be updated', function () {
    $user = createPatientWithProfile();

    $this->actingAs($user);

    Volt::test('profile.update-profile-information-form')
        ->set('name', 'Juan')
        ->set('last_name', 'Perez')
        ->set('telefono', '3101234567')
        ->set('direccion', 'Calle de prueba 123')
        ->set('ciudad', 'Bogota')
        ->call('updateProfileInformation')
        ->assertHasNoErrors()
        ->assertNoRedirect();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Juan',
        'last_name' => 'Perez',
    ]);

    $this->assertDatabaseHas('paciente', [
        'idusuario' => $user->id,
        'telefono' => '3101234567',
        'direccion' => 'Calle de prueba 123',
        'ciudad' => 'Bogota',
        'numdoc' => '1000000001',
    ]);
});

test('professional profile information can be updated', function () {
    $user = createProfessionalWithProfile();

    $this->actingAs($user);

    Volt::test('profile.update-profile-information-form')
        ->set('name', 'Carlos')
        ->set('last_name', 'Rodriguez')
        ->set('telefono', '3201234567')
        ->call('updateProfileInformation')
        ->assertHasNoErrors()
        ->assertNoRedirect();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Carlos',
        'last_name' => 'Rodriguez',
    ]);

    $this->assertDatabaseHas('profesionalsalud', [
        'idusuario' => $user->id,
        'telefono' => '3201234567',
        'numdoc' => '1000000002',
    ]);
});

test('email and verification status remain unchanged after profile update', function () {
    $user = createPatientWithProfile();

    $originalEmail = $user->email;
    $originalVerification = $user->email_verified_at;

    $this->actingAs($user);

    Volt::test('profile.update-profile-information-form')
        ->set('name', 'Nombre actualizado')
        ->call('updateProfileInformation')
        ->assertHasNoErrors()
        ->assertNoRedirect();

    $user->refresh();

    expect($user->email)->toBe($originalEmail);

    expect($user->email_verified_at->equalTo(
        $originalVerification
    ))->toBeTrue();
});

test('account deletion is not available from the profile page', function () {
    $user = createPatientWithProfile();

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertDontSee('profile.delete-user-form', false)
        ->assertDontSee('Eliminar cuenta');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);
});

test('patient without an associated profile receives a warning', function () {
    $user = User::factory()->create([
        'rol' => 'paciente',
        'estado' => 'activo',
    ]);

    $this->actingAs($user);

    Volt::test('profile.update-profile-information-form')
        ->assertSee(
            'Tu cuenta todavía no tiene un perfil asociado.'
        );
});
