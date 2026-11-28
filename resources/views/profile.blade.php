@extends('layouts.dashboard')

@section('contenido')

    <main class="sigac-profile-page">

        <div class="sigac-profile-container">

            <header class="sigac-profile-banner">

                <div class="sigac-profile-avatar">
                    <i class="bi bi-person"></i>
                </div>

                <div>
                    <span class="sigac-profile-eyebrow">
                        MI CUENTA
                    </span>

                    <h2>Mi perfil</h2>

                    <p>
                        Gestiona tu información personal
                        y la seguridad de tu cuenta.
                    </p>
                </div>

            </header>

            <div class="sigac-profile-card">
                <livewire:profile.update-profile-information-form />
            </div>

            <div class="sigac-profile-card">
                <livewire:profile.update-password-form />
            </div>

        </div>

    </main>
@endsection
