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
    public string $rol = 'paciente';
    public string $estado = 'activo';
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

        $user = User::create($validated);

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="bg-light">

    <main class="container min-vh-100 d-flex align-items-center py-5">

        <div class="row w-100 justify-content-center">

            <div class="col-lg-6 col-md-8 mx-auto">

                {{-- Encabezado --}}
                <div class="text-center mb-4">

                    <i class="bi bi-person-plus display-4 text-primary"></i>

                    <h1 class="display-5 fw-bold mt-3">
                        Laboratorios Carvajal
                    </h1>

                    <p class="lead text-muted">
                        Crea tu cuenta para acceder al sistema de
                        agendamiento y gestionar tus citas médicas.
                    </p>

                </div>


                {{-- Tarjeta de registro --}}
                <div class="bg-white p-4 shadow-lg rounded-3 px-5 py-4">

                    <div class="text-center mb-4">

                        <h2 class="fw-bold">
                            Crear cuenta
                        </h2>

                        <p class="text-muted mb-0">
                            Regístrate como usuario del sistema
                        </p>

                    </div>


                    <form wire:submit="register">

                        {{-- Nombre --}}
                        <div class="mb-3">

                            <label for="name" class="form-label">

                                <i class="bi bi-person"></i>
                                Nombre

                            </label>

                            <input wire:model="name" id="name" type="text" name="name"
                                class="form-control form-control-lg" required autofocus autocomplete="name">

                            <x-input-error :messages="$errors->get('name')" class="mt-2" />

                        </div>
                        {{-- apellido --}}
                        <div class="mb-3">

                            <label for="last_name" class="form-label">

                                <i class="bi bi-person"></i>
                                Apellido

                            </label>

                            <input wire:model="last_name" id="last_name" type="text" name="last_name"
                                class="form-control form-control-lg" required autofocus autocomplete="last_name">

                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />

                        </div>

                        {{-- Correo --}}
                        <div class="mb-3">

                            <label for="email" class="form-label">

                                <i class="bi bi-envelope"></i>
                                Correo electrónico

                            </label>

                            <input wire:model="email" id="email" type="email" name="email"
                                class="form-control form-control-lg" required autocomplete="username">

                            <x-input-error :messages="$errors->get('email')" class="mt-2" />

                        </div>


                        {{-- Contraseña --}}
                        <div class="mb-3">

                            <label for="password" class="form-label">

                                <i class="bi bi-lock"></i>
                                Contraseña

                            </label>

                            <input wire:model="password" id="password" type="password" name="password"
                                class="form-control form-control-lg" required autocomplete="new-password">

                            <x-input-error :messages="$errors->get('password')" class="mt-2" />

                        </div>


                        {{-- Confirmar contraseña --}}
                        <div class="mb-3">

                            <label for="password_confirmation" class="form-label">

                                <i class="bi bi-lock-fill"></i>
                                Confirmar contraseña

                            </label>

                            <input wire:model="password_confirmation" id="password_confirmation" type="password"
                                name="password_confirmation" class="form-control form-control-lg" required
                                autocomplete="new-password">

                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />

                        </div>


                        {{-- Botón --}}
                        <button type="submit" class="btn btn-primary btn-lg w-100">

                            <i class="bi bi-person-plus me-2"></i>

                            Crear cuenta

                        </button>


                        {{-- Login --}}
                        <div class="text-center mt-4">

                            <span class="text-muted">
                                ¿Ya tienes una cuenta?
                            </span>

                            <a href="{{ route('login') }}" wire:navigate class="text-decoration-none fw-semibold ms-1">
                                Iniciar sesión
                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </main>

</div>
