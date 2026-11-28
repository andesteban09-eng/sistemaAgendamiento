<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['rol'] = 'paciente';
        $validated['estado'] = 'activo';

        $user = User::create($validated);

        Auth::login($user);

        session()->regenerate();

        $this->redirectRoute('verification.notice', navigate: false);
    }
}; ?>
<div class="sigac-auth-page sigac-auth-page-register">
    <main class="sigac-auth-layout" aria-labelledby="sigac-auth-title">
        {{-- Presentación: únicamente contenido visual. --}}
        <section class="sigac-auth-intro">
            <div class="sigac-auth-icon" aria-hidden="true">
                <i class="bi bi-person-plus"></i>
            </div>

            <h1 id="sigac-auth-title">Únete a <span>SIGAC</span></h1>
            <p>Crea tu cuenta y comienza a gestionar tus citas médicas.</p>
        </section>

        <section class="sigac-auth-card" aria-labelledby="sigac-register-title">
            <header class="sigac-auth-card-heading">
                <h2 id="sigac-register-title">Crear cuenta</h2>
                <p>Completa los siguientes datos para registrarte.</p>
            </header>

            {{-- Se conserva el flujo existente de Livewire/Volt. --}}
            <form wire:submit="register">
                <div class="sigac-auth-fields">
                    <div class="sigac-auth-field">
                        <label for="name" class="form-label">
                            <i class="bi bi-person" aria-hidden="true"></i>
                            Nombre
                        </label>
                        <input wire:model="name" id="name" type="text" name="name" class="form-control"
                            required autofocus autocomplete="name">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="sigac-auth-field">
                        <label for="last_name" class="form-label">
                            <i class="bi bi-person" aria-hidden="true"></i>
                            Apellido
                        </label>
                        <input wire:model="last_name" id="last_name" type="text" name="last_name"
                            class="form-control" required autocomplete="family-name">
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>

                    <div class="sigac-auth-field sigac-auth-full">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope" aria-hidden="true"></i>
                            Correo electrónico
                        </label>
                        <input wire:model="email" id="email" type="email" name="email" class="form-control"
                            required autocomplete="username">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="sigac-auth-field">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock" aria-hidden="true"></i>
                            Contraseña
                        </label>
                        <input wire:model="password" id="password" type="password" name="password" class="form-control"
                            required autocomplete="new-password">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="sigac-auth-field">
                        <label for="password_confirmation" class="form-label">
                            <i class="bi bi-lock-fill" aria-hidden="true"></i>
                            Confirmar contraseña
                        </label>
                        <input wire:model="password_confirmation" id="password_confirmation" type="password"
                            name="password_confirmation" class="form-control" required autocomplete="new-password">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <button type="submit" class="btn btn-primary sigac-auth-submit w-100">
                    <i class="bi bi-person-plus me-2" aria-hidden="true"></i>
                    Crear cuenta
                </button>

                <div class="sigac-auth-links">
                    <span>¿Ya tienes una cuenta?</span>
                    <a href="{{ route('login') }}" wire:navigate>Iniciar sesión</a>
                </div>
            </form>
        </section>
    </main>
</div>
