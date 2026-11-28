@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Editar auxiliar" subtitle="Actualiza la información personal y de acceso del auxiliar."
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

        <form method="POST" action="{{ route('admin.auxiliares.update', $auxiliar) }}" class="sigac-form sigac-form-card">

            @csrf
            @method('PUT')

            @include('admin.auxiliares.partials.form', [
                'auxiliar' => $auxiliar,
            ])

            <div class="sigac-form-actions">

                <a href="{{ route('admin.auxiliares.index') }}" class="btn btn-outline-secondary">

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
