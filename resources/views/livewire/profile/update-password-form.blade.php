<?php

use App\Models\User;
use App\Models\OtpChallenge;
use App\Services\Security\OtpService;
use App\Services\Security\OtpRateLimiter;
use App\Services\Security\PasswordSecurityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new class extends Component {
    private const SESSION_KEY = 'security.password_change';

    #[Locked]
    public int $step = 1;

    public string $current_password = '';
    public string $otp = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->authenticatedUser();
    }

    /**
     * Validar la contraseña actual y solicitar un OTP.
     */
    public function requestOtp(): void
    {
        $this->resetValidation();

        if ($this->step !== 1 && $this->step !== 2) {
            return;
        }

        try {
            $user = $this->authenticatedUser();

            if (!app(OtpRateLimiter::class)->allowIssue(request()->ip())) {
                $this->addError('request', 'Demasiadas solicitudes. Intenta más tarde.');

                return;
            }

            $this->validate([
                'current_password' => ['required', 'string', 'max:72'],
            ]);

            if (!Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'La contraseña actual es incorrecta.');

                return;
            }

            $challenge = app(OtpService::class)->issue($user, OtpChallenge::PURPOSE_PASSWORD_CHANGE, session()->getId());

            session()->put(self::SESSION_KEY, [
                'user_id' => $user->getKey(),
                'challenge_id' => $challenge->getKey(),
            ]);

            $this->reset('otp');

            $this->step = 2;

            session()->flash('password_change_status', 'Hemos enviado un código de seguridad a tu correo.');
        } catch (ValidationException $exception) {
            $errors = $exception->errors();

            if (isset($errors['otp'])) {
                $this->addError('request', $errors['otp'][0]);
            } else {
                $this->setErrorBag($exception->validator->errors());
            }
        } finally {
            $this->reset('current_password');
        }
    }

    /**
     * Verificar el código de seguridad.
     */
    public function verifyOtp(): void
    {
        $this->resetValidation();

        if ($this->step !== 2) {
            return;
        }

        try {
            $user = $this->authenticatedUser();

            if (!app(OtpRateLimiter::class)->allowVerification(request()->ip())) {
                $this->addError('otp', 'Demasiados intentos. Intenta más tarde.');

                return;
            }

            $this->validate([
                'otp' => ['required', 'regex:/\A[0-9]{6}\z/'],
            ]);

            $challengeId = $this->challengeId($user);

            if ($challengeId === null) {
                $this->addError('otp', 'Solicita un nuevo código de seguridad.');

                return;
            }

            $verified = app(OtpService::class)->verify($user, $challengeId, OtpChallenge::PURPOSE_PASSWORD_CHANGE, session()->getId(), $this->otp);

            if (!$verified) {
                $this->addError('otp', 'El código no es válido o ha expirado.');

                return;
            }

            $this->step = 3;
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());
        } finally {
            $this->reset('otp');
        }
    }

    /**
     * Actualizar las credenciales de manera segura.
     */
    public function updatePassword(): void
    {
        $this->resetValidation();

        if ($this->step !== 3) {
            return;
        }

        try {
            $user = $this->authenticatedUser();

            if (!app(OtpRateLimiter::class)->allowVerification(request()->ip())) {
                $this->addError('request', 'Demasiados intentos. Intenta más tarde.');

                return;
            }

            $challengeId = $this->challengeId($user);

            if ($challengeId === null) {
                $this->restart();

                $this->addError('request', 'La autorización ya no está disponible.');

                return;
            }

            $this->validate([
                'current_password' => ['required', 'string', 'max:72'],
            ]);

            $changed = app(PasswordSecurityService::class)->changePassword($user, $challengeId, session()->getId(), $this->current_password, $this->password, $this->password_confirmation);

            if (!$changed) {
                $this->restart();

                $this->addError('request', 'La autorización no es válida o ha expirado.');

                return;
            }

            Auth::logout();

            session()->invalidate();
            session()->regenerateToken();

            session()->flash('status', 'Tu contraseña se ha actualizado correctamente.');

            $this->redirectRoute('login', navigate: false);
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());
        } finally {
            $this->reset('current_password', 'password', 'password_confirmation');
        }
    }

    /**
     * Cancelar el procedimiento y revocar el código.
     */
    public function restart(): void
    {
        $user = $this->authenticatedUser();

        $challengeId = $this->challengeId($user);

        if ($challengeId !== null) {
            app(OtpService::class)->revoke($user, $challengeId, OtpChallenge::PURPOSE_PASSWORD_CHANGE, session()->getId());
        }

        session()->forget(self::SESSION_KEY);

        $this->reset('current_password', 'otp', 'password', 'password_confirmation');

        $this->resetValidation();

        $this->step = 1;
    }

    /**
     * Recuperar la autorización asociada a la sesión.
     */
    private function challengeId(User $user): ?string
    {
        $state = session()->get(self::SESSION_KEY);

        if ((string) ($state['user_id'] ?? '') !== (string) $user->getKey()) {
            return null;
        }

        return $state['challenge_id'] ?? null;
    }

    /**
     * Comprobar las condiciones de acceso.
     */
    private function authenticatedUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        $user = $user->fresh();

        abort_unless($user !== null && $user->estado === 'activo' && $user->hasVerifiedEmail(), 403);

        return $user;
    }
};

?>

