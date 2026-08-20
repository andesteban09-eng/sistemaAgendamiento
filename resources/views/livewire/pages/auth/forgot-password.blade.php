<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="bg-light min-vh-100 d-flex align-items-center justify-content-center">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-12 col-md-8 col-lg-5">

                {{-- Encabezado --}}
                <div class="text-center mb-4">

                    <i class="bi bi-envelope-open display-4 text-primary"></i>

                    <h1 class="fw-bold mt-3">
                        Recuperar contraseña
                    </h1>

                    <p class="text-muted">
                        Ingresa tu correo electrónico y te enviaremos
                        un enlace para restablecer tu contraseña.
                    </p>

                </div>

                {{-- Tarjeta --}}
                <div class="bg-white shadow-lg rounded-3 p-4 px-md-5 py-md-5">

                    {{-- Estado --}}
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form wire:submit="sendPasswordResetLink">

                        {{-- Correo --}}
                        <div class="mb-4">

                            <label for="email" class="form-label fw-semibold">

                                <i class="bi bi-envelope me-1"></i>
                                Correo electrónico

                            </label>

                            <input wire:model="email" id="email" type="email" name="email"
                                class="form-control form-control-lg" placeholder="correo@ejemplo.com" required autofocus
                                autocomplete="email">

                            <x-input-error :messages="$errors->get('email')" class="mt-2" />

                        </div>

                        {{-- Botón --}}
                        <button type="submit" class="btn btn-primary btn-lg w-100">

                            <i class="bi bi-send me-2"></i>
                            Enviar enlace

                        </button>

                    </form>

                    {{-- Regresar --}}
                    <div class="text-center mt-4">

                        <a href="{{ route('login') }}" wire:navigate class="text-decoration-none">

                            <i class="bi bi-arrow-left me-1"></i>
                            Volver a iniciar sesión

                        </a>

                    </div>

                </div>

                {{-- Identidad --}}
                <div class="text-center mt-4">

                    <small class="text-muted">
                        Laboratorios Carvajal IPS
                    </small>

                </div>

            </div>

        </div>

    </div>

</div>
