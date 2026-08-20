<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
};
?>

<div class="min-vh-100 bg-light d-flex align-items-center justify-content-center py-5">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-12 col-md-8 col-lg-6">

                {{-- Encabezado --}}
                <div class="text-center mb-4">

                    <i class="bi bi-envelope-check display-4 text-primary">
                    </i>

                    <h1 class="fw-bold mt-3" style="color: #0a2558;">

                        Verifica tu correo electrónico

                    </h1>

                    <p class="text-muted mb-0">

                        Confirma tu dirección de correo para
                        completar el registro de tu cuenta.

                    </p>

                </div>


                {{-- Tarjeta --}}
                <div class="card border-0 shadow-lg rounded-4">

                    <div class="card-body px-4 px-md-5 py-5">

                        {{-- Mensaje principal --}}
                        <div class="text-center mb-4">

                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                                style="
                                    width: 70px;
                                    height: 70px;
                                    background-color: #e7f5ff;
                                ">

                                <i class="bi bi-envelope-open"
                                    style="
                                        font-size: 2rem;
                                        color: #0077b6;
                                    ">
                                </i>

                            </div>

                            <p class="text-muted mb-0">

                                Gracias por registrarte.

                                Antes de comenzar, revisa tu correo
                                electrónico y haz clic en el enlace de
                                verificación que te enviamos.

                            </p>

                        </div>


                        {{-- Mensaje de enlace enviado --}}
                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success text-center rounded-3" role="alert">

                                <i class="bi bi-check-circle me-1"></i>

                                Se ha enviado un nuevo enlace de
                                verificación a tu correo electrónico.

                            </div>
                        @endif


                        {{-- Botones --}}
                        <div class="d-flex flex-column gap-3 mt-4">

                            <button type="button" wire:click="sendVerification"
                                class="btn btn-primary btn-lg rounded-3 fw-bold">

                                <i class="bi bi-envelope-arrow-up me-2"></i>

                                Reenviar correo de verificación

                            </button>


                            <button type="button" wire:click="logout"
                                class="btn btn-outline-secondary btn-lg rounded-3">

                                <i class="bi bi-box-arrow-left me-2"></i>

                                Cerrar sesión

                            </button>

                        </div>

                    </div>

                </div>


                {{-- Nota --}}
                <div class="text-center mt-4">

                    <small class="text-muted">

                        <i class="bi bi-shield-check me-1"></i>

                        La verificación ayuda a proteger tu cuenta
                        y mantener tus datos seguros.

                    </small>

                </div>

            </div>

        </div>

    </div>

</div>
