@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Registrar auxiliar" subtitle="Registra un nuevo auxiliar en el sistema."
            eyebrow="SIGAC · GESTIÓN DE AUXILIARES">

            <x-slot:actions>

                <a href="{{ route('admin.auxiliares.index') }}" class="btn sigac-btn-light">

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

        <form method="POST" action="{{ route('admin.auxiliares.store') }}" class="sigac-form sigac-form-card">

            @csrf

            @include('admin.auxiliares.partials.form', [
                'auxiliar' => null,
            ])

            <div class="sigac-form-actions">

                <a href="{{ route('admin.auxiliares.index') }}" class="btn btn-outline-secondary">

                    Cancelar

                </a>

                <button type="submit" class="btn sigac-btn-primary">

                    <i class="bi bi-person-plus"></i>
                    Registrar auxiliar

                </button>

            </div>

        </form>

    </div>

@endsection
