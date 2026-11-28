@extends('layouts.dashboard')

@section('contenido')

    @php

        $varianteEstado = match ($cita->estadocita) {
            'Pendiente' => 'warning',
            'Confirmada' => 'info',
            'Realizada', 'Atendida' => 'success',
            'Cancelada' => 'danger',
            default => 'neutral',
        };

        $profesional = $cita->agenda?->profesional?->user;

        $nombreProfesional = trim(($profesional?->name ?? '') . ' ' . ($profesional?->last_name ?? ''));

        $reprogramaciones = (int) ($cita->numreprogramaciones ?? 0);

        $puedeModificar =
            $cita->estadocita === 'Pendiente' &&
            $cita->fechacita &&
            now()->lte(\Carbon\Carbon::parse($cita->fechacita, 'America/Bogota')->subHours(24));
    @endphp

    <div class="sigac-page">

        <x-sigac.page-header title="Detalle de la cita" subtitle="Consulta la información de tu atención programada."
            eyebrow="SIGAC · PORTAL DE PACIENTES">

            <x-slot:actions>

                <a href="{{ route('Paciente.citas.index') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left me-1"></i>
                    Mis citas

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        <div class="sigac-form-card">

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

            {{-- Información de la cita --}}

            <x-sigac.card>

                <div class="sigac-identity">

                    <div class="sigac-icon sigac-icon-info">
                        <i class="bi bi-calendar-check"></i>
                    </div>

                    <div class="sigac-identity-content">

                        <h2>
                            {{ $cita->servicio?->nombre ?? 'Cita médica' }}
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
                        <dt>
                            <i class="bi bi-calendar3 me-1"></i>
                            Fecha
                        </dt>

                        <dd>
                            {{ $cita->fechacita?->format('d/m/Y') ?? 'Sin información' }}
                        </dd>
                    </div>

                    <div>
                        <dt>
                            <i class="bi bi-clock me-1"></i>
                            Hora
                        </dt>

                        <dd>
                            {{ $cita->fechacita?->format('h:i A') ?? 'Sin información' }}
                        </dd>
                    </div>

                    <div>
                        <dt>
                            <i class="bi bi-person-badge me-1"></i>
                            Profesional asignado
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
                            {{ $cita->agenda?->sede?->nombre ?? 'Sin información' }}
                        </dd>
                    </div>

                    <div>
                        <dt>
                            <i class="bi bi-door-open me-1"></i>
                            Consultorio
                        </dt>

                        <dd>
                            {{ $cita->agenda?->consultorio ?? 'Sin información' }}
                        </dd>
                    </div>

                    <div>
                        <dt>
                            <i class="bi bi-clipboard2-pulse me-1"></i>
                            Tipo de servicio
                        </dt>

                        <dd>
                            {{ $cita->tipoServicio?->nombre ?? 'Sin información' }}
                        </dd>
                    </div>

                    <div class="sigac-details-full">
                        <dt>
                            <i class="bi bi-chat-left-text me-1"></i>
                            Observaciones
                        </dt>

                        <dd class="text-break">
                            {{ $cita->detalle ?: 'Sin observaciones' }}
                        </dd>
                    </div>

                </dl>

                {{-- Reprogramaciones --}}

                <div class="sigac-resource mt-3">

                    <div class="sigac-icon sigac-icon-navy">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>

                    <div class="flex-grow-1">

                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">

                            <span>
                                Reprogramaciones utilizadas
                            </span>

                            <strong>
                                {{ $reprogramaciones }} de 3
                            </strong>

                        </div>

                        <div class="progress" role="progressbar" aria-label="Reprogramaciones utilizadas"
                            aria-valuenow="{{ min(3, max(0, $reprogramaciones)) }}" aria-valuemin="0" aria-valuemax="3"
                            style="height: 6px;">

                            <div class="progress-bar"
                                style="width: {{ min(100, max(0, ($reprogramaciones / 3) * 100)) }}%;
                                       background-color: var(--aqua);">
                            </div>

                        </div>

                    </div>

                </div>

                {{-- Acciones --}}

                @if ($cita->estadocita === 'Pendiente')
                    <div class="sigac-form-actions">

                        <form action="{{ route('Paciente.citas.cancelar', $cita) }}" method="POST"
                            onsubmit="return confirm('¿Está seguro de cancelar esta cita?');">

                            @csrf
                            @method('PATCH')

                            <button type="submit" class="btn btn-outline-danger">

                                <i class="bi bi-x-circle me-1"></i>
                                Cancelar cita

                            </button>

                        </form>

                        @if ($puedeModificar)
                            <a href="{{ route('Paciente.citas.edit', $cita) }}" class="btn sigac-btn-primary">

                                <i class="bi bi-pencil-square me-1"></i>
                                Editar cita

                            </a>
                        @endif

                    </div>
                @endif

            </x-sigac.card>

            {{-- Información según el estado --}}

            @if ($cita->estadocita === 'Pendiente')
                @if (!$puedeModificar)
                    <x-sigac.alert variant="warning" title="Plazo de modificación finalizado">

                        Las modificaciones deben realizarse con al menos
                        24 horas de anticipación al inicio de la cita.

                    </x-sigac.alert>
                @elseif ($reprogramaciones >= 3)
                    <x-sigac.alert variant="warning" title="Límite de reprogramaciones alcanzado">

                        Ya utilizaste las tres reprogramaciones permitidas.
                        Todavía puedes modificar las observaciones de tu cita.

                    </x-sigac.alert>
                @else
                    <x-sigac.alert variant="info" title="Recuerda">

                        Presenta tu documento de identidad y, cuando
                        corresponda, la orden médica requerida para tu atención.

                    </x-sigac.alert>
                @endif
            @elseif ($cita->estadocita === 'Confirmada')
                <x-sigac.alert variant="info" title="Llegada confirmada">

                    El personal de recepción ha confirmado tu llegada.
                    Tu atención se encuentra pendiente de realización.

                </x-sigac.alert>
            @elseif (in_array($cita->estadocita, ['Realizada', 'Atendida'], true))
                <x-sigac.alert variant="success" title="Atención realizada">

                    El profesional de salud ha registrado la realización
                    de tu cita.

                </x-sigac.alert>
            @elseif ($cita->estadocita === 'Cancelada')
                <x-sigac.alert variant="danger" title="Cita cancelada">

                    Esta cita fue cancelada y ya no se encuentra programada.

                </x-sigac.alert>
            @endif

        </div>

    </div>

@endsection
