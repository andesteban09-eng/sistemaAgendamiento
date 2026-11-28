<?php

use App\Models\User;
use App\Models\OtpChallenge;
use App\Livewire\Actions\Logout;
use App\Services\Security\OtpService;
use App\Services\Security\OtpRateLimiter;
use App\Services\Security\PasswordSecurityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    private const SESSION_KEY = 'security.email_verification';

    #[Locked]
    public bool $codeSent = false;

    public string $otp = '';

    public function mount(): void
    {
        $user = $this->authenticatedUser();

        if ($user->email_verified_at !== null) {
            $this->redirectRoute('dashboard', navigate: false);

            return;
        }

        $state = session()->get(self::SESSION_KEY);

        $this->codeSent = (string) ($state['user_id'] ?? '') === (string) $user->getKey() && !empty($state['challenge_id']);
    }

    public function sendVerification(): void
    {
        $this->resetValidation();
        $this->reset('otp');

        $user = $this->authenticatedUser();

        if ($user->email_verified_at !== null) {
            $this->redirectRoute('dashboard', navigate: false);

            return;
        }

        if (!app(OtpRateLimiter::class)->allowIssue(request()->ip())) {
            $this->addError('request', 'Demasiadas solicitudes. Intenta más tarde.');

            return;
        }

        try {
            $challenge = app(OtpService::class)->issue($user, OtpChallenge::PURPOSE_EMAIL_VERIFICATION, session()->getId());

            session()->put(self::SESSION_KEY, [
                'user_id' => $user->getKey(),
                'challenge_id' => $challenge->getKey(),
            ]);

            $this->codeSent = true;

            session()->flash('verification_status', 'Hemos enviado un código de seguridad a tu correo.');
        } catch (ValidationException $exception) {
            $this->addError('request', $exception->errors()['otp'][0] ?? 'No fue posible enviar el código.');
        }
    }

    public function verifyOtp(): void
    {
        $this->resetValidation();

        try {
            $user = $this->authenticatedUser();

            if ($user->email_verified_at !== null) {
                $this->redirectRoute('dashboard', navigate: false);

                return;
            }

            if (!app(OtpRateLimiter::class)->allowVerification(request()->ip())) {
                $this->addError('otp', 'Demasiados intentos. Intenta más tarde.');

                return;
            }

            $this->validate([
                'otp' => ['required', 'regex:/\A[0-9]{6}\z/'],
            ]);

            $state = session()->get(self::SESSION_KEY);

            if ((string) ($state['user_id'] ?? '') !== (string) $user->getKey() || empty($state['challenge_id'])) {
                $this->addError('otp', 'Solicita un código de seguridad.');

                return;
            }

            $challengeId = $state['challenge_id'];
            $sessionId = session()->getId();

            $verified = app(OtpService::class)->verify($user, $challengeId, OtpChallenge::PURPOSE_EMAIL_VERIFICATION, $sessionId, $this->otp);

            if (!$verified) {
                $this->addError('otp', 'El código no es válido o ha expirado.');

                return;
            }

            $completed = app(PasswordSecurityService::class)->verifyEmail($user, $challengeId, $sessionId);

            if (!$completed) {
                $this->addError('otp', 'No fue posible completar la verificación. Solicita otro código.');

                return;
            }

            session()->forget(self::SESSION_KEY);

            session()->regenerate();

            session()->flash('status', 'Tu correo electrónico ha sido verificado correctamente.');

            $this->redirectRoute('dashboard', navigate: false);
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());
        } finally {
            $this->reset('otp');
        }
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirectRoute('login', navigate: false);
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        $user = $user->fresh();

        abort_unless($user !== null && $user->estado === 'activo', 403);

        return $user;
    }
};
?>


