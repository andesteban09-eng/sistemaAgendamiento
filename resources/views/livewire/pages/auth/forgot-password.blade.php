<?php

use App\Models\User;
use App\Models\OtpChallenge;
use App\Services\Security\OtpService;
use App\Services\Security\OtpRateLimiter;
use App\Services\Security\PasswordSecurityService;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    private const SESSION_KEY = 'security.password_recovery';

    #[Locked]
    public int $step = 1;

    public string $email = '';
    public string $otp = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function requestOtp(): void
    {
        $this->resetValidation();

        $this->email = Str::lower(trim($this->email));

        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $limiter = app(OtpRateLimiter::class);

        if (!$limiter->allowIssue(request()->ip())) {
            $this->addError('email', 'Demasiadas solicitudes. Intenta más tarde.');

            return;
        }

        $previous = session()->get(self::SESSION_KEY);

        // Mantener el desafío anterior si el reenvío
        // es rechazado por el límite de solicitudes.
        $state = [
            'email' => $this->email,
            'user_id' => null,
            'challenge_id' => null,
        ];

        if (($previous['email'] ?? null) === $this->email) {
            $state = $previous;
        }

        $user = User::query()->where('email', $this->email)->where('estado', 'activo')->whereNotNull('email_verified_at')->first();

        if ($user !== null) {
            try {
                $challenge = app(OtpService::class)->issue($user, OtpChallenge::PURPOSE_PASSWORD_RESET, session()->getId());

                $state = [
                    'email' => $this->email,
                    'user_id' => $user->getKey(),
                    'challenge_id' => $challenge->getKey(),
                ];
            } catch (ValidationException $exception) {
                // No revelar si la cuenta existe,
                // está bloqueada o alcanzó sus límites.
            }
        }

        session()->put(self::SESSION_KEY, $state);

        $this->reset('otp', 'password', 'password_confirmation');

        $this->step = 2;
    }

    public function verifyOtp(): void
    {
        $this->resetValidation();

        try {
            $limiter = app(OtpRateLimiter::class);

            if (!$limiter->allowVerification(request()->ip())) {
                $this->addError('otp', 'Demasiados intentos. Intenta más tarde.');

                return;
            }

            $this->validate([
                'otp' => ['required', 'regex:/\A[0-9]{6}\z/'],
            ]);

            $state = session()->get(self::SESSION_KEY);

            $user = $this->recoveryUser();

            if ($user === null || empty($state['challenge_id'])) {
                $this->invalidOtp();

                return;
            }

            try {
                $verified = app(OtpService::class)->verify($user, $state['challenge_id'], OtpChallenge::PURPOSE_PASSWORD_RESET, session()->getId(), $this->otp);
            } catch (ValidationException $exception) {
                $verified = false;
            }

            if (!$verified) {
                $this->invalidOtp();

                return;
            }

            $this->step = 3;
        } finally {
            $this->reset('otp');
        }
    }

    public function resetPassword(): void
    {
        $this->resetValidation();

        try {
            $state = session()->get(self::SESSION_KEY);

            $user = $this->recoveryUser();

            if ($user === null || empty($state['challenge_id'])) {
                $this->restart();

                $this->addError('email', 'La autorización no está disponible. Solicita otro código.');

                return;
            }

            $result = app(PasswordSecurityService::class)->resetPassword($user, $state['challenge_id'], session()->getId(), $this->password, $this->password_confirmation);

            if (!$result) {
                $this->step = 2;

                $this->addError('otp', 'La autorización no es válida o ha expirado. Solicita otro código.');

                return;
            }

            session()->invalidate();
            session()->regenerateToken();

            session()->flash('status', 'Tu contraseña se ha actualizado correctamente.');

            $this->redirectRoute('login', navigate: false);
        } finally {
            $this->reset('password', 'password_confirmation');
        }
    }

    public function restart(): void
    {
        $state = session()->get(self::SESSION_KEY);

        $user = $this->recoveryUser();

        if ($user !== null && !empty($state['challenge_id'])) {
            try {
                app(OtpService::class)->revoke($user, $state['challenge_id'], OtpChallenge::PURPOSE_PASSWORD_RESET, session()->getId());
            } catch (ValidationException $exception) {
                // La cuenta ya no cumple las condiciones.
            }
        }

        session()->forget(self::SESSION_KEY);

        $this->reset('email', 'otp', 'password', 'password_confirmation');

        $this->resetValidation();

        $this->step = 1;
    }

    private function recoveryUser(): ?User
    {
        $state = session()->get(self::SESSION_KEY);

        if (empty($state['user_id'])) {
            return null;
        }

        return User::query()->whereKey($state['user_id'])->where('email', $state['email'])->where('estado', 'activo')->whereNotNull('email_verified_at')->first();
    }

    private function invalidOtp(): void
    {
        $this->addError('otp', 'El código no es válido o ha expirado.');
    }
};

