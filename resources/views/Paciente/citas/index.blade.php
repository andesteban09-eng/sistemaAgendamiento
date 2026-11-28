@extends('layouts.dashboard')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Mis citas" subtitle="Consulta el historial y gestiona tus citas médicas."
            eyebrow="SIGAC · PORTAL DE PACIENTES">

            <x-slot:actions>

                <a href="{{ route('dashboard') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left me-1"></i>
                    Volver al panel

                </a>

                <a href="{{ route('Paciente.citas.create') }}" class="btn sigac-btn-light">

                    <i class="bi bi-calendar-plus me-1"></i>
                    Agendar cita

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

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

        {{-- Indicadores --}}

        <div class="sigac-metrics sigac-grid-four">

            <x-sigac.metric-card label="Total de citas" :value="$citas->count()" icon="calendar-event" color="navy" />

            <x-sigac.metric-card label="Pendientes" :value="$citas->where('estadocita', 'Pendiente')->count()" icon="clock" color="warning" />

            <x-sigac.metric-card label="Confirmadas" :value="$citas->where('estadocita', 'Confirmada')->count()" icon="person-check" color="info" />

            <x-sigac.metric-card label="Realizadas" :value="$citas->whereIn('estadocita', ['Realizada', 'Atendida'])->count()" icon="check-circle" color="success" />

        </div>

        {{-- Historial --}}

        <x-sigac.card title="Historial de citas" subtitle="Consulta la información y el estado de tus citas registradas.">

            @if ($citas->isNotEmpty())
                <div class="table-responsive">

                    <table class="table sigac-table align-middle">

                        <thead>
                            <tr>
                                <th>Cita</th>
                                <th>Servicio</th>
                                <th>Fecha y hora</th>
                                <th>Profesional</th>
                                <th>Sede</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($citas as $cita)
                                @php

                                    $varianteEstado = match ($cita->estadocita) {
                                        'Pendiente' => 'warning',
                                        'Confirmada' => 'info',
                                        'Realizada', 'Atendida' => 'success',
                                        'Cancelada' => 'danger',
                                        default => 'neutral',
                                    };

                                    $profesional = $cita->agenda?->profesional?->user;

                                    $nombreProfesional = trim(
                                        ($profesional?->name ?? '') . ' ' . ($profesional?->last_name ?? ''),
                                    );

                                    $puedeModificar =
                                        $cita->estadocita === 'Pendiente' &&
                                        $cita->fechacita &&
                                        now()->lte(
                                            \Carbon\Carbon::parse($cita->fechacita, 'America/Bogota')->subHours(24),
                                        );
                                @endphp

                                <tr>

                                    <td class="fw-semibold">
                                        #{{ $cita->idcita }}
                                    </td>

                                    <td>

                                        <div class="sigac-table-entity">

                                            <div class="sigac-icon sigac-icon-info">
                                                <i class="bi bi-clipboard2-pulse"></i>
                                            </div>

                                            <div>

                                                <strong>
                                                    {{ $cita->servicio?->nombre ?? 'Sin información' }}
                                                </strong>

                                                <div class="text-muted small">
                                                    {{ $cita->tipoServicio?->nombre ?? '' }}
                                                </div>

                                            </div>

                                        </div>

                                    </td>

                                    <td>

                                        <div class="fw-semibold">
                                            {{ $cita->fechacita?->format('d/m/Y') ?? 'Sin fecha' }}
                                        </div>

                                        <div class="text-muted small">
                                            {{ $cita->fechacita?->format('h:i A') ?? 'Sin hora' }}
                                        </div>

                                    </td>

                                    <td>
                                        {{ $nombreProfesional ?: 'Sin información' }}
                                    </td>

                                    <td>

                                        {{ $cita->agenda?->sede?->nombre ?? 'Sin información' }}

                                        <div class="small text-muted">
                                            {{ $cita->agenda?->consultorio ?? '' }}
                                        </div>

                                    </td>

                                    <td>

                                        <x-sigac.status :label="$cita->estadocita" :variant="$varianteEstado" />

                                    </td>

                                    <td>

                                        <div class="sigac-table-actions">

                                            <a href="{{ route('Paciente.citas.show', $cita) }}"
                                                class="btn btn-sm sigac-btn-light">

                                                <i class="bi bi-eye me-1"></i>
                                                Ver

                                            </a>

                                            @if ($puedeModificar)
                                                <a href="{{ route('Paciente.citas.edit', $cita) }}"
                                                    class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-pencil-square me-1"></i>
                                                    Editar

                                                </a>
                                            @endif

                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="sigac-pagination">

                    <span class="sigac-table-results">
                        Total: {{ $citas->count() }} citas registradas.
                    </span>

                </div>
            @else
                <x-sigac.empty-state title="Todavía no tienes citas registradas"
                    description="Agenda tu primera cita para comenzar a gestionar tus atenciones desde SIGAC."
                    icon="calendar-x">

                    <a href="{{ route('Paciente.citas.create') }}" class="btn sigac-btn-primary">

                        <i class="bi bi-calendar-plus me-1"></i>
                        Agendar una cita

                    </a>

                </x-sigac.empty-state>
            @endif

        </x-sigac.card>

        <x-sigac.alert variant="info" title="Información sobre tus citas">

            Recuerda que puedes modificar una cita pendiente con al menos
            24 horas de anticipación. Cada cita admite un máximo de
            tres reprogramaciones.

        </x-sigac.alert>

    </div>

@endsection
