@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Registrar profesional"
            subtitle="Registra los datos del profesional y los servicios que puede prestar."
            eyebrow="SIGAC · GESTIÓN DE PROFESIONALES">

            <x-slot:actions>

                <a href="{{ route('admin.profesionales.index') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        @if ($errors->any())
            <x-sigac.alert variant="danger" title="Revisa la información ingresada">

                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

            </x-sigac.alert>
        @endif

        <form method="POST" action="{{ route('admin.profesionales.store') }}" class="sigac-form sigac-form-card">

            @csrf

            @include('admin.profesionales.partials.form', [
                'profesional' => null,
                'serviciosAsignados' => [],
            ])

            <div class="sigac-form-actions">

                <a href="{{ route('admin.profesionales.index') }}" class="btn btn-outline-secondary">

                    Cancelar

                </a>

                <button type="submit" class="btn sigac-btn-primary">

                    <i class="bi bi-person-plus"></i>
                    Registrar profesional

                </button>

            </div>

        </form>

    </div>

@endsection
