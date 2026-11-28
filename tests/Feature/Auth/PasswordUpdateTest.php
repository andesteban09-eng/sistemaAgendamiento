<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;
use Tests\Support\OtpTestHelper;

beforeEach(function () {
    OtpTestHelper::configure();
});

test('password change otp can be requested with current password', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('profile.update-password-form')
        ->set('current_password', 'password')
        ->call('requestOtp')
        ->assertHasNoErrors()
        ->assertSet('step', 2);

    OtpTestHelper::sentCode();

    expect(Hash::check(
        'password',
        $user->fresh()->password
    ))->toBeTrue();
});

test('correct password must be provided to request password change', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('profile.update-password-form')
        ->set('current_password', 'wrong-password')
        ->call('requestOtp')
        ->assertHasErrors(['current_password'])
        ->assertSet('step', 1)
        ->assertNoRedirect();

    Notification::assertNothingSent();

    expect(Hash::check(
        'password',
        $user->fresh()->password
    ))->toBeTrue();
});

test('password can be updated after otp verification', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('profile.update-password-form')
        ->set('current_password', 'password')
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
        ->set('current_password', 'password')
        ->set('password', 'NewPassword2026!')
        ->set('password_confirmation', 'NewPassword2026!')
        ->call('updatePassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    expect(Hash::check(
        'NewPassword2026!',
        $user->fresh()->password
    ))->toBeTrue();

    $this->assertGuest();
});

test('invalid otp does not authorize password change', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('profile.update-password-form')
        ->set('current_password', 'password')
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

test('password cannot be updated without completing previous steps', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('profile.update-password-form')
        ->set('current_password', 'password')
        ->set('password', 'NewPassword2026!')
        ->set('password_confirmation', 'NewPassword2026!')
        ->call('updatePassword')
        ->assertSet('step', 1)
        ->assertNoRedirect();

    expect(Hash::check(
        'password',
        $user->fresh()->password
    ))->toBeTrue();

    $this->assertAuthenticatedAs($user);
});

test('password cannot be updated before verifying requested otp', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('profile.update-password-form')
        ->set('current_password', 'password')
        ->call('requestOtp')
        ->assertSet('step', 2)
        ->set('current_password', 'password')
        ->set('password', 'NewPassword2026!')
        ->set('password_confirmation', 'NewPassword2026!')
        ->call('updatePassword')
        ->assertSet('step', 2)
        ->assertNoRedirect();

    expect(Hash::check(
        'password',
        $user->fresh()->password
    ))->toBeTrue();
});
