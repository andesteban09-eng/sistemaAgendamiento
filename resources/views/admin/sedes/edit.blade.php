@extends('layouts.admin')

@section('contenido')
    <div class="sigac-page">

        <x-sigac.page-header title="Editar sede" subtitle="Actualiza la información de la ubicación registrada."
            eyebrow="SIGAC · GESTIÓN DE SEDES">

            <x-slot:actions>

                <a href="{{ route('admin.sedes.index') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        {{-- Identificación de la sede --}}

        <x-sigac.card>

            <div class="sigac-identity">

                <div class="sigac-icon sigac-icon-info">

                    <i class="bi bi-building" aria-hidden="true"></i>

                </div>

                <div class="sigac-identity-content">

                    <h2>{{ $sede->nombre }}</h2>

                    <p class="sigac-description mb-0">
                        Sede #{{ $sede->idsede }}
                    </p>

                </div>

                @php
                    $varianteEstado = $sede->estadosede === 'Activo' ? 'success' : 'neutral';
                @endphp

                <x-sigac.status :label="$sede->estadosede" :variant="$varianteEstado" />

            </div>

        </x-sigac.card>

        {{-- Formulario de actualización --}}

        <x-sigac.card title="Información de la sede"
            subtitle="Modifica los datos correspondientes a la ubicación. Todos los campos son obligatorios."
            class="sigac-form-card">

            <form action="{{ route('admin.sedes.update', $sede) }}" method="POST" class="sigac-form">

                @csrf
                @method('PUT')

                @include('admin.sedes.partials.form', [
                    'sede' => $sede,
                ])

                <x-sigac.alert variant="info">

                    La actualización conservará el identificador,
                    el estado y los horarios asociados a esta sede.

                    Los cambios en la información de ubicación
                    también se reflejarán en las consultas de
                    horarios y citas vinculados a ella.

                </x-sigac.alert>

                <div class="sigac-form-actions">

                    <a href="{{ route('admin.sedes.index') }}" class="btn btn-outline-secondary">

                        Cancelar

                    </a>

                    <button type="submit" class="btn sigac-btn-primary">

                        <i class="bi bi-floppy"></i>
                        Guardar cambios

                    </button>

                </div>

            </form>

        </x-sigac.card>

    </div>
@endsection
