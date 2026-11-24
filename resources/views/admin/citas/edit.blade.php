
@extends('layouts.admin')

@section('contenido')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">
                <i class="bi bi-pencil-square me-2"></i>
                Editar cita
            </h1>

            <p class="text-muted mb-0">
                Modificar el agendamiento #{{ $cita->idcita }}
            </p>
        </div>

        <a href="{{ route('admin.citas.show', $cita) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver al detalle
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>No se pudo actualizar la cita.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- Información actual --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-calendar-check me-2"></i>
                        Cita actual
                    </h5>

                    <p class="mb-3">
                        <strong>Paciente</strong>
                        <span class="d-block text-muted">
                            {{ $cita->paciente?->user?->name ?? 'Sin información' }}
                            {{ $cita->paciente?->user?->last_name ?? '' }}
                        </span>
                    </p>

                    <p class="mb-3">
                        <strong>Servicio</strong>
                        <span class="d-block text-muted">
                            {{ $cita->servicio?->nombre ?? 'Sin información' }}
                        </span>
                    </p>

                    <p class="mb-3">
                        <strong>Fecha y hora</strong>
                        <span class="d-block text-muted">
                            {{ $cita->fechacita?->format('d/m/Y h:i A') }}
                        </span>
                    </p>

                    <p class="mb-3">
                        <strong>Profesional</strong>
                        <span class="d-block text-muted">
                            {{ $cita->agenda?->profesional?->user?->name ?? 'Sin información' }}
                            {{ $cita->agenda?->profesional?->user?->last_name ?? '' }}
                        </span>
                    </p>

                    <p class="mb-3">
                        <strong>Sede</strong>
                        <span class="d-block text-muted">
                            {{ $cita->agenda?->sede?->nombre ?? 'Sin información' }}
                        </span>
                    </p>

                    <p class="mb-3">
                        <strong>Consultorio</strong>
                        <span class="d-block text-muted">
                            {{ $cita->agenda?->consultorio ?? 'Sin información' }}
                        </span>
                    </p>

                    <p class="mb-0">
                        <strong>Reprogramaciones realizadas</strong>
                        <span class="d-block mt-1">
                            <span class="badge bg-{{ $puedeReprogramar ? 'primary' : 'warning text-dark' }}">
                                {{ $cita->numreprogramaciones ?? 0 }} de 3
                            </span>
                        </span>
                    </p>

                </div>
            </div>
        </div>

        {{-- Formulario --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-calendar-plus me-2"></i>
                        Modificar agendamiento
                    </h5>

                    <form action="{{ route('admin.citas.update', $cita) }}"
                          method="POST">

                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="idhorariodispo" class="form-label fw-semibold">
                                Horario de la cita
                            </label>

                            @if ($puedeReprogramar)

                                <select name="idhorariodispo"
                                        id="idhorariodispo"
                                        class="form-select @error('idhorariodispo') is-invalid @enderror"
                                        required>

                                    <option value="{{ $cita->idhorariodispo }}"
                                        @selected(
                                            (string) old('idhorariodispo', $cita->idhorariodispo)
                                            === (string) $cita->idhorariodispo
                                        )>
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
                                                @selected(
                                                    (string) old('idhorariodispo')
                                                    === (string) $horario->idhorariodispo
                                                )>
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
                                    o conserva el actual para editar únicamente las observaciones.
                                </div>

                            @else

                                <input type="hidden"
                                       name="idhorariodispo"
                                       value="{{ $cita->idhorariodispo }}">

                                <input type="text"
                                       class="form-control"
                                       value="{{ $cita->fechacita?->format('d/m/Y h:i A') }}"
                                       disabled>

                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Esta cita alcanzó el máximo de tres reprogramaciones.
                                    Solo puedes modificar las observaciones.
                                </div>

                            @endif

                            @error('idhorariodispo')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="detalle" class="form-label fw-semibold">
                                Observaciones
                            </label>

                            <textarea name="detalle"
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

                        <div class="d-flex justify-content-end gap-2">

                            <a href="{{ route('admin.citas.show', $cita) }}"
                               class="btn btn-outline-secondary">
                                Cancelar edición
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Guardar cambios
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