<section class="sigac-password-change">

    <header class="mb-4">

        <h5 class="fw-semibold">
            <i class="bi bi-shield-lock me-2"></i>
            Cambiar contraseña
        </h5>

        <p class="text-muted small mb-0">
            Protege tu cuenta mediante nuestro procedimiento
            de verificación de seguridad.
        </p>

    </header>

    {{-- Indicador de progreso --}}
    <div class="row g-2 mb-4 text-center">

        @foreach ([
        1 => 'Identificación',
        2 => 'Verificación',
        3 => 'Contraseña',
    ] as $number => $label)
            <div class="col">

                <div
                    class="rounded border p-2
                    {{ $step === $number
                        ? 'bg-primary text-white'
                        : ($step > $number
                            ? 'bg-success text-white'
                            : 'bg-light text-muted') }}">

                    <small class="fw-semibold">
                        {{ $number }}. {{ $label }}
                    </small>

                </div>

            </div>
        @endforeach

    </div>

    @error('request')
        <div class="alert alert-danger small">
            {{ $message }}
        </div>
    @enderror

    @if (session('password_change_status'))
        <div class="alert alert-success small">
            {{ session('password_change_status') }}
        </div>
    @endif

    {{-- PASO 1 --}}
    @if ($step === 1)

        <form wire:submit="requestOtp" wire:key="password-change-identification">

            <div class="mb-3">

                <label for="change_current_password" class="form-label">
                    Contraseña actual
                </label>

                <input wire:model="current_password" id="change_current_password" type="password"
                    class="form-control
                        @error('current_password')
                            is-invalid
                        @enderror"
                    autocomplete="current-password" required>

                @error('current_password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <p class="small text-muted">

                <i class="bi bi-info-circle me-1"></i>

                Enviaremos un código de seguridad
                a tu correo electrónico verificado.

            </p>

            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">

                <i class="bi bi-envelope me-2"></i>

                <span wire:loading.remove wire:target="requestOtp">
                    Enviar código
                </span>

                <span wire:loading wire:target="requestOtp">
                    Enviando...
                </span>

            </button>

        </form>

        {{-- PASO 2 --}}
    @elseif ($step === 2)
        <div wire:key="password-change-verification">

            <div class="alert alert-light border small">

                <i class="bi bi-envelope-check me-2"></i>

                {{ auth()->user()->email }}

            </div>

            <form wire:submit="verifyOtp">

                <div class="mb-3">

                    <label for="change_otp" class="form-label">
                        Código de seguridad
                    </label>

                    <input wire:model="otp" id="change_otp" type="text" inputmode="numeric"
                        autocomplete="one-time-code" pattern="[0-9]{6}" minlength="6" maxlength="6"
                        placeholder="000000"
                        class="form-control text-center
                            fs-4
                            @error('otp') is-invalid @enderror"
                        required>

                    @error('otp')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="form-text">
                        El código tiene una vigencia de
                        {{ config('otp.expiration_minutes') }}
                        minutos.
                    </div>

                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">

                    <i class="bi bi-shield-check me-2"></i>
                    Verificar código

                </button>

            </form>

            <hr>

            {{-- Reenvío con nueva validación de contraseña --}}
            <details class="mb-3">

                <summary class="text-primary small">
                    Solicitar un nuevo código
                </summary>

                <form wire:submit="requestOtp" class="mt-3">

                    <label for="resend_current_password" class="form-label">
                        Contraseña actual
                    </label>

                    <input wire:model="current_password" id="resend_current_password" type="password"
                        autocomplete="current-password"
                        class="form-control mb-2
                            @error('current_password')
                                is-invalid
                            @enderror"
                        required>

                    @error('current_password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <button type="submit" class="btn btn-outline-primary btn-sm" wire:loading.attr="disabled">

                        Reenviar código

                    </button>

                    <p class="form-text">
                        Debes esperar al menos
                        {{ config('otp.resend_seconds') }}
                        segundos entre solicitudes.
                    </p>

                </form>

            </details>

        </div>

        {{-- PASO 3 --}}
    @elseif ($step === 3)
        <form wire:submit="updatePassword" wire:key="password-change-new-password">

            <div class="alert alert-success small">

                <i class="bi bi-check-circle me-2"></i>
                Código de seguridad verificado.

            </div>

            @foreach ([
        'current_password' => 'Contraseña actual',
        'password' => 'Nueva contraseña',
        'password_confirmation' => 'Confirmar contraseña',
    ] as $field => $label)
                <div class="mb-3">

                    <label for="change_{{ $field }}" class="form-label">

                        {{ $label }}

                    </label>

                    <input wire:model="{{ $field }}" id="change_{{ $field }}" type="password"
                        autocomplete="{{ $field === 'current_password' ? 'current-password' : 'new-password' }}"
                        class="form-control
                            @error($field) is-invalid @enderror" required>

                    @error($field)
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>
            @endforeach

            @error('otp')
                <div class="alert alert-danger small">
                    {{ $message }}
                </div>
            @enderror

            <div class="alert alert-warning small">

                <i class="bi bi-info-circle me-2"></i>

                Al actualizar tu contraseña, se cerrarán
                las sesiones anteriores y deberás iniciar
                sesión nuevamente.

            </div>

            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">

                <i class="bi bi-lock me-2"></i>
                Actualizar contraseña

            </button>

        </form>

    @endif

    @if ($step > 1)
        <button type="button" wire:click="restart" wire:loading.attr="disabled"
            class="btn btn-link text-secondary
                   text-decoration-none px-0 mt-3">

            Cancelar procedimiento

        </button>
    @endif

</section>
