@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Editar disponibilidad" subtitle="Modifica los datos de la disponibilidad del profesional."
            eyebrow="SIGAC · GESTIÓN DE AGENDA">

            <x-slot:actions>

                <a href="{{ route('auxiliar.agenda.index') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        @if (session('error'))
            <x-sigac.alert variant="danger">
                {{ session('error') }}
            </x-sigac.alert>
        @endif

        @if ($errors->any())
            <x-sigac.alert variant="danger" title="Revisa la información ingresada">

                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

            </x-sigac.alert>
        @endif

        <form action="{{ route('auxiliar.agenda.update', $agenda) }}" method="POST" class="sigac-form sigac-form-card">

            @csrf
            @method('PUT')

            @include('auxiliar.agenda.partials.form', [
                'agenda' => $agenda,
            ])

            <div class="sigac-form-actions">

                <a href="{{ route('auxiliar.agenda.index') }}" class="btn btn-outline-secondary">

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
