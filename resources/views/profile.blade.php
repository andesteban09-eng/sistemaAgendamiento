@extends('layouts.app')

@section('contenido')

    <main class="container py-5">

        <div class="mb-4">
            <h2 class="fw-bold">
                <i class="bi bi-person-circle me-2"></i>
                Mi perfil
            </h2>

            <p class="text-muted">
                Gestiona la información y seguridad de tu cuenta.
            </p>
        </div>

        {{-- Información personal --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        {{-- Actualización de contraseña --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <livewire:profile.update-password-form />
            </div>
        </div>

        {{-- Eliminación de cuenta --}}
        <div class="card shadow-sm border-danger mb-4">
            <div class="card-body p-4">
                <livewire:profile.delete-user-form />
            </div>
        </div>

    </main>

@endsection
