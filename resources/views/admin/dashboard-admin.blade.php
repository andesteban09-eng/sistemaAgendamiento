@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        {{-- Encabezado --}}
        <div class="mb-4">
            <h1 class="fw-bold mb-1">
                Panel de administración
            </h1>

            <p class="text-muted mb-0">
                Bienvenido al sistema de gestión de Laboratorios Carvajal IPS.
            </p>
        </div>

        {{-- Tarjetas principales --}}
        <div class="row g-4">

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">
                                    Pacientes
                                </p>

                                <h2 class="fw-bold mb-0">
                                    0
                                </h2>
                            </div>

                            <i class="bi bi-people fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">
                                    Profesionales
                                </p>

                                <h2 class="fw-bold mb-0">
                                    0
                                </h2>
                            </div>

                            <i class="bi bi-person-badge fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">
                                    Citas
                                </p>

                                <h2 class="fw-bold mb-0">
                                    0
                                </h2>
                            </div>

                            <i class="bi bi-calendar-check fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">
                                    Servicios
                                </p>

                                <h2 class="fw-bold mb-0">
                                    0
                                </h2>
                            </div>

                            <i class="bi bi-clipboard2-pulse fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Módulos administrativos --}}
        <div class="mt-5">

            <h3 class="fw-bold mb-4">
                Gestión del sistema
            </h3>

            <div class="row g-4">

                {{-- Pacientes --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-people fs-1 text-primary"></i>

                            <h4 class="mt-3">
                                Pacientes
                            </h4>

                            <p class="text-muted">
                                Registrar, consultar, actualizar y gestionar
                                pacientes del sistema.
                            </p>

                            <a href="{{ route('admin.pacientes.index') }}" class="btn btn-primary">
                                Gestionar pacientes
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Profesionales --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-person-badge fs-1 text-success"></i>

                            <h4 class="mt-3">
                                Profesionales
                            </h4>

                            <p class="text-muted">
                                Administrar los profesionales de salud
                                registrados en el sistema.
                            </p>

                            <a href="{{ route('admin.profesionales.index') }}" class="btn btn-success">
                                Gestionar profesionales
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Auxiliares --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">

                            <i class="bi bi-person-gear fs-1 text-info"></i>

                            <h4 class="mt-3">
                                Auxiliares
                            </h4>

                            <p class="text-muted">
                                Administrar los auxiliares encargados de gestionar
                                la distribución de horarios de los profesionales.
                            </p>

                            <a href="{{ route('admin.auxiliares.index') }}" class="btn btn-info">
                                Gestionar auxiliares
                            </a>

                        </div>
                    </div>
                </div>

                {{-- Citas --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-calendar-check fs-1 text-warning"></i>

                            <h4 class="mt-3">
                                Citas
                            </h4>

                            <p class="text-muted">
                                Consultar y administrar las citas
                                registradas en el sistema.
                            </p>

                            <a href="#" class="btn btn-warning">
                                Gestionar citas
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Tipos de servicio --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-tags fs-1 text-secondary"></i>

                            <h4 class="mt-3">
                                Tipos de servicio
                            </h4>

                            <p class="text-muted">
                                Crear y administrar las categorías que agrupan
                                los servicios ofrecidos por el laboratorio.
                            </p>

                            <a href="{{ route('admin.tiposervicios.index') }}" class="btn btn-secondary">
                                Gestionar tipos
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Servicios --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-clipboard2-pulse fs-1 text-danger"></i>

                            <h4 class="mt-3">
                                Servicios
                            </h4>

                            <p class="text-muted">
                                Registrar y administrar los servicios del
                                laboratorio, sus precios y requisitos.
                            </p>

                            <a href="{{ route('admin.servicios.index') }}" class="btn btn-danger">
                                Gestionar servicios
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
