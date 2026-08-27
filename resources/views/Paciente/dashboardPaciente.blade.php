@extends('layouts.dashboard')

@section('contenido')
    <div class="container py-4">

        {{-- ========================================================= --}}
        {{-- BIENVENIDA --}}
        {{-- ========================================================= --}}

        <section class="hero rounded-4 shadow-sm mb-4 p-4 p-lg-5">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <p class="text-uppercase small fw-bold mb-2">
                        Portal de pacientes
                    </p>

                    <h1 class="fw-bold mb-3">
                        Hola, {{ auth()->user()->name }}
                    </h1>

                    <p class="lead mb-2">
                        Bienvenido al portal de pacientes de
                        Laboratorios Carvajal IPS.
                    </p>

                    <p class="mb-0">
                        Gestiona tus citas, consulta tus pruebas de laboratorio
                        y revisa tus resultados desde un solo lugar.
                    </p>

                </div>

                <div class="col-md-4 text-center mt-4 mt-md-0">

                    <i class="bi bi-person-heart display-1"></i>

                </div>

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- RESUMEN --}}
        {{-- ========================================================= --}}

        <div class="row g-4 mb-4">

            {{-- CITAS --}}
            <div class="col-md-4">

                <div class="card dashboard-card border-0 shadow-sm h-100">

                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <p class="text-muted mb-1">
                                    Citas pendientes
                                </p>

                                <h2 class="fw-bold mb-0">
                                    {{ $totalPendientes }}
                                </h2>

                            </div>

                            <i class="bi bi-calendar-check fs-1 text-primary"></i>

                        </div>

                    </div>

                </div>

            </div>


            {{-- PRUEBAS --}}
            <div class="col-md-4">

                <div class="card dashboard-card border-0 shadow-sm h-100">

                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <p class="text-muted mb-1">
                                    Pruebas de laboratorio
                                </p>

                                <h2 class="fw-bold mb-0">
                                    —
                                </h2>

                                <small class="text-muted">
                                    Próximamente
                                </small>

                            </div>

                            <i class="bi bi-droplet fs-1 text-success"></i>

                        </div>

                    </div>

                </div>

            </div>


            {{-- RESULTADOS --}}
            <div class="col-md-4">

                <div class="card dashboard-card border-0 shadow-sm h-100">

                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <p class="text-muted mb-1">
                                    Resultados
                                </p>

                                <h2 class="fw-bold mb-0">
                                    —
                                </h2>

                                <small class="text-muted">
                                    Próximamente
                                </small>

                            </div>

                            <i class="bi bi-file-earmark-medical fs-1 text-danger"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- ACCIONES PRINCIPALES --}}
        {{-- ========================================================= --}}

        <div class="row g-4 mb-4">

            {{-- AGENDAR CITA --}}
            <div class="col-md-6">

                <a href="{{ route('Paciente.citas.index') }}" class="text-decoration-none">

                    <div class="card dashboard-card border-0 shadow-sm h-100">

                        <div class="card-body p-4 p-lg-5">

                            <i class="bi bi-calendar-plus display-4 text-primary"></i>

                            <h3 class="fw-bold mt-3">
                                Agendar una cita
                            </h3>

                            <p class="text-muted mb-0">
                                Selecciona el servicio que necesitas y
                                encuentra una disponibilidad con un profesional
                                autorizado.
                            </p>

                        </div>

                    </div>

                </a>

            </div>


            {{-- PRUEBAS --}}
            <div class="col-md-6">

                <a href="#" class="text-decoration-none">

                    <div class="card dashboard-card border-0 shadow-sm h-100">

                        <div class="card-body p-4 p-lg-5">

                            <i class="bi bi-clipboard2-pulse display-4 text-success"></i>

                            <h3 class="fw-bold mt-3">
                                Pruebas de laboratorio
                            </h3>

                            <p class="text-muted mb-0">
                                Consulta y gestiona las pruebas y procedimientos
                                de laboratorio disponibles.
                            </p>

                        </div>

                    </div>

                </a>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- PRÓXIMA CITA --}}
        {{-- ========================================================= --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <h4 class="fw-bold mb-0">

                        <i class="bi bi-calendar-event me-2"></i>

                        Próxima cita

                    </h4>

                    @if ($proximaCita)
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            Ver mis citas
                        </a>
                    @endif

                </div>


                @if ($proximaCita)
                    <div class="row g-4">

                        {{-- SERVICIO --}}
                        <div class="col-md-6">

                            <p class="text-muted mb-1">
                                Servicio
                            </p>

                            <h5 class="fw-bold">
                                {{ $proximaCita->servicio?->nombre ?? 'Sin información' }}
                            </h5>

                        </div>


                        {{-- FECHA --}}
                        <div class="col-md-6">

                            <p class="text-muted mb-1">
                                Fecha y hora
                            </p>

                            <h5 class="fw-bold">

                                {{ $proximaCita->fechacita?->format('d/m/Y h:i A') }}

                            </h5>

                        </div>


                        {{-- PROFESIONAL --}}
                        <div class="col-md-6">

                            <p class="text-muted mb-1">
                                Profesional
                            </p>

                            <h5 class="fw-bold">

                                {{ $proximaCita->agenda?->profesional?->user?->name }}

                                {{ $proximaCita->agenda?->profesional?->user?->last_name }}

                            </h5>

                        </div>


                        {{-- SEDE --}}
                        <div class="col-md-6">

                            <p class="text-muted mb-1">
                                Sede
                            </p>

                            <h5 class="fw-bold">

                                {{ $proximaCita->agenda?->sede?->nombre ?? 'Sin información' }}

                            </h5>

                        </div>

                    </div>
                @else
                    <div class="text-center py-4">

                        <i class="bi bi-calendar-x display-5 text-muted"></i>

                        <h5 class="mt-3">
                            No tienes citas programadas
                        </h5>

                        <p class="text-muted">
                            Cuando agendes una cita, aparecerá aquí.
                        </p>

                        <a href="#" class="btn btn-primary">

                            <i class="bi bi-calendar-plus me-2"></i>

                            Agendar una cita

                        </a>

                    </div>
                @endif

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- INFORMACIÓN --}}
        {{-- ========================================================= --}}

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                <div class="row align-items-center">

                    <div class="col-md-1 text-center">

                        <i class="bi bi-info-circle display-6 text-primary"></i>

                    </div>

                    <div class="col-md-11">

                        <h5 class="fw-bold">
                            ¿Necesitas realizarte un procedimiento?
                        </h5>

                        <p class="text-muted mb-0">
                            Puedes consultar los servicios disponibles y
                            seleccionar el procedimiento que necesitas.
                            El sistema te mostrará las disponibilidades
                            correspondientes.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
