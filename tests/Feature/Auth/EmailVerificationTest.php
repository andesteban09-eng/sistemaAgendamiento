<?php

use App\Models\User;
use Livewire\Volt\Volt;
use Tests\Support\OtpTestHelper;

beforeEach(function () {
    OtpTestHelper::configure();
});

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/verify-email')
        ->assertOk()
        ->assertSeeVolt('pages.auth.verify-email');
});

test('email verification code can be requested', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user);

    Volt::test('pages.auth.verify-email')
        ->call('sendVerification')
        ->assertHasNoErrors()
        ->assertSet('codeSent', true);

    OtpTestHelper::sentCode();

    expect($user->fresh()->hasVerifiedEmail())
        ->toBeFalse();
});

test('email can be verified with a valid otp', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user);

    $component = Volt::test('pages.auth.verify-email')
        ->call('sendVerification')
        ->assertHasNoErrors();

    $code = OtpTestHelper::sentCode();

    $component
        ->set('otp', $code)
        ->call('verifyOtp')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect($user->fresh()->hasVerifiedEmail())
        ->toBeTrue();

    $this->assertAuthenticatedAs($user);
});

test('email cannot be verified with an invalid otp', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user);

    $component = Volt::test('pages.auth.verify-email')
        ->call('sendVerification')
        ->assertHasNoErrors();

    $code = OtpTestHelper::invalidCode(
        OtpTestHelper::sentCode()
    );

    $component
        ->set('otp', $code)
        ->call('verifyOtp')
        ->assertHasErrors(['otp'])
        ->assertNoRedirect();

    expect($user->fresh()->hasVerifiedEmail())
        ->toBeFalse();
});

test('email cannot be verified without requesting an otp', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user);

    Volt::test('pages.auth.verify-email')
        ->set('otp', '123456')
        ->call('verifyOtp')
        ->assertHasErrors(['otp'])
        ->assertNoRedirect();

    expect($user->fresh()->hasVerifiedEmail())
        ->toBeFalse();
});
