<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        if (!Auth::user()->hasVerifiedEmail()) {
            $this->redirectRoute('verification.notice', navigate: false);

            return;
        }

        $this->redirectIntended(default: route('dashboard'), navigate: true);
    }
}; ?>

<div class="sigac-auth-page">
    <main class="sigac-auth-layout" aria-labelledby="sigac-auth-title">
        {{-- Presentación: únicamente contenido visual. --}}
        <section class="sigac-auth-intro">

            <div class="sigac-auth-icon" aria-hidden="true">
                <i class="bi bi-calendar2-check"></i>
            </div>

            <h1 id="sigac-auth-title">Bienvenido a <span>SIGAC</span></h1>
            <p>Consulta y gestiona tus citas médicas desde un solo lugar.</p>
        </section>

        <section class="sigac-auth-card" aria-labelledby="sigac-login-title">
            <header class="sigac-auth-card-heading">
                <h2 id="sigac-login-title">Iniciar sesión</h2>
                <p>Ingresa tus credenciales para continuar.</p>
            </header>

            {{-- Se conserva el flujo existente de Livewire/Volt. --}}
            <form wire:submit="login">
                <x-auth-session-status class="sigac-auth-status" :status="session('status')" />

                <div class="sigac-auth-field">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope" aria-hidden="true"></i>
                        Correo electrónico
                    </label>
                    <input wire:model="form.email" type="email" id="email" name="email" class="form-control"
                        required autofocus autocomplete="username">
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>

                <div class="sigac-auth-field">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock" aria-hidden="true"></i>
                        Contraseña
                    </label>
                    <input wire:model="form.password" type="password" id="password" name="password"
                        class="form-control" required autocomplete="current-password">
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>

                <div class="sigac-auth-remember form-check">
                    <input wire:model="form.remember" type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Recuérdame</label>
                </div>

                <button type="submit" class="btn btn-primary sigac-auth-submit w-100">
                    <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>
                    Iniciar sesión
                </button>

                <div class="sigac-auth-links sigac-auth-links-between">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" wire:navigate>Crear cuenta</a>
                    @endif

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate>¿Olvidaste tu contraseña?</a>
                    @endif
                </div>
            </form>
        </section>
    </main>
</div>
