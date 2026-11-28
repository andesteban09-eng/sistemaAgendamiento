@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Editar profesional"
            subtitle="Actualiza los datos personales, profesionales y servicios asignados."
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

        <form method="POST" action="{{ route('admin.profesionales.update', $profesional) }}"
            class="sigac-form sigac-form-card">

            @csrf
            @method('PUT')

            @include('admin.profesionales.partials.form', [
                'profesional' => $profesional,
                'serviciosAsignados' => $serviciosAsignados,
            ])

            <div class="sigac-form-actions">

                <a href="{{ route('admin.profesionales.index') }}" class="btn btn-outline-secondary">

                    Cancelar

                </a>

                <button type="submit" class="btn sigac-btn-primary">

                    <i class="bi bi-floppy"></i>
                    Guardar cambios

                </button>

            </div>

        </form>

    </div>

@endsection
