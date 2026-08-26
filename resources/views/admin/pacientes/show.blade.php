@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="fw-bold mb-1">
                    Información del paciente
                </h1>

                <p class="text-muted mb-0">
                    Consulta detallada del paciente registrado.
                </p>
            </div>

            <div>
                <a href="{{ route('admin.pacientes.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Volver
                </a>
            </div>

        </div>


        <div class="row g-4">

            {{-- Información personal --}}
            <div class="col-lg-6">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body p-4">

                        <h4 class="fw-bold mb-4">
                            <i class="bi bi-person me-2"></i>
                            Información personal
                        </h4>

                        <div class="mb-3">
                            <small class="text-muted">
                                Nombre completo
                            </small>

                            <div class="fw-semibold">
                                {{ $paciente->user?->name }}
                                {{ $paciente->user?->last_name }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                Correo electrónico
                            </small>

                            <div>
                                {{ $paciente->user?->email }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                Tipo de documento
                            </small>

                            <div>
                                {{ $paciente->tipodoc }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                Número de documento
                            </small>

                            <div class="fw-semibold">
                                {{ $paciente->numdoc }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>


            {{-- Información de contacto --}}
            <div class="col-lg-6">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body p-4">

                        <h4 class="fw-bold mb-4">
                            <i class="bi bi-telephone me-2"></i>
                            Información de contacto
                        </h4>

                        <div class="mb-3">
                            <small class="text-muted">
                                Teléfono
                            </small>

                            <div>
                                {{ $paciente->telefono }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                Dirección
                            </small>

                            <div>
                                {{ $paciente->direccion }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                Ciudad
                            </small>

                            <div>
                                {{ $paciente->ciudad }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                Estado
                            </small>

                            <div>
                                @if ($paciente->estadopaciente === 'Activo')
                                    <span class="badge text-bg-success">
                                        Activo
                                    </span>
                                @else
                                    <span class="badge text-bg-secondary">
                                        Inactivo
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>

            </div>


            {{-- Información del registro --}}
            <div class="col-12">

                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4">

                        <h4 class="fw-bold mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Información del registro
                        </h4>

                        <div class="row">

                            <div class="col-md-4">
                                <small class="text-muted">
                                    ID paciente
                                </small>

                                <div class="fw-semibold">
                                    {{ $paciente->idpaciente }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Fecha de registro
                                </small>

                                <div>
                                    {{ $paciente->fecharegistro ?? 'No registrada' }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    ID usuario
                                </small>

                                <div>
                                    {{ $paciente->idusuario }}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
