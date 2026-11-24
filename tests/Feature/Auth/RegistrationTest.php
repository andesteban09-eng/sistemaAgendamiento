<?php

namespace Tests\Feature\Auth;

use Livewire\Volt\Volt;

test('registration screen can be rendered', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get('/register');

    $response
        ->assertOk()
        ->assertSeeVolt('pages.auth.register');
});

test('new users can register', function () {
    /** @var \Tests\TestCase $this */
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test')
        ->set('last_name', 'User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register');

    $component
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'rol' => 'paciente',
        'estado' => 'activo',
    ]);
});
