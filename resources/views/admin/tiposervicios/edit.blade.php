@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Editar tipo de servicio" subtitle="Modifica la información de la categoría de servicios."
            eyebrow="SIGAC · GESTIÓN DE TIPOS DE SERVICIO">

            <x-slot:actions>

                <a href="{{ route('admin.tiposervicios.index') }}" class="btn sigac-btn-light">

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

        <form action="{{ route('admin.tiposervicios.update', $tipoServicio) }}" method="POST"
            class="sigac-form sigac-form-card">

            @csrf
            @method('PUT')

            @include('admin.tiposervicios.partials.form', [
                'tipoServicio' => $tipoServicio,
            ])

            <div class="sigac-form-actions">

                <a href="{{ route('admin.tiposervicios.index') }}" class="btn btn-outline-secondary">

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
