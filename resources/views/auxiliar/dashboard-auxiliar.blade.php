@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        {{-- Encabezado --}}
        <div class="mb-4">
            <h1 class="fw-bold mb-1">
                Panel del auxiliar
            </h1>

            <p class="text-muted mb-0">
                Bienvenido al sistema de gestión de Laboratorios Carvajal IPS.
                Desde este panel podrá gestionar la disponibilidad de los profesionales
                y consultar las citas registradas.
            </p>
        </div>

        {{-- Resumen --}}
        <div class="row g-4">

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <p class="text-muted mb-1">
                                    Profesionales activos
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
                                    Sedes activas
                                </p>

                                <h2 class="fw-bold mb-0">
                                    0
                                </h2>
                            </div>

                            <i class="bi bi-building fs-1 text-primary"></i>

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
                                    Horarios registrados
                                </p>

                                <h2 class="fw-bold mb-0">
                                    0
                                </h2>
                            </div>

                            <i class="bi bi-calendar-week fs-1 text-warning"></i>

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
                                    Citas pendientes
                                </p>

                                <h2 class="fw-bold mb-0">
                                    0
                                </h2>
                            </div>

                            <i class="bi bi-calendar-check fs-1 text-info"></i>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Módulos del auxiliar --}}
        <div class="mt-5">

            <h3 class="fw-bold mb-4">
                Gestión de agenda
            </h3>

            <div class="row g-4">

                {{-- Agenda --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body p-4">

                            <i class="bi bi-calendar-plus fs-1 text-primary"></i>

                            <h4 class="mt-3">
                                Gestionar agenda
                            </h4>

                            <p class="text-muted">
                                Registrar y administrar la disponibilidad de horarios
                                de los profesionales de la salud según la sede,
                                fecha y hora.
                            </p>

                            <a href="{{ route('auxiliar.agenda.index') }}" class="btn btn-primary">
                                Gestionar agenda
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
                                Consultar citas
                            </h4>

                            <p class="text-muted">
                                Consultar las citas programadas y verificar
                                su estado, profesional, sede, servicio,
                                fecha y hora.
                            </p>

                            <a href="#" class="btn btn-warning">
                                Consultar citas
                            </a>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
