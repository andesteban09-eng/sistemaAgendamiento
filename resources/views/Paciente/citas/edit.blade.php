@extends('layouts.dashboard')

@section('contenido')

    @php

        $reprogramaciones = (int) ($cita->numreprogramaciones ?? 0);

        $reprogramacionesRestantes = max(0, 3 - $reprogramaciones);

        $horarioActual = (string) $cita->idhorariodispo;

        $horarioSeleccionado = (string) old('idhorariodispo', $horarioActual);

        $horariosAlternativos = $horarios->filter(fn($horario) => (string) $horario->idhorariodispo !== $horarioActual);

        $grupos = $horariosAlternativos->groupBy(fn($horario) => $horario->fecha->format('Y-m-d'));

        $seleccionDisponible =
            $horarioSeleccionado === $horarioActual ||
            $horariosAlternativos->contains(fn($horario) => (string) $horario->idhorariodispo === $horarioSeleccionado);
    @endphp

    <div class="sigac-page sigac-booking">

        <x-sigac.page-header title="Editar cita" subtitle="Modifica el horario o las observaciones de tu atención."
            eyebrow="SIGAC · PORTAL DE PACIENTES">

            <x-slot:actions>

                <a href="{{ route('Paciente.citas.show', $cita) }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left me-1"></i>
                    Volver al detalle

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        <div class="sigac-form-card">

            {{-- Notificaciones --}}

            @if (session('error'))
                <x-sigac.alert variant="danger">
                    {{ session('error') }}
                </x-sigac.alert>
            @endif

            @if ($errors->any())
                <x-sigac.alert variant="danger" title="Revisa la información ingresada">

                    <ul class="mb-0 ps-3">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </x-sigac.alert>
            @endif

            {{-- Información de la cita actual --}}

            <x-sigac.card title="Cita actual">

                <div class="sigac-identity">

                    <div class="sigac-icon sigac-icon-info">
                        <i class="bi bi-calendar-check"></i>
                    </div>

                    <div class="sigac-identity-content">

                        <h2>
                            {{ $cita->servicio?->nombre ?? 'Sin información' }}
                        </h2>

                        <p class="sigac-description mb-0">

                            Cita #{{ $cita->idcita }}

                        </p>

                    </div>

                    <x-sigac.status label="Pendiente" variant="warning" />

                </div>

                <hr>

                <dl class="sigac-details mb-0">

                    <div>

                        <dt>Tipo de servicio</dt>

                        <dd>
                            {{ $cita->tipoServicio?->nombre ?? 'Sin información' }}
                        </dd>

                    </div>

                    <div>

                        <dt>Fecha y hora actual</dt>

                        <dd>
                            {{ $cita->fechacita?->format('d/m/Y h:i A') ?? 'Sin información' }}
                        </dd>

                    </div>

                    <div>

                        <dt>Reprogramaciones utilizadas</dt>

                        <dd>
                            {{ $reprogramaciones }} de 3
                        </dd>

                    </div>

                    <div>

                        <dt>Reprogramaciones restantes</dt>

                        <dd>
                            {{ $reprogramacionesRestantes }}
                        </dd>

                    </div>

                </dl>

            </x-sigac.card>

            {{-- Formulario --}}

            <form class="sigac-form" action="{{ route('Paciente.citas.update', $cita) }}" method="POST">

                @csrf
                @method('PUT')

                {{-- Selección de horario --}}

                <x-sigac.card title="Horario de atención"
                    subtitle="Conserva tu horario actual o selecciona una nueva disponibilidad.">

                    @if (!$puedeReprogramar)

                        {{-- Conservar obligatoriamente el horario actual --}}

                        <input type="hidden" name="idhorariodispo" value="{{ $cita->idhorariodispo }}">

                        <x-sigac.alert variant="warning" title="Límite de reprogramaciones alcanzado">

                            Ya utilizaste las tres reprogramaciones
                            permitidas. Puedes modificar las observaciones,
                            pero no el horario de atención.

                        </x-sigac.alert>

                        <div class="sigac-resource">

                            <div class="sigac-icon sigac-icon-navy">
                                <i class="bi bi-calendar-lock"></i>
                            </div>

                            <div>

                                <span>Horario actual</span>

                                <div class="fw-semibold">

                                    {{ $cita->fechacita?->format('d/m/Y h:i A') }}

                                </div>

                            </div>

                        </div>
                    @else
                        {{-- Opción permanente: mantener el horario --}}

                        <label class="sigac-slot sigac-slot-current">

                            <input type="radio" name="idhorariodispo" value="{{ $cita->idhorariodispo }}"
                                class="sigac-slot-radio visually-hidden" required @checked($horarioSeleccionado === $horarioActual)>

                            <span class="sigac-slot-content">

                                <strong class="sigac-slot-time">

                                    <i class="bi bi-calendar-check"></i>
                                    Mantener horario actual

                                </strong>

                                <span class="sigac-slot-info">

                                    {{ $cita->fechacita?->format('d/m/Y h:i A') }}

                                </span>

                                <span class="sigac-slot-info">

                                    Esta opción no consume una reprogramación.

                                </span>

                            </span>

                        </label>

                        @if (!$seleccionDisponible)
                            <x-sigac.alert variant="warning">

                                El horario seleccionado anteriormente
                                ya no está disponible. Selecciona otro
                                horario o conserva el actual.

                            </x-sigac.alert>
                        @endif

                        {{-- Nuevas disponibilidades --}}

                        @if ($horariosAlternativos->isEmpty())
                            <x-sigac.alert variant="info">

                                Actualmente no hay nuevos horarios
                                disponibles para este servicio.
                                Puedes conservar el horario actual
                                y modificar las observaciones.

                            </x-sigac.alert>
                        @else
                            <h3 class="fs-6 fw-semibold mb-3">

                                Nuevos horarios disponibles

                            </h3>

                            @foreach ($grupos as $fecha => $horariosFecha)
                                <section class="sigac-slot-group">

                                    <h4 class="sigac-slot-group-title">

                                        <i class="bi bi-calendar-event"></i>

                                        {{ \Carbon\Carbon::parse($fecha)->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}

                                    </h4>

                                    <div class="sigac-slot-grid">

                                        @foreach ($horariosFecha as $horario)
                                            @php

                                                $horaIntervalo = (string) $horario->getRawOriginal('horainicio');

                                                $horaTexto = preg_match(
                                                    '/(\d{2}):(\d{2})/',
                                                    $horaIntervalo,
                                                    $partesHora,
                                                )
                                                    ? $partesHora[1] . ':' . $partesHora[2]
                                                    : 'Hora sin información';

                                                $profesional = $horario->profesional?->user;

                                                $nombreProfesional = trim(
                                                    ($profesional?->name ?? '') .
                                                        ' ' .
                                                        ($profesional?->last_name ?? ''),
                                                );

                                                $seleccionado =
                                                    $horarioSeleccionado === (string) $horario->idhorariodispo;
                                            @endphp

                                            <label class="sigac-slot">

                                                <input type="radio" name="idhorariodispo"
                                                    value="{{ $horario->idhorariodispo }}"
                                                    class="sigac-slot-radio visually-hidden" required
                                                    @checked($seleccionado)>

                                                <span class="sigac-slot-content">

                                                    <strong class="sigac-slot-time">

                                                        <i class="bi bi-clock"></i>

                                                        {{ $horaTexto }}

                                                    </strong>

                                                    <span class="sigac-slot-info">

                                                        {{ $horario->sede?->nombre ?? 'Sede sin información' }}

                                                    </span>

                                                    <span class="sigac-slot-info">

                                                        Consultorio:
                                                        {{ $horario->consultorio ?? 'Sin información' }}

                                                    </span>

                                                    <span class="sigac-slot-info">

                                                        {{ $nombreProfesional ?: 'Profesional sin información' }}

                                                    </span>

                                                </span>

                                            </label>
                                        @endforeach

                                    </div>

                                </section>
                            @endforeach

                            <div class="form-text">

                                Seleccionar un horario diferente
                                consumirá una de las reprogramaciones
                                disponibles.

                            </div>
                        @endif

                    @endif

                    @error('idhorariodispo')
                        <div class="invalid-feedback d-block">

                            {{ $message }}

                        </div>
                    @enderror

                </x-sigac.card>

                {{-- Observaciones --}}

                <x-sigac.card title="Información adicional"
                    subtitle="Actualiza las observaciones relacionadas con tu atención.">

                    <label for="detalle" class="form-label">

                        Observaciones
                        <span class="text-muted">(opcional)</span>

                    </label>

                    <textarea id="detalle" name="detalle" class="form-control @error('detalle') is-invalid @enderror" rows="3"
                        maxlength="100" placeholder="Escribe alguna observación...">{{ old('detalle', $cita->detalle) }}</textarea>

                    <div class="form-text">

                        Máximo 100 caracteres.

                    </div>

                    @error('detalle')
                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>
                    @enderror

                    <div class="sigac-form-actions">

                        <a href="{{ route('Paciente.citas.show', $cita) }}" class="btn sigac-btn-light">

                            Volver sin guardar

                        </a>

                        <button type="submit" class="btn sigac-btn-primary">

                            <i class="bi bi-save me-1"></i>
                            Guardar cambios

                        </button>

                    </div>

                </x-sigac.card>

            </form>

            <x-sigac.alert variant="info" title="Política de modificaciones">

                Las citas pendientes pueden modificarse con al menos
                24 horas de anticipación. Cambiar únicamente las
                observaciones no consume una reprogramación.

            </x-sigac.alert>

        </div>

    </div>

@endsection
