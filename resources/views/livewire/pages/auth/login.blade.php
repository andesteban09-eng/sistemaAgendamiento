<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

new class extends Component {
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }
}; ?>
<div class="bg-light">

    <main class="container min-vh-100 d-flex align-items-center py-5">

        <div class="row w-100 justify-content-center">

            <div class="col-lg-6 col-md-8 mx-auto">

                <div class="text-center mb-4">

                    <i class="bi bi-calendar2-check display-4 text-primary"></i>

                    <h1 class="display-5 fw-bold mt-3">
                        Laboratorios Carvajal
                    </h1>

                    <p class="lead text-muted">
                        Acceso para usuarios registrados. Por favor,
                        ingresa tus credenciales para iniciar sesión
                        y gestionar tus citas médicas.
                    </p>

                </div>

                {{-- Login --}}
                <form wire:submit="login" class="bg-white p-4 shadow-lg rounded-3 px-5 py-4">

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <div class="mb-3">

                        <label for="email" class="form-label">

                            <i class="bi bi-envelope"></i>
                            Correo electrónico

                        </label>

                        <input wire:model="form.email" type="email" id="email" name="email"
                            class="form-control form-control-lg" required autofocus autocomplete="username">

                        <x-input-error :messages="$errors->get('form.email')" class="mt-2" />

                    </div>

                    <div class="mb-3">

                        <label for="password" class="form-label">

                            <i class="bi bi-lock"></i>
                            Contraseña

                        </label>

                        <input wire:model="form.password" type="password" id="password" name="password"
                            class="form-control form-control-lg" required autocomplete="current-password">

                        <x-input-error :messages="$errors->get('form.password')" class="mt-2" />

                    </div>

                    <div class="mb-3 form-check">

                        <input wire:model="form.remember" type="checkbox" class="form-check-input" id="remember">

                        <label class="form-check-label" for="remember">

                            Recuérdame

                        </label>

                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">

                        Iniciar sesión

                    </button>

                    <div class="d-flex justify-content-between mt-3">

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" wire:navigate class="text-decoration-none">

                                Crear cuenta

                            </a>
                        @endif

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" wire:navigate class="text-decoration-none">

                                ¿Olvidaste tu contraseña?

                            </a>
                        @endif

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>