<div class="sigac-auth-page sigac-auth-page-recovery sigac-auth-page-verification">

    <main class="sigac-auth-layout">

        {{-- Presentación --}}
        <section class="sigac-auth-intro">

            <span class="sigac-auth-eyebrow">
                SEGURIDAD DE TU CUENTA
            </span>

            <div class="sigac-auth-icon" aria-hidden="true">
                <i class="bi bi-envelope-check"></i>
            </div>

            <h1>
                Verifica tu
                <span>correo electrónico.</span>
            </h1>

            <p>
                Confirma tu dirección de correo para
                proteger tu cuenta y habilitar la
                recuperación de contraseña.
            </p>

        </section>

        {{-- Tarjeta --}}
        <section class="sigac-auth-card">

            <header class="sigac-auth-card-heading">

                <h2>
                    {{ $codeSent ? 'Introduce el código' : 'Verificación de correo' }}
                </h2>

                <p>
                    @if ($codeSent)
                        Introduce los seis dígitos
                        enviados a tu correo electrónico.
                    @else
                        Envía un código de seguridad
                        al correo asociado a tu cuenta.
                    @endif
                </p>

            </header>

            {{-- Correo del usuario autenticado --}}
            <div class="sigac-recovery-email">

                <i class="bi bi-envelope"></i>

                <span>
                    {{ auth()->user()->email }}
                </span>

            </div>

            {{-- Estado del envío --}}
            @if (session('verification_status'))
                <div class="alert alert-success small py-2" role="status">

                    <i class="bi bi-check-circle me-1"></i>

                    {{ session('verification_status') }}

                </div>
            @endif

            {{-- Errores de solicitud --}}
            @error('request')
                <div class="alert alert-danger small py-2" role="alert">

                    {{ $message }}

                </div>
            @enderror

            @if (!$codeSent)
                {{-- Solicitud inicial --}}
                <button type="button" wire:click="sendVerification" wire:loading.attr="disabled"
                    class="btn sigac-auth-submit w-100">

                    <span wire:loading.remove wire:target="sendVerification">

                        <i class="bi bi-send me-1"></i>
                        Enviar código de seguridad

                    </span>

                    <span wire:loading wire:target="sendVerification">

                        Enviando código...

                    </span>

                </button>
            @else
                {{-- Comprobación del OTP --}}
                <form wire:submit="verifyOtp">

                    <div class="sigac-auth-field">

                        <label for="otp" class="form-label">
                            Código de seguridad
                        </label>

                        <input wire:model="otp" id="otp" type="text" class="form-control sigac-otp-input"
                            inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" minlength="6"
                            maxlength="6" placeholder="000000" required>

                        @error('otp')
                            <small class="text-danger" role="alert">

                                {{ $message }}

                            </small>
                        @enderror

                    </div>

                    <p class="sigac-recovery-hint">

                        <i class="bi bi-clock"></i>

                        El código tiene una vigencia de
                        {{ config('otp.expiration_minutes') }}
                        minutos.

                    </p>

                    <button type="submit" wire:loading.attr="disabled" class="btn sigac-auth-submit w-100">

                        <span wire:loading.remove wire:target="verifyOtp">

                            <i class="bi bi-shield-check me-1"></i>
                            Verificar correo electrónico

                        </span>

                        <span wire:loading wire:target="verifyOtp">

                            Verificando...

                        </span>

                    </button>

                </form>

                {{-- Reenvío --}}
                <div class="sigac-auth-links">

                    <span>¿No recibiste el código?</span>

                    <button type="button" class="sigac-recovery-link" wire:click="sendVerification"
                        wire:loading.attr="disabled">

                        Reenviar código

                    </button>

                </div>

                <p class="sigac-recovery-hint text-center">

                    Espera al menos
                    {{ config('otp.resend_seconds') }}
                    segundos antes de solicitar otro código.

                </p>
            @endif

            {{-- Información de seguridad --}}
            <div class="sigac-recovery-notice">

                <i class="bi bi-shield-lock"></i>

                <span>
                    Nunca compartas tu código de seguridad
                    con otras personas.
                </span>

            </div>

            {{-- Cierre de sesión --}}
            <div class="sigac-auth-links">

                <button type="button" class="sigac-recovery-link" wire:click="logout" wire:loading.attr="disabled">

                    <i class="bi bi-box-arrow-left me-1"></i>
                    Cerrar sesión

                </button>

            </div>

        </section>

    </main>

</div>
