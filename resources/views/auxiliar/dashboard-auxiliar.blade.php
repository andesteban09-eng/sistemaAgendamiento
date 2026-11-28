@extends('layouts.admin')

@section('contenido')

    @php
        $subtitulo = 'Bienvenido, ' . auth()->user()->name . '. Consulta la actividad operativa de la IPS.';

        $indicadores = [
            ['Citas de hoy', $citasHoy, 'calendar-event', 'navy'],
            ['Pendientes', $citasPendientes, 'clock', 'warning'],
            ['Confirmadas', $citasConfirmadas, 'person-check', 'info'],
            ['Realizadas', $citasRealizadas, 'check-circle', 'success'],
        ];

        $estados = [
            ['Pendientes', $citasPendientes, 'warning'],
            ['Confirmadas', $citasConfirmadas, 'info'],
            ['Realizadas', $citasRealizadas, 'success'],
            ['Canceladas', $citasCanceladas, 'danger'],
        ];

        $recursos = [
            ['Profesionales activos', $profesionalesActivos, 'person-badge'],
            ['Sedes activas', $sedesActivas, 'building'],
            ['Horarios de hoy', $horariosHoy, 'calendar-week'],
        ];
    @endphp

    <div class="sigac-page">

        {{-- Encabezado --}}

        <x-sigac.page-header title="Panel del auxiliar" :subtitle="$subtitulo" eyebrow="SIGAC · GESTIÓN OPERATIVA" />

        {{-- Indicadores principales --}}

        <section class="sigac-metrics sigac-grid-four" aria-label="Indicadores de la jornada">

            @foreach ($indicadores as [$label, $value, $icon, $color])
                <x-sigac.metric-card :label="$label" :value="$value" :icon="$icon" :color="$color" />
            @endforeach

        </section>

        {{-- Progreso de la jornada --}}

        <x-sigac.card title="Progreso de la jornada" subtitle="Distribución de las citas de hoy.">

            <x-slot:actions>

                <div class="text-end">

                    <span class="sigac-description">
                        Citas realizadas
                    </span>

                    <strong class="sigac-metric-value text-success mt-0">
                        {{ $porcentajeRealizadas }}%
                    </strong>

                </div>

            </x-slot:actions>

            @if ($citasHoy > 0)
                <div class="progress-stacked mb-3" style="height: 12px;" role="img"
                    aria-label="Distribución de citas por estado">

                    @foreach ($estados as [$label, $total, $color])
                        @php
                            $porcentaje = ($total / $citasHoy) * 100;
                        @endphp

                        <div class="progress" style="width: {{ $porcentaje }}%;">

                            <div class="progress-bar" style="background-color: var(--sigac-{{ $color }});">
                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                <x-sigac.empty-state title="Sin actividad programada" description="No hay citas programadas para hoy."
                    icon="calendar-x" />
            @endif

            {{-- Distribución por estado --}}

            <div class="row g-3">

                @foreach ($estados as [$label, $value, $color])
                    <div class="col-12 col-sm-6 col-lg-3">

                        <div class="d-flex align-items-center justify-content-between gap-2 border rounded-3 p-3">

                            <x-sigac.status :label="$label" :variant="$color" />

                            <strong>{{ $value }}</strong>

                        </div>

                    </div>
                @endforeach

            </div>

        </x-sigac.card>

        {{-- Capacidad operativa --}}

        <x-sigac.card title="Capacidad operativa" subtitle="Recursos registrados en la IPS.">

            <div class="row g-3">

                @foreach ($recursos as [$label, $value, $icon])
                    <div class="col-12 col-md-4">

                        <div class="sigac-resource h-100">

                            <div class="sigac-icon sigac-icon-info">

                                <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>

                            </div>

                            <div>

                                <span>{{ $label }}</span>

                                <strong>{{ $value }}</strong>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

        </x-sigac.card>

        {{-- Accesos rápidos --}}

        <x-sigac.card title="Gestión operativa" subtitle="Accede a las herramientas de gestión de agendas y citas.">

            <div class="sigac-modules">

                <x-sigac.module-card title="Gestionar agenda"
                    description="Registra y administra los horarios de los profesionales." :href="route('auxiliar.agenda.index')"
                    icon="calendar-plus" color="info" />

                <x-sigac.module-card title="Consultar citas"
                    description="Consulta las citas programadas y gestiona sus estados." :href="route('auxiliar.citas.index')"
                    icon="calendar-check" color="navy" />

            </div>

        </x-sigac.card>

    </div>

@endsection
