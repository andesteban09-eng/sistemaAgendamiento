@extends('layouts.admin')

@section('contenido')
    @php
        $varianteEstado = match ($cita->estadocita) {
            'Pendiente' => 'warning',
            'Realizada' => 'success',
            'Cancelada' => 'danger',
            default => 'neutral',
        };

        $subtitulo = 'Información del agendamiento #' . $cita->idcita;
    @endphp

    <div class="sigac-page">

        <x-sigac.page-header title="Detalle de la cita" :subtitle="$subtitulo" eyebrow="SIGAC · GESTIÓN DE CITAS">

            <x-slot:actions>

                <a href="{{ route('admin.citas.index') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver al listado

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        {{-- Información de la cita --}}

        <x-sigac.card>

            <div class="sigac-identity">

                <div class="sigac-icon sigac-icon-info">

                    <i class="bi bi-calendar-check" aria-hidden="true"></i>

                </div>

                <div class="sigac-identity-content">

                    <h2>
                        {{ $cita->servicio?->nombre ?? 'Sin información' }}
                    </h2>

                    <p class="sigac-description mb-0">
                        Cita #{{ $cita->idcita }}
                    </p>

                </div>

                <x-sigac.status :label="$cita->estadocita" :variant="$varianteEstado" />

            </div>

            <hr>

            <dl class="sigac-details">

                <div>
                    <dt>Fecha y hora</dt>

                    <dd>
                        {{ $cita->fechacita?->format('d/m/Y h:i A') ?? 'Sin información' }}
                    </dd>
                </div>

                <div>
                    <dt>Tipo de servicio</dt>

                    <dd>
                        {{ $cita->tipoServicio?->nombre ?? 'Sin información' }}
                    </dd>
                </div>

                <div>
                    <dt>Servicio</dt>

                    <dd>
                        {{ $cita->servicio?->nombre ?? 'Sin información' }}
                    </dd>
                </div>

                <div>
                    <dt>Reprogramaciones realizadas</dt>

                    <dd>
                        {{ $cita->numreprogramaciones ?? 0 }} de 3
                    </dd>
                </div>

                <div class="sigac-details-full">
                    <dt>Observaciones</dt>

                    <dd>
                        {{ $cita->detalle ?? 'Sin observaciones' }}
                    </dd>
                </div>

            </dl>

        </x-sigac.card>

        {{-- Información del paciente --}}

        <x-sigac.card title="Información del paciente" subtitle="Datos del paciente asociado al agendamiento.">

            <div class="sigac-identity mb-3">

                <div class="sigac-icon sigac-icon-info">
                    <i class="bi bi-person" aria-hidden="true"></i>
                </div>

                <div class="sigac-identity-content">

                    <h2>
                        {{ $cita->paciente?->user?->name ?? 'Sin información' }}
                        {{ $cita->paciente?->user?->last_name ?? '' }}
                    </h2>

                    <p class="sigac-description mb-0">
                        Paciente #{{ $cita->idpaciente }}
                    </p>

                </div>

            </div>

        </x-sigac.card>

        {{-- Profesional y ubicación --}}

        <x-sigac.card title="Profesional y ubicación" subtitle="Información del profesional asignado y lugar de atención.">

            <dl class="sigac-details">

                <div>
                    <dt>Profesional</dt>

                    <dd>
                        {{ $cita->agenda?->profesional?->user?->name ?? 'Sin información' }}
                        {{ $cita->agenda?->profesional?->user?->last_name ?? '' }}
                    </dd>
                </div>

                <div>
                    <dt>Sede</dt>

                    <dd>
                        {{ $cita->agenda?->sede?->nombre ?? 'Sin información' }}
                    </dd>
                </div>

                <div>
                    <dt>Consultorio</dt>

                    <dd>
                        {{ $cita->agenda?->consultorio ?? 'Sin información' }}
                    </dd>
                </div>

            </dl>

        </x-sigac.card>

    </div>
@endsection
