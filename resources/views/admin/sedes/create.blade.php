@extends('layouts.admin')

@section('contenido')
    <div class="sigac-page">

        <x-sigac.page-header title="Registrar sede" subtitle="Agrega una nueva ubicación de atención."
            eyebrow="SIGAC · GESTIÓN DE SEDES">

            <x-slot:actions>

                <a href="{{ route('admin.sedes.index') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        <x-sigac.card title="Información de la sede"
            subtitle="Completa los datos correspondientes a la ubicación. Todos los campos son obligatorios."
            class="sigac-form-card">

            <form action="{{ route('admin.sedes.store') }}" method="POST" class="sigac-form">

                @csrf

                @include('admin.sedes.partials.form', [
                    'sede' => null,
                ])

                <x-sigac.alert variant="info">

                    La sede se registrará con estado activo
                    y estará disponible para la asignación
                    de horarios.

                </x-sigac.alert>

                <div class="sigac-form-actions">

                    <a href="{{ route('admin.sedes.index') }}" class="btn btn-outline-secondary">

                        Cancelar

                    </a>

                    <button type="submit" class="btn sigac-btn-primary">

                        <i class="bi bi-floppy"></i>
                        Registrar sede

                    </button>

                </div>

            </form>

        </x-sigac.card>

    </div>
@endsection
