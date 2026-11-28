@extends('layouts.dashboard')

@section('contenido')

    <div class="sigac-page sigac-paciente">

        {{-- Encabezado --}}

        <x-sigac.page-header title="Panel del paciente" subtitle="Gestiona tus citas y consulta tus próximas atenciones."
            eyebrow="SIGAC · PORTAL DE PACIENTES" />

        {{-- Notificaciones --}}

        @if (session('success'))
            <x-sigac.alert variant="success">
                {{ session('success') }}
            </x-sigac.alert>
        @endif

        @if (session('error'))
            <x-sigac.alert variant="danger">
                {{ session('error') }}
            </x-sigac.alert>
        @endif

        @unless ($paciente)
            <x-sigac.alert variant="warning" title="Perfil de paciente no disponible">

                Tu cuenta todavía no tiene un perfil de paciente asociado.
                Si tienes dificultades para agendar una cita,
                comunícate con el administrador.

            </x-sigac.alert>
        @endunless

        {{-- Accesos principales --}}

        <section class="sigac-modules mb-3">

            <x-sigac.module-card title="Agendar una cita"
                description="Selecciona el servicio que necesitas y consulta los horarios disponibles." :href="route('Paciente.citas.create')"
                icon="calendar-plus" color="info" />

            <x-sigac.module-card title="Mis citas"
                description="Consulta tus citas registradas y gestiona las que tengas pendientes." :href="route('Paciente.citas.index')"
                icon="calendar-check" color="navy" />

        </section>

        {{-- Próxima cita y resumen --}}

        <div class="row g-3 mb-3">

            <div class="col-12 col-xl-8">

                <x-sigac.card title="Próxima cita" subtitle="Información de tu siguiente cita pendiente."
                    class="h-100 mb-0">

                    @if ($proximaCita)
                        @php
                            $profesional = $proximaCita->agenda?->profesional?->user;

                            $nombreProfesional = trim(
                                ($profesional?->name ?? '') . ' ' . ($profesional?->last_name ?? ''),
                            );
                        @endphp

                        <div class="sigac-identity">

                            <div class="sigac-icon sigac-icon-info">
                                <i class="bi bi-calendar-event"></i>
                            </div>

                            <div class="sigac-identity-content">

                                <h2>
                                    {{ $proximaCita->servicio?->nombre ?? 'Sin información' }}
                                </h2>

                                <p class="sigac-description mb-0">
                                    Cita #{{ $proximaCita->idcita }}
                                </p>

                            </div>

                            <x-sigac.status label="Pendiente" variant="warning" />

                        </div>

                        <hr>

                        <dl class="sigac-details">

                            <div>
                                <dt>
                                    <i class="bi bi-calendar3 me-1"></i>
                                    Fecha
                                </dt>

                                <dd>
                                    {{ $proximaCita->fechacita?->format('d/m/Y') ?? 'Sin fecha' }}
                                </dd>
                            </div>

                            <div>
                                <dt>
                                    <i class="bi bi-clock me-1"></i>
                                    Hora
                                </dt>

                                <dd>
                                    {{ $proximaCita->fechacita?->format('h:i A') ?? 'Sin hora' }}
                                </dd>
                            </div>

                            <div>
                                <dt>
                                    <i class="bi bi-person-badge me-1"></i>
                                    Profesional
                                </dt>

                                <dd>
                                    {{ $nombreProfesional ?: 'Sin información' }}
                                </dd>
                            </div>

                            <div>
                                <dt>
                                    <i class="bi bi-hospital me-1"></i>
                                    Sede
                                </dt>

                                <dd>
                                    {{ $proximaCita->agenda?->sede?->nombre ?? 'Sin información' }}
                                </dd>
                            </div>

                            <div>
                                <dt>
                                    <i class="bi bi-door-open me-1"></i>
                                    Consultorio
                                </dt>

                                <dd>
                                    {{ $proximaCita->agenda?->consultorio ?? 'Sin asignar' }}
                                </dd>
                            </div>

                            <div>
                                <dt>
                                    <i class="bi bi-clipboard2-pulse me-1"></i>
                                    Tipo de servicio
                                </dt>

                                <dd>
                                    {{ $proximaCita->tipoServicio?->nombre ?? 'Sin información' }}
                                </dd>
                            </div>

                        </dl>

                        <div class="d-flex flex-wrap gap-2 mt-3">

                            <a href="{{ route('Paciente.citas.show', $proximaCita) }}" class="btn sigac-btn-primary">

                                <i class="bi bi-eye me-1"></i>
                                Ver detalle

                            </a>

                            <a href="{{ route('Paciente.citas.index') }}" class="btn sigac-btn-light">

                                Ver mis citas

                            </a>

                        </div>
                    @else
                        <x-sigac.empty-state title="No tienes citas pendientes próximas"
                            description="Cuando agendes una cita, podrás consultar aquí la información de tu próxima atención."
                            icon="calendar-check">

                            <a href="{{ route('Paciente.citas.create') }}" class="btn sigac-btn-primary">

                                <i class="bi bi-calendar-plus me-1"></i>
                                Agendar una cita

                            </a>

                        </x-sigac.empty-state>
                    @endif

                </x-sigac.card>

            </div>

            {{-- Resumen personal --}}

            <div class="col-12 col-xl-4">

                <x-sigac.card title="Resumen de citas" subtitle="Consulta tu actividad." class="h-100 mb-0">

                    <x-sigac.metric-card label="Citas pendientes próximas" :value="$totalPendientes" icon="calendar-check"
                        color="warning" />

                    <div class="mt-3">

                        <p class="sigac-description mb-3">
                            Desde el listado de citas puedes consultar
                            el estado de tus atenciones y las operaciones
                            disponibles para cada una.
                        </p>

                        <a href="{{ route('Paciente.citas.index') }}" class="btn sigac-btn-light w-100">

                            <i class="bi bi-clock-history me-1"></i>
                            Consultar mis citas

                        </a>

                    </div>

                </x-sigac.card>

            </div>

        </div>

        {{-- Servicios futuros --}}

        <x-sigac.card title="Servicios complementarios" subtitle="Nuevas funcionalidades del portal de pacientes.">

            <div class="row g-3">

                <div class="col-12 col-md-6">

                    <div class="sigac-module h-100">

                        <div class="sigac-icon sigac-icon-neutral">
                            <i class="bi bi-droplet"></i>
                        </div>

                        <div class="sigac-module-content">

                            <h3>Pruebas de laboratorio</h3>

                            <p>
                                Consulta y gestión de pruebas
                                y procedimientos de laboratorio.
                            </p>

                            <x-sigac.status label="Próximamente" variant="neutral" />

                        </div>

                        <i class="bi bi-lock text-muted"></i>

                    </div>

                </div>

                <div class="col-12 col-md-6">

                    <div class="sigac-module h-100">

                        <div class="sigac-icon sigac-icon-neutral">
                            <i class="bi bi-file-earmark-medical"></i>
                        </div>

                        <div class="sigac-module-content">

                            <h3>Resultados</h3>

                            <p>
                                Consulta de resultados de
                                pruebas y procedimientos.
                            </p>

                            <x-sigac.status label="Próximamente" variant="neutral" />

                        </div>

                        <i class="bi bi-lock text-muted"></i>

                    </div>

                </div>

            </div>

        </x-sigac.card>

        {{-- Información para el paciente --}}

        <x-sigac.alert variant="info" title="¿Necesitas realizarte un procedimiento?">

            Consulta los servicios disponibles desde la opción
            Agendar una cita. El sistema te mostrará los horarios
            correspondientes al servicio seleccionado.

        </x-sigac.alert>

    </div>

@endsection
