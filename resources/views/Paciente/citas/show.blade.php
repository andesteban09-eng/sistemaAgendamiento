@extends('layouts.dashboard')

@section('contenido')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="fw-bold mb-1">
                    Detalle de la cita
                </h1>

                <p class="text-muted mb-0">
                    Consulta la información de tu cita.
                </p>
            </div>

            <a href="{{ route('Paciente.citas.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Mis citas
            </a>

        </div>


        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>

                <div>
                    {{ session('success') }}
                </div>
            </div>
        @endif


        <div class="card border-0 shadow-sm">

            <div class="card-body p-4 p-lg-5">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>
                        <span class="text-muted">
                            Cita #{{ $cita->idcita }}
                        </span>

                        <h3 class="fw-bold mb-0">
                            {{ $cita->servicio?->nombre ?? 'Sin información' }}
                        </h3>
                    </div>


                    <div>

                        @if ($cita->estadocita === 'Pendiente')
                            <span class="badge bg-warning text-dark fs-6">
                                Pendiente
                            </span>
                        @elseif ($cita->estadocita === 'Realizada')
                            <span class="badge bg-success fs-6">
                                Realizada
                            </span>
                        @elseif ($cita->estadocita === 'Cancelada')
                            <span class="badge bg-danger fs-6">
                                Cancelada
                            </span>
                        @else
                            <span class="badge bg-secondary fs-6">
                                {{ $cita->estadocita }}
                            </span>
                        @endif

                    </div>

                </div>


                <div class="row g-4">

                    {{-- TIPO DE SERVICIO --}}

                    <div class="col-md-6">

                        <div class="p-3 bg-light rounded-3">

                            <p class="text-muted mb-1">
                                Tipo de servicio
                            </p>

                            <h5 class="fw-bold mb-0">
                                {{ $cita->tipoServicio?->nombre ?? 'Sin información' }}
                            </h5>

                        </div>

                    </div>


                    {{-- SERVICIO --}}

                    <div class="col-md-6">

                        <div class="p-3 bg-light rounded-3">

                            <p class="text-muted mb-1">
                                Servicio
                            </p>

                            <h5 class="fw-bold mb-0">
                                {{ $cita->servicio?->nombre ?? 'Sin información' }}
                            </h5>

                        </div>

                    </div>


                    {{-- FECHA --}}

                    <div class="col-md-6">

                        <div class="p-3 bg-light rounded-3">

                            <p class="text-muted mb-1">
                                Fecha
                            </p>

                            <h5 class="fw-bold mb-0">

                                {{ $cita->fechacita?->format('d/m/Y') ?? 'Sin información' }}

                            </h5>

                        </div>

                    </div>


                    {{-- HORA --}}

                    <div class="col-md-6">

                        <div class="p-3 bg-light rounded-3">

                            <p class="text-muted mb-1">
                                Hora
                            </p>

                            <h5 class="fw-bold mb-0">

                                {{ $cita->fechacita?->format('h:i A') ?? 'Sin información' }}

                            </h5>

                        </div>

                    </div>


                    {{-- PROFESIONAL --}}

                    <div class="col-md-6">

                        <div class="p-3 bg-light rounded-3">

                            <p class="text-muted mb-1">
                                Profesional asignado
                            </p>

                            <h5 class="fw-bold mb-0">

                                {{ $cita->agenda?->profesional?->user?->name ?? '' }}

                                {{ $cita->agenda?->profesional?->user?->last_name ?? '' }}

                            </h5>

                        </div>

                    </div>


                    {{-- SEDE --}}

                    <div class="col-md-6">

                        <div class="p-3 bg-light rounded-3">

                            <p class="text-muted mb-1">
                                Sede
                            </p>

                            <h5 class="fw-bold mb-0">
                                {{ $cita->agenda?->sede?->nombre ?? 'Sin información' }}
                            </h5>

                        </div>

                    </div>


                    {{-- CONSULTORIO --}}

                    <div class="col-md-6">

                        <div class="p-3 bg-light rounded-3">

                            <p class="text-muted mb-1">
                                Consultorio
                            </p>

                            <h5 class="fw-bold mb-0">
                                {{ $cita->agenda?->consultorio ?? 'Sin información' }}
                            </h5>

                        </div>

                    </div>


                    {{-- DETALLE --}}

                    <div class="col-md-6">

                        <div class="p-3 bg-light rounded-3">

                            <p class="text-muted mb-1">
                                Detalle
                            </p>

                            <h5 class="fw-bold mb-0">
                                {{ $cita->detalle }}
                            </h5>

                        </div>

                    </div>

                </div>

                @if ($cita->estadocita === 'Pendiente')
                <hr class="my-4">
                    <div class="d-flex justify-content-end gap-2">

                        <a href="{{ route('Paciente.citas.edit', $cita) }}" class="btn btn-outline-warning">
                            <i class="bi bi-pencil-square me-1"></i>
                            Editar cita
                        </a>

                        <form action="{{ route('Paciente.citas.cancelar', $cita) }}" method="POST"
                            onsubmit="return confirm('¿Está seguro de cancelar esta cita?');">

                            @csrf
                            @method('PATCH')

                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle me-1"></i>
                                Cancelar cita
                            </button>

                        </form>

                    </div>
                @endif

            </div>

        </div>

    </div>
@endsection
