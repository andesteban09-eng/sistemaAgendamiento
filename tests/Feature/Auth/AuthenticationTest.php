<?php

use App\Models\User;
use Livewire\Volt\Volt;
use Tests\Support\SigacTestSchema;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response
        ->assertOk()
        ->assertSeeVolt('pages.auth.login');
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $component = Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password');

    $component->call('login');

    $component
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $component = Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'wrong-password');

    $component->call('login');

    $component
        ->assertHasErrors()
        ->assertNoRedirect();

    $this->assertGuest();
});

test('patient dashboard and navigation can be rendered', function () {
    SigacTestSchema::create();

    $user = User::factory()->create([
        'rol' => 'paciente',
        'estado' => 'activo',
    ]);

    $this->actingAs($user);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertViewIs('Paciente.dashboardPaciente')
        ->assertSee('sigac-dashboard-header', false);
});

test('users can logout through the http logout route', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('private routes cannot be accessed after logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();

    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));

    $this->get(route('profile'))
        ->assertRedirect(route('login'));
});

test('guest users cannot access private routes', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));

    $this->get(route('profile'))
        ->assertRedirect(route('login'));
});

test('authenticated dashboard responses cannot be cached', function () {
    SigacTestSchema::create();

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();

    expect(
        $response->headers->hasCacheControlDirective('no-store')
    )->toBeTrue();

    expect(
        $response->headers->hasCacheControlDirective('private')
    )->toBeTrue();
});

test('authenticated profile responses cannot be cached', function () {
    SigacTestSchema::create();

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk();

    expect(
        $response->headers->hasCacheControlDirective('no-store')
    )->toBeTrue();
});

test('login responses cannot be cached', function () {
    $response = $this->get(route('login'))
        ->assertOk();

    expect(
        $response->headers->hasCacheControlDirective('no-store')
    )->toBeTrue();
});

test('logout clears previous session data', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession([
            'security_test_value' => 'previous_session',
        ]);

    $this->post(route('logout'))
        ->assertRedirect(route('login'))
        ->assertSessionMissing('security_test_value');

    $this->assertGuest();
});
