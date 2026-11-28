@extends('layouts.admin')

@section('contenido')
    @php
        $varianteEstado = $paciente->estadopaciente === 'Activo' ? 'success' : 'neutral';
    @endphp

    <div class="sigac-page">

        <x-sigac.page-header title="Información del paciente" subtitle="Consulta detallada del paciente registrado."
            eyebrow="SIGAC · GESTIÓN DE PACIENTES">

            <x-slot:actions>

                <a href="{{ route('admin.pacientes.index') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver

                </a>

                <a href="{{ route('admin.pacientes.edit', $paciente) }}" class="btn sigac-btn-light">

                    <i class="bi bi-pencil"></i>
                    Editar paciente

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        {{-- Información personal --}}

        <x-sigac.card>

            <div class="sigac-identity">

                <div class="sigac-icon sigac-icon-info">

                    <i class="bi bi-person" aria-hidden="true"></i>

                </div>

                <div class="sigac-identity-content">

                    <h2>
                        {{ $paciente->user?->name }}
                        {{ $paciente->user?->last_name }}
                    </h2>

                    <p class="sigac-description mb-0">
                        Paciente #{{ $paciente->idpaciente }}
                    </p>

                </div>

                <x-sigac.status :label="$paciente->estadopaciente" :variant="$varianteEstado" />

            </div>

            <hr>

            <dl class="sigac-details">

                <div>

                    <dt>Nombre completo</dt>

                    <dd>
                        {{ $paciente->user?->name }}
                        {{ $paciente->user?->last_name }}
                    </dd>

                </div>

                <div>

                    <dt>Correo electrónico</dt>

                    <dd>{{ $paciente->user?->email }}</dd>

                </div>

                <div>

                    <dt>Tipo de documento</dt>

                    <dd>{{ $paciente->tipodoc }}</dd>

                </div>

                <div>

                    <dt>Número de documento</dt>

                    <dd>{{ $paciente->numdoc }}</dd>

                </div>

            </dl>

        </x-sigac.card>

        {{-- Información de contacto --}}

        <x-sigac.card title="Información de contacto"
            subtitle="Datos de contacto registrados por el paciente o el administrador.">

            <dl class="sigac-details">

                <div>

                    <dt>Teléfono</dt>

                    <dd>{{ $paciente->telefono }}</dd>

                </div>

                <div>

                    <dt>Ciudad</dt>

                    <dd>{{ $paciente->ciudad }}</dd>

                </div>

                <div class="sigac-details-full">

                    <dt>Dirección</dt>

                    <dd>{{ $paciente->direccion }}</dd>

                </div>

            </dl>

        </x-sigac.card>

        {{-- Información del registro --}}

        <x-sigac.card title="Información del registro" subtitle="Identificadores y datos de registro en SIGAC.">

            <dl class="sigac-details">

                <div>

                    <dt>ID paciente</dt>

                    <dd>{{ $paciente->idpaciente }}</dd>

                </div>

                <div>

                    <dt>ID usuario</dt>

                    <dd>{{ $paciente->idusuario }}</dd>

                </div>

                <div>

                    <dt>Fecha de registro</dt>

                    <dd>
                        {{ $paciente->fecharegistro ?? 'No registrada' }}
                    </dd>

                </div>

                <div>

                    <dt>Estado del paciente</dt>

                    <dd>

                        <x-sigac.status :label="$paciente->estadopaciente" :variant="$varianteEstado" />

                    </dd>

                </div>

            </dl>

        </x-sigac.card>

    </div>
@endsection
