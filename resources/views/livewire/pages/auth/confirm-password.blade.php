<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (
            !Auth::guard('web')->validate([
                'email' => Auth::user()->email,
                'password' => $this->password,
            ])
        ) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="bg-light">

    @include('partials.menu')

    <main class="container min-vh-100 d-flex align-items-center py-5">

        <div class="row w-100 justify-content-center">

            <div class="col-lg-6 col-md-8 mx-auto">

                {{-- Encabezado --}}
                <div class="text-center mb-4">

                    <i class="bi bi-shield-lock display-4 text-primary"></i>

                    <h1 class="display-5 fw-bold mt-3">
                        Confirmar contraseña
                    </h1>

                    <p class="lead text-muted">
                        Esta es un área segura del sistema.
                        Confirma tu contraseña para continuar.
                    </p>

                </div>

                {{-- Tarjeta --}}
                <div class="bg-white p-4 shadow-lg rounded-3 px-5 py-4">

                    <div class="text-center mb-4">

                        <h2 class="fw-bold">
                            Verificación de seguridad
                        </h2>

                        <p class="text-muted mb-0">
                            Por seguridad, necesitamos confirmar tu identidad.
                        </p>

                    </div>

                    {{-- Formulario --}}
                    <form wire:submit="confirmPassword">

                        {{-- Contraseña --}}
                        <div class="mb-4">

                            <label for="password" class="form-label">

                                <i class="bi bi-lock me-1"></i>
                                Contraseña

                            </label>

                            <input wire:model="password" id="password" type="password" name="password"
                                class="form-control form-control-lg" required autofocus autocomplete="current-password">

                            <x-input-error :messages="$errors->get('password')" class="mt-2" />

                        </div>

                        {{-- Botón --}}
                        <button type="submit" class="btn btn-primary btn-lg w-100">

                            <i class="bi bi-check-circle me-2"></i>
                            Confirmar contraseña

                        </button>

                    </form>

                    <div class="text-center mt-4">

                        <small class="text-muted">

                            <i class="bi bi-shield-check me-1"></i>
                            Tu información está protegida.

                        </small>

                    </div>

                </div>

            </div>

        </div>

    </main>

    @include('partials.footer')

</div>
