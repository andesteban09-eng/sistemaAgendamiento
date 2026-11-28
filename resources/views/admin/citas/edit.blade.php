@extends('layouts.admin')

@section('contenido')

    @php
        $subtitulo = 'Modificar el agendamiento #' . $cita->idcita;

        $varianteReprogramacion = $puedeReprogramar
            ? 'info'
            : 'warning';

        $etiquetaReprogramacion = ($cita->numreprogramaciones ?? 0) . ' de 3';
    @endphp

    <div class="sigac-page">

        <x-sigac.page-header
            title="Editar cita"
            :subtitle="$subtitulo"
            eyebrow="SIGAC · GESTIÓN DE CITAS">

            <x-slot:actions>

                <a href="{{ route('admin.citas.show', $cita) }}"
                    class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver al detalle

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        {{-- Mensajes de error --}}

        @if (session('error'))

            <x-sigac.alert
                variant="danger"
                class="alert alert-dismissible fade show pe-5">

                {{ session('error') }}

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar">
                </button>

            </x-sigac.alert>

        @endif

        @if ($errors->any())

            <x-sigac.alert
                variant="danger"
                title="No se pudo actualizar la cita">

                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

            </x-sigac.alert>

        @endif

        <div class="row g-3 align-items-start">

            {{-- Información actual --}}

            <div class="col-12 col-lg-4">

                <x-sigac.card
                    title="Cita actual"
                    subtitle="Información del agendamiento antes de la modificación.">

                    <dl class="sigac-details">

                        <div class="sigac-details-full">

                            <dt>Paciente</dt>

                            <dd>
                                {{ $cita->paciente?->user?->name ?? 'Sin información' }}
                                {{ $cita->paciente?->user?->last_name ?? '' }}
                            </dd>

                        </div>

                        <div class="sigac-details-full">

                            <dt>Servicio</dt>

                            <dd>
                                {{ $cita->servicio?->nombre ?? 'Sin información' }}
                            </dd>

                        </div>

                        <div class="sigac-details-full">

                            <dt>Fecha y hora</dt>

                            <dd>
                                {{ $cita->fechacita?->format('d/m/Y h:i A') }}
                            </dd>

                        </div>

                        <div class="sigac-details-full">

                            <dt>Profesional</dt>

                            <dd>
                                {{ $cita->agenda?->profesional?->user?->name ?? 'Sin información' }}
                                {{ $cita->agenda?->profesional?->user?->last_name ?? '' }}
                            </dd>

                        </div>

                        <div class="sigac-details-full">

                            <dt>Sede</dt>

                            <dd>
                                {{ $cita->agenda?->sede?->nombre ?? 'Sin información' }}
                            </dd>

                        </div>

                        <div class="sigac-details-full">

                            <dt>Consultorio</dt>

                            <dd>
                                {{ $cita->agenda?->consultorio ?? 'Sin información' }}
                            </dd>

                        </div>

                        <div class="sigac-details-full">

                            <dt>Reprogramaciones realizadas</dt>

                            <dd>

                                <x-sigac.status
                                    :label="$etiquetaReprogramacion"
                                    :variant="$varianteReprogramacion"
                                />

                            </dd>

                        </div>

                    </dl>

                </x-sigac.card>

            </div>

            {{-- Formulario de modificación --}}

            <div class="col-12 col-lg-8">

                <form
                    action="{{ route('admin.citas.update', $cita) }}"
                    method="POST"
                    class="sigac-form">

                    @csrf
                    @method('PUT')

                    <x-sigac.card
                        title="Modificar agendamiento"
                        subtitle="Actualiza el horario disponible o las observaciones de la cita.">

                        <div class="sigac-form-grid">

                            {{-- Horario de la cita --}}

                            <div class="sigac-form-full">

                                <label for="idhorariodispo"
                                    class="form-label">

                                    Horario de la cita

                                </label>

                                @if ($puedeReprogramar)

                                    <select
                                        name="idhorariodispo"
                                        id="idhorariodispo"
                                        class="form-select @error('idhorariodispo') is-invalid @enderror"
                                        required>

                                        <option value="{{ $cita->idhorariodispo }}"
                                            @selected((string) old('idhorariodispo', $cita->idhorariodispo) === (string) $cita->idhorariodispo)>

                                            Mantener horario actual:
                                            {{ $cita->fechacita?->format('d/m/Y h:i A') }}

                                        </option>

                                        @foreach ($horarios as $horario)

                                            @if ((string) $horario->idhorariodispo !== (string) $cita->idhorariodispo)

                                                @php
                                                    $horaIntervalo = (string) $horario->getRawOriginal('horainicio');

                                                    $horaTexto = preg_match(
                                                        '/(\d{2}):(\d{2})/',
                                                        $horaIntervalo,
                                                        $partesHora
                                                    )
                                                        ? $partesHora[1] . ':' . $partesHora[2]
                                                        : 'Hora no disponible';
                                                @endphp

                                                <option value="{{ $horario->idhorariodispo }}"
                                                    @selected((string) old('idhorariodispo') === (string) $horario->idhorariodispo)>

                                                    {{ \Carbon\Carbon::parse($horario->fecha)->format('d/m/Y') }}
                                                    - {{ $horaTexto }}
                                                    |
                                                    {{ $horario->profesional?->user?->name ?? 'Profesional' }}
                                                    {{ $horario->profesional?->user?->last_name ?? '' }}
                                                    |
                                                    {{ $horario->sede?->nombre ?? 'Sin sede' }}

                                                </option>

                                            @endif

                                        @endforeach

                                    </select>

                                    <div class="form-text">

                                        Selecciona otro horario para reprogramar la cita
                                        o conserva el actual para editar únicamente
                                        las observaciones.

                                    </div>

                                @else

                                    <input type="hidden"
                                        name="idhorariodispo"
                                        value="{{ $cita->idhorariodispo }}">

                                    <input type="text"
                                        id="idhorariodispo"
                                        class="form-control"
                                        value="{{ $cita->fechacita?->format('d/m/Y h:i A') }}"
                                        disabled>

                                    <x-sigac.alert
                                        variant="warning"
                                        title="Límite de reprogramaciones alcanzado"
                                        class="mt-3 mb-0">

                                        Esta cita alcanzó el máximo de tres
                                        reprogramaciones. Solo puedes modificar
                                        las observaciones.

                                    </x-sigac.alert>

                                @endif

                                @error('idhorariodispo')

                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>

                            {{-- Observaciones --}}

                            <div class="sigac-form-full">

                                <label for="detalle" class="form-label">
                                    Observaciones
                                </label>

                                <textarea
                                    name="detalle"
                                    id="detalle"
                                    rows="4"
                                    maxlength="100"
                                    class="form-control @error('detalle') is-invalid @enderror"
                                    placeholder="Escribe las observaciones de la cita...">{{ old('detalle', $cita->detalle) }}</textarea>

                                <div class="form-text">
                                    Máximo 100 caracteres.
                                </div>

                                @error('detalle')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>

                        </div>

                        {{-- Acciones --}}

                        <div class="sigac-form-actions">

                            <a href="{{ route('admin.citas.show', $cita) }}"
                                class="btn btn-outline-secondary">

                                Cancelar edición

                            </a>

                            <button type="submit"
                                class="btn sigac-btn-primary">

                                <i class="bi bi-check-circle"></i>
                                Guardar cambios

                            </button>

                        </div>

                    </x-sigac.card>

                </form>

            </div>

        </div>

    </div>

@endsection
