@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Editar servicio" subtitle="Actualiza la información y las condiciones del servicio."
            eyebrow="SIGAC · GESTIÓN DE SERVICIOS">

            <x-slot:actions>

                <a href="{{ route('admin.servicios.index') }}" class="btn sigac-btn-light">

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

        <form method="POST" action="{{ route('admin.servicios.update', ['servicio' => $servicio->idservicio]) }}"
            class="sigac-form sigac-form-card">

            @csrf
            @method('PUT')

            @include('admin.servicios.partials.form', [
                'servicio' => $servicio,
            ])

            <div class="sigac-form-actions">

                <a href="{{ route('admin.servicios.index') }}" class="btn btn-outline-secondary">

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