?>


<div class="sigac-auth-page sigac-auth-page-recovery">

    <div class="sigac-auth-layout">

        {{-- Presentación --}}
        <section class="sigac-auth-intro">

            <span class="sigac-auth-eyebrow">
                SEGURIDAD DE TU CUENTA
            </span>

            <div class="sigac-auth-icon">
                <i class="bi bi-shield-lock"></i>
            </div>

            <h1>
                Recupera el acceso a
                <span>tu cuenta.</span>
            </h1>

            <p>
                Protegemos tu información mediante un
                código de seguridad enviado a tu correo
                electrónico.
            </p>

        </section>

        {{-- Tarjeta de recuperación --}}
        <section class="sigac-auth-card">

            {{-- Indicador de progreso --}}
            <div class="sigac-recovery-progress">

                @foreach ([
        1 => 'Identificación',
        2 => 'Verificación',
        3 => 'Contraseña',
    ] as $number => $label)
                    <div @class([
                        'sigac-recovery-step',
                        'is-active' => $step === $number,
                        'is-completed' => $step > $number,
                    ]) @if ($step === $number) aria-current="step" @endif>

                        <span class="sigac-recovery-number">

                            @if ($step > $number)
                                <i class="bi bi-check-lg"></i>
                            @else
                                {{ $number }}
                            @endif

                        </span>

                        <small>{{ $label }}</small>

                    </div>
                @endforeach

            </div>

            {{-- PASO 1: Identificación --}}
            @if ($step === 1)
                <div wire:key="recovery-identification">

                    <div class="sigac-auth-card-heading">

                        <h2>Recuperar contraseña</h2>

                        <p>
                            Ingresa el correo electrónico
                            asociado a tu cuenta.
                        </p>

                    </div>

                    <form wire:submit="requestOtp">

                        <div class="sigac-auth-field">

                            <label for="email" class="form-label">

                                <i class="bi bi-envelope"></i>
                                Correo electrónico

                            </label>

                            <input wire:model="email" id="email" type="email" class="form-control"
                                placeholder="correo@ejemplo.com" autocomplete="email" maxlength="255" required>

                            @error('email')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>

                        <button type="submit" class="btn sigac-auth-submit w-100" wire:loading.attr="disabled">

                            <span wire:loading.remove wire:target="requestOtp">

                                <i class="bi bi-send me-1"></i>
                                Enviar código de seguridad

                            </span>

                            <span wire:loading wire:target="requestOtp">
                                Procesando solicitud...
                            </span>

                        </button>

                    </form>

                </div>

                {{-- PASO 2: Verificación OTP --}}
            @elseif ($step === 2)
                <div wire:key="recovery-verification">

                    <div class="sigac-auth-card-heading">

                        <h2>Verifica tu identidad</h2>

                        <p>
                            Si tu cuenta cumple los requisitos,
                            recibirás un código de seguridad
                            en tu correo electrónico.
                        </p>

                    </div>

                    <div class="sigac-recovery-email">
                        <i class="bi bi-envelope"></i>
                        {{ $email }}
                    </div>

                    @error('email')
                        <div class="text-danger small mb-2">
                            {{ $message }}
                        </div>
                    @enderror

                    <form wire:submit="verifyOtp">

                        <div class="sigac-auth-field">

                            <label for="otp" class="form-label">
                                Código de seguridad
                            </label>

                            <input wire:model="otp" id="otp" type="text" class="form-control sigac-otp-input"
                                inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" minlength="6"
                                maxlength="6" placeholder="000000" required>

                            @error('otp')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>

                        <p class="sigac-recovery-hint">
                            <i class="bi bi-clock"></i>
                            El código tiene una vigencia
                            de 5 minutos.
                        </p>

                        <button type="submit" class="btn sigac-auth-submit w-100" wire:loading.attr="disabled">

                            <span wire:loading.remove wire:target="verifyOtp">
                                Verificar código
                            </span>

                            <span wire:loading wire:target="verifyOtp">
                                Verificando...
                            </span>

                        </button>

                    </form>

                    <div class="sigac-auth-links">

                        <span>¿No recibiste el código?</span>

                        <button type="button" class="sigac-recovery-link" wire:click="requestOtp"
                            wire:loading.attr="disabled">

                            Reenviar código

                        </button>

                    </div>

                    <p class="sigac-recovery-hint text-center">
                        Espera al menos 60 segundos
                        antes de solicitar otro código.
                    </p>

                    <div class="sigac-auth-links">

                        <button type="button" class="sigac-recovery-link" wire:click="restart"
                            wire:loading.attr="disabled">

                            Cambiar correo electrónico

                        </button>

                    </div>

                </div>

                {{-- PASO 3: Nueva contraseña --}}
            @elseif ($step === 3)
                <div wire:key="recovery-password">

                    <div class="sigac-auth-card-heading">

                        <h2>Nueva contraseña</h2>

                        <p>
                            Establece una nueva contraseña
                            para recuperar el acceso a tu cuenta.
                        </p>

                    </div>

                    <form wire:submit="resetPassword">

                        <div class="sigac-auth-field">

                            <label for="password" class="form-label">

                                <i class="bi bi-lock"></i>
                                Nueva contraseña

                            </label>

                            <input wire:model="password" id="password" type="password" class="form-control"
                                autocomplete="new-password" required>

                            @error('password')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>

                        <div class="sigac-auth-field">

                            <label for="password_confirmation" class="form-label">

                                <i class="bi bi-lock-fill"></i>
                                Confirmar contraseña

                            </label>

                            <input wire:model="password_confirmation" id="password_confirmation" type="password"
                                class="form-control" autocomplete="new-password" required>

                        </div>

                        @error('otp')
                            <div class="text-danger small mb-2">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="sigac-recovery-notice">

                            <i class="bi bi-info-circle"></i>

                            <span>
                                Al restablecer tu contraseña,
                                se cerrarán las sesiones
                                anteriores de tu cuenta.
                            </span>

                        </div>

                        <button type="submit" class="btn sigac-auth-submit w-100" wire:loading.attr="disabled">

                            <span wire:loading.remove wire:target="resetPassword">
                                Restablecer contraseña
                            </span>

                            <span wire:loading wire:target="resetPassword">
                                Actualizando contraseña...
                            </span>

                        </button>

                    </form>

                    <div class="sigac-auth-links">

                        <button type="button" class="sigac-recovery-link" wire:click="restart"
                            wire:loading.attr="disabled">

                            Reiniciar recuperación

                        </button>

                    </div>

                </div>
            @endif

            {{-- Regresar al inicio de sesión --}}
            <div class="sigac-auth-links">

                <a href="{{ route('login') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Volver a iniciar sesión
                </a>

            </div>

        </section>

    </div>

</div>
