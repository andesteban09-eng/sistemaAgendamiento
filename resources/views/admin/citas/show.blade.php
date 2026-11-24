@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Detalle de la cita</h1>
                <p class="text-muted mb-0">
                    Información del agendamiento #{{ $cita->idcita }}
                </p>
            </div>

            <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Volver al listado
            </a>
        </div>

        <div class="row g-4">

            {{-- Información de la cita --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">
                            <i class="bi bi-calendar-check me-2"></i>
                            Información de la cita
                        </h4>

                        <p>
                            <strong>Fecha y hora:</strong>
                            {{ $cita->fechacita?->format('d/m/Y h:i A') ?? 'Sin información' }}
                        </p>

                        <p>
                            <strong>Tipo de servicio:</strong>
                            {{ $cita->tipoServicio?->nombre ?? 'Sin información' }}
                        </p>

                        <p>
                            <strong>Servicio:</strong>
                            {{ $cita->servicio?->nombre ?? 'Sin información' }}
                        </p>

                        <p>
                            <strong>Estado:</strong>

                            @php
                                $color = match ($cita->estadocita) {
                                    'Pendiente' => 'warning text-dark',
                                    'Realizada' => 'success',
                                    'Cancelada' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp

                            <span class="badge bg-{{ $color }}">
                                {{ $cita->estadocita }}
                            </span>
                        </p>

                        <p>
                            <strong>Reprogramaciones:</strong>
                            {{ $cita->numreprogramaciones ?? 0 }} de 3
                        </p>

                        <p class="mb-0">
                            <strong>Observaciones:</strong>
                            {{ $cita->detalle ?? 'Sin observaciones' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Paciente --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">
                            <i class="bi bi-person me-2"></i>
                            Paciente
                        </h4>

                        <p>
                            <strong>Nombre:</strong>
                            {{ $cita->paciente?->user?->name ?? 'Sin información' }}
                            {{ $cita->paciente?->user?->last_name ?? '' }}
                        </p>

                        <p class="mb-0">
                            <strong>Identificador:</strong>
                            {{ $cita->idpaciente }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Profesional y ubicación --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">
                            <i class="bi bi-geo-alt me-2"></i>
                            Profesional y ubicación
                        </h4>

                        <div class="row">
                            <div class="col-md-4">
                                <p>
                                    <strong>Profesional:</strong>
                                    {{ $cita->agenda?->profesional?->user?->name ?? 'Sin información' }}
                                    {{ $cita->agenda?->profesional?->user?->last_name ?? '' }}
                                </p>
                            </div>

                            <div class="col-md-4">
                                <p>
                                    <strong>Sede:</strong>
                                    {{ $cita->agenda?->sede?->nombre ?? 'Sin información' }}
                                </p>
                            </div>

                            <div class="col-md-4">
                                <p>
                                    <strong>Consultorio:</strong>
                                    {{ $cita->agenda?->consultorio ?? 'Sin información' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
