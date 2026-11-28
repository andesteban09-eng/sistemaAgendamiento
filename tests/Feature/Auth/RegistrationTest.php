<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;

test('registration screen can be rendered', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSeeVolt('pages.auth.register');
});

test('new users can register and must verify their email', function () {
    Volt::test('pages.auth.register')
        ->set('name', 'Test')
        ->set('last_name', 'User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('verification.notice'));

    $user = User::query()
        ->where('email', 'test@example.com')
        ->firstOrFail();

    $this->assertAuthenticatedAs($user);

    $this->assertDatabaseHas('users', [
        'name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'rol' => 'paciente',
        'estado' => 'activo',
        'email_verified_at' => null,
    ]);

    expect($user->hasVerifiedEmail())
        ->toBeFalse();

    expect(Hash::check('password', $user->password))
        ->toBeTrue();
});

test('registration rejects an existing email address', function () {
    $user = User::factory()->create();

    Volt::test('pages.auth.register')
        ->set('name', 'Test')
        ->set('last_name', 'User')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertHasErrors(['email' => 'unique']);

    $this->assertGuest();
});

test('registration requires password confirmation', function () {
    Volt::test('pages.auth.register')
        ->set('name', 'Test')
        ->set('last_name', 'User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'different-password')
        ->call('register')
        ->assertHasErrors(['password' => 'confirmed']);

    $this->assertGuest();

    $this->assertDatabaseMissing('users', [
        'email' => 'test@example.com',
    ]);
});

test('unverified users cannot access the dashboard', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('verification.notice'));
});
