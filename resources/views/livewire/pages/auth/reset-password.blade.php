<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    #[Locked]
    public string $token = '';

    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset($this->only('email', 'password', 'password_confirmation', 'token'), function ($user) {
            $user
                ->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])
                ->save();

            event(new PasswordReset($user));
        });

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
};
?>

<div class="min-vh-100 bg-light d-flex align-items-center justify-content-center py-5">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-12 col-md-8 col-lg-5">

                {{-- Encabezado --}}
                <div class="text-center mb-4">

                    <i class="bi bi-shield-lock display-4 text-primary">
                    </i>

                    <h1 class="fw-bold mt-3" style="color: #0a2558;">
                        Restablecer contraseña
                    </h1>

                    <p class="text-muted mb-0">
                        Crea una nueva contraseña para recuperar
                        el acceso a tu cuenta.
                    </p>

                </div>


                {{-- Tarjeta --}}
                <div class="card border-0 shadow-lg rounded-4">

                    <div class="card-body px-4 px-md-5 py-5">

                        <form wire:submit="resetPassword">

                            {{-- Correo --}}
                            <div class="mb-4">

                                <label for="email" class="form-label fw-semibold" style="color: #0a2558;">

                                    <i class="bi bi-envelope me-1"></i>
                                    Correo electrónico

                                </label>

                                <input wire:model="email" id="email" type="email" name="email"
                                    class="form-control form-control-lg rounded-3" required autofocus
                                    autocomplete="username">

                                <x-input-error :messages="$errors->get('email')" class="mt-2" />

                            </div>


                            {{-- Nueva contraseña --}}
                            <div class="mb-4">

                                <label for="password" class="form-label fw-semibold" style="color: #0a2558;">

                                    <i class="bi bi-lock me-1"></i>
                                    Nueva contraseña

                                </label>

                                <input wire:model="password" id="password" type="password" name="password"
                                    class="form-control form-control-lg rounded-3" required autocomplete="new-password">

                                <x-input-error :messages="$errors->get('password')" class="mt-2" />

                            </div>


                            {{-- Confirmar contraseña --}}
                            <div class="mb-4">

                                <label for="password_confirmation" class="form-label fw-semibold"
                                    style="color: #0a2558;">

                                    <i class="bi bi-lock-fill me-1"></i>
                                    Confirmar contraseña

                                </label>

                                <input wire:model="password_confirmation" id="password_confirmation" type="password"
                                    name="password_confirmation" class="form-control form-control-lg rounded-3" required
                                    autocomplete="new-password">

                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />

                            </div>


                            {{-- Botón --}}
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fw-bold">

                                <i class="bi bi-key me-2"></i>

                                Restablecer contraseña

                            </button>


                            {{-- Volver al login --}}
                            <div class="text-center mt-4">

                                <a href="{{ route('login') }}" wire:navigate class="text-decoration-none fw-semibold"
                                    style="color: #0077b6;">

                                    <i class="bi bi-arrow-left me-1"></i>

                                    Volver a iniciar sesión

                                </a>

                            </div>

                        </form>

                    </div>

                </div>


                {{-- Mensaje de seguridad --}}
                <div class="text-center mt-4">

                    <small class="text-muted">

                        <i class="bi bi-shield-check me-1"></i>

                        Tu nueva contraseña se almacenará de forma segura.

                    </small>

                </div>

            </div>

        </div>

    </div>

</div>
