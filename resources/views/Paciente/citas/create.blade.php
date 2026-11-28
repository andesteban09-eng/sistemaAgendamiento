@extends('layouts.dashboard')

@section('contenido')

    @php
        $catalogo = $tiposServicio
            ->map(
                fn($tipo) => [
                    'idtiposervicio' => $tipo->idtiposervicio,
                    'servicios' => $tipo->servicios
                        ->map(
                            fn($servicio) => [
                                'idservicio' => $servicio->idservicio,
                                'nombre' => $servicio->nombre,
                            ],
                        )
                        ->values(),
                ],
            )
            ->values();
    @endphp

    <div class="sigac-page sigac-booking">

        <x-sigac.page-header title="Agendar una cita" subtitle="Selecciona el servicio y el horario de tu próxima atención."
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

            @if (session('error'))
                <x-sigac.alert variant="danger">
                    {{ session('error') }}
                </x-sigac.alert>
            @endif

            @if (session('success'))
                <x-sigac.alert variant="success">
                    {{ session('success') }}
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

            {{-- Formulario de agendamiento --}}

            <form id="formAgendarCita" class="sigac-form" method="POST" action="{{ route('Paciente.citas.store') }}"
                data-tipos="{{ $catalogo->toJson() }}" data-servicio-anterior="{{ old('idservicio') }}"
                data-horario-anterior="{{ old('idhorariodispo') }}"
                data-url-horarios="{{ route('Paciente.citas.horarios', ['idservicio' => '__servicio__']) }}">

                @csrf

                {{-- Selección del servicio --}}

                <x-sigac.card title="Información del servicio" subtitle="Selecciona el tipo de atención que necesitas.">

                    <div class="sigac-form-grid">

                        <div>

                            <label for="idtiposervicio" class="form-label">

                                Tipo de servicio

                            </label>

                            <select id="idtiposervicio" name="idtiposervicio"
                                class="form-select @error('idtiposervicio') is-invalid @enderror" required>

                                <option value="">
                                    Selecciona un tipo de servicio
                                </option>

                                @foreach ($tiposServicio as $tipo)
                                    <option value="{{ $tipo->idtiposervicio }}" @selected(old('idtiposervicio') == $tipo->idtiposervicio)>

                                        {{ $tipo->nombre }}

                                    </option>
                                @endforeach

                            </select>

                            @error('idtiposervicio')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div>

                            <label for="idservicio" class="form-label">

                                Servicio

                            </label>

                            <select id="idservicio" name="idservicio"
                                class="form-select @error('idservicio') is-invalid @enderror" required disabled>

                                <option value="">
                                    Selecciona primero un tipo de servicio
                                </option>

                            </select>

                            @error('idservicio')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    @if ($tiposServicio->isEmpty())
                        <div class="mt-3">

                            <x-sigac.alert variant="warning">
                                Actualmente no hay servicios disponibles
                                para agendamiento.
                            </x-sigac.alert>

                        </div>
                    @endif

                </x-sigac.card>

                {{-- Horarios disponibles --}}

                <div id="contenedorHorarios" hidden>

                    <x-sigac.card title="Horarios disponibles" subtitle="Selecciona la fecha, hora y lugar de tu atención.">

                        <div id="horariosDisponibles" role="group" aria-label="Horarios disponibles" aria-busy="false">
                        </div>

                        <div id="mensajeHorarios" class="visually-hidden" aria-live="polite">
                        </div>

                        @error('idhorariodispo')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </x-sigac.card>

                </div>

                {{-- Observaciones --}}

                <div id="contenedorDetalle" hidden>

                    <x-sigac.card title="Información adicional"
                        subtitle="Puedes agregar observaciones relacionadas con tu cita.">

                        <label for="detalle" class="form-label">

                            Observaciones
                            <span class="text-muted">(opcional)</span>

                        </label>

                        <textarea id="detalle" name="detalle" class="form-control @error('detalle') is-invalid @enderror" rows="3"
                            maxlength="100" placeholder="Escribe alguna observación...">{{ old('detalle') }}</textarea>

                        <div class="form-text">
                            Máximo 100 caracteres.
                        </div>

                        @error('detalle')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="sigac-form-actions">

                            <a href="{{ route('Paciente.citas.index') }}" class="btn sigac-btn-light">

                                Volver

                            </a>

                            <button type="submit" id="btnContinuar" class="btn sigac-btn-primary" disabled>

                                <i class="bi bi-calendar-check me-1"></i>
                                Agendar cita

                            </button>

                        </div>

                    </x-sigac.card>

                </div>

            </form>

            <x-sigac.alert variant="info" title="Antes de agendar">

                Verifica que el servicio, la sede y el horario
                seleccionados correspondan a tus necesidades.
                La disponibilidad será comprobada nuevamente
                al registrar la cita.

            </x-sigac.alert>

        </div>

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/agendamiento-paciente.js') }}" data-navigate-once></script>
@endpush
