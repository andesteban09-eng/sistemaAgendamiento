<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;
use Tests\Support\OtpTestHelper;

beforeEach(function () {
    OtpTestHelper::configure();
});

test('password recovery screen can be rendered', function () {
    $this->get('/forgot-password')
        ->assertOk()
        ->assertSeeVolt('pages.auth.forgot-password');
});

test('password recovery otp can be requested', function () {
    $user = User::factory()->create();

    Volt::test('pages.auth.forgot-password')
        ->set('email', $user->email)
        ->call('requestOtp')
        ->assertHasNoErrors()
        ->assertSet('step', 2);

    OtpTestHelper::sentCode();

    expect(Hash::check(
        'password',
        $user->fresh()->password
    ))->toBeTrue();
});

test('password can be reset after otp verification', function () {
    $user = User::factory()->create();

    $component = Volt::test('pages.auth.forgot-password')
        ->set('email', $user->email)
        ->call('requestOtp')
        ->assertHasNoErrors()
        ->assertSet('step', 2);

    $code = OtpTestHelper::sentCode();

    $component
        ->set('otp', $code)
        ->call('verifyOtp')
        ->assertHasNoErrors()
        ->assertSet('step', 3);

    expect(Hash::check(
        'password',
        $user->fresh()->password
    ))->toBeTrue();

    $component
        ->set('password', 'NewPassword2026!')
        ->set('password_confirmation', 'NewPassword2026!')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    expect(Hash::check(
        'NewPassword2026!',
        $user->fresh()->password
    ))->toBeTrue();

    $this->assertGuest();
});

test('invalid otp does not authorize password recovery', function () {
    $user = User::factory()->create();

    $component = Volt::test('pages.auth.forgot-password')
        ->set('email', $user->email)
        ->call('requestOtp')
        ->assertSet('step', 2);

    $code = OtpTestHelper::invalidCode(
        OtpTestHelper::sentCode()
    );

    $component
        ->set('otp', $code)
        ->call('verifyOtp')
        ->assertHasErrors(['otp'])
        ->assertSet('step', 2)
        ->assertNoRedirect();

    expect(Hash::check(
        'password',
        $user->fresh()->password
    ))->toBeTrue();
});

test('password cannot be reset before otp verification', function () {
    $user = User::factory()->create();

    Volt::test('pages.auth.forgot-password')
        ->set('email', $user->email)
        ->call('requestOtp')
        ->assertSet('step', 2)
        ->set('password', 'NewPassword2026!')
        ->set('password_confirmation', 'NewPassword2026!')
        ->call('resetPassword')
        ->assertNoRedirect();

    expect(Hash::check(
        'password',
        $user->fresh()->password
    ))->toBeTrue();
});

test('password recovery does not disclose unknown accounts', function () {
    Volt::test('pages.auth.forgot-password')
        ->set('email', 'unknown@example.com')
        ->call('requestOtp')
        ->assertHasNoErrors()
        ->assertSet('step', 2);

    Notification::assertNothingSent();
});

test('password recovery does not disclose inactive accounts', function () {
    $user = User::factory()->create([
        'estado' => 'inactivo',
    ]);

    Volt::test('pages.auth.forgot-password')
        ->set('email', $user->email)
        ->call('requestOtp')
        ->assertHasNoErrors()
        ->assertSet('step', 2);

    Notification::assertNothingSent();
});

test('password recovery does not disclose unverified accounts', function () {
    $user = User::factory()->unverified()->create();

    Volt::test('pages.auth.forgot-password')
        ->set('email', $user->email)
        ->call('requestOtp')
        ->assertHasNoErrors()
        ->assertSet('step', 2);

    Notification::assertNothingSent();
});
