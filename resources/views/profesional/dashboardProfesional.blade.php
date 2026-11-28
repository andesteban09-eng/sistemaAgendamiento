@extends('layouts.dashboard')

@section('contenido')

    @php
        $subtitulo = 'Consulta y gestiona tus citas programadas.';
        $indicadores = [
            ['Citas de hoy', $citasHoy, 'calendar-event', 'navy'],
            ['Pendientes', $citasPendientes, 'clock', 'warning'],
            ['Confirmadas', $citasConfirmadas, 'person-check', 'info'],
            ['Realizadas', $citasRealizadas, 'check-circle', 'success'],
        ];

        $estados = [
            ['Pendiente', 'warning'],
            ['Confirmada', 'info'],
            ['Realizada', 'success'],
            ['Cancelada', 'danger'],
        ];
    @endphp

    <div class="sigac-page sigac-profesional">

        <x-sigac.page-header title="Panel del profesional" :subtitle="$subtitulo" eyebrow="SIGAC · GESTIÓN ASISTENCIAL" />

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

        @unless ($perfilActivo)
            <x-sigac.alert variant="warning" title="Perfil profesional no disponible">

                Tu perfil no está asociado o no se encuentra activo.
                Comunícate con el administrador.

            </x-sigac.alert>
        @endunless

        {{-- Indicadores --}}

        <section class="sigac-metrics sigac-grid-four">

            @foreach ($indicadores as [$label, $value, $icon, $color])
                <x-sigac.metric-card :label="$label" :value="$value" :icon="$icon" :color="$color" />
            @endforeach

        </section>

        {{-- Calendario: SIEMPRE VISIBLE --}}

        <x-sigac.card title="Calendario de citas" subtitle="Consulta tu programación mensual, semanal o diaria.">

            <x-slot:actions>

                <div class="d-flex flex-wrap gap-2">

                    @foreach ($estados as [$label, $variant])
                        <x-sigac.status :label="$label" :variant="$variant" />
                    @endforeach

                </div>

            </x-slot:actions>

            <div class="sigac-calendar">

                <div id="calendario" data-eventos="{{ $citas->toJson() }}" style="min-height: 600px;">
                </div>

            </div>

            @if ($citas->isEmpty())
                <p class="sigac-description mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Actualmente no tienes citas programadas.
                </p>
            @else
                <p class="sigac-description mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Selecciona una cita para consultar sus detalles.
                </p>
            @endif

        </x-sigac.card>

        {{-- Información complementaria --}}

        <div class="row g-3 mb-3">

            <div class="col-12 col-xl-5">

                <x-sigac.card title="Próxima cita" subtitle="Siguiente atención programada." class="h-100 mb-0">

                    @if ($proximaCita)
                        <div class="sigac-identity">

                            <div class="sigac-icon sigac-icon-info">
                                <i class="bi bi-person-badge"></i>
                            </div>

                            <div class="sigac-identity-content">

                                <h2>{{ $proximaCita['paciente'] }}</h2>

                                <p class="sigac-description mb-0">
                                    Cita #{{ $proximaCita['id'] }}
                                </p>

                            </div>

                        </div>

                        <hr>

                        <dl class="sigac-details">

                            <div>
                                <dt>Servicio</dt>
                                <dd>{{ $proximaCita['servicio'] }}</dd>
                            </div>

                            <div>
                                <dt>Fecha y hora</dt>
                                <dd>
                                    {{ $proximaCita['fecha'] }}
                                    · {{ $proximaCita['hora'] }}
                                </dd>
                            </div>

                            <div>
                                <dt>Sede</dt>
                                <dd>{{ $proximaCita['sede'] ?? 'Sin sede' }}</dd>
                            </div>

                            <div>
                                <dt>Consultorio</dt>
                                <dd>
                                    {{ $proximaCita['consultorio'] ?? 'Sin asignar' }}
                                </dd>
                            </div>

                        </dl>

                        <button type="button" class="btn sigac-btn-primary mt-3" data-cita-id="{{ $proximaCita['id'] }}">

                            <i class="bi bi-eye me-1"></i>
                            Consultar cita

                        </button>
                    @else
                        <x-sigac.empty-state title="Sin próximas citas"
                            description="No tienes nuevas atenciones programadas." icon="calendar-check" />
                    @endif

                </x-sigac.card>

            </div>

            <div class="col-12 col-xl-7">

                <x-sigac.card title="Pacientes por atender" subtitle="Citas confirmadas correspondientes a hoy."
                    class="h-100 mb-0">

                    @if ($citasPorAtender->isNotEmpty())
                        <div class="sigac-attention-queue">

                            @foreach ($citasPorAtender as $cita)
                                <article class="sigac-attention-item">

                                    <div class="sigac-icon sigac-icon-info">
                                        <i class="bi bi-person"></i>
                                    </div>

                                    <div class="sigac-attention-content">

                                        <strong>{{ $cita['paciente'] }}</strong>

                                        <p>{{ $cita['servicio'] }}</p>

                                        <span class="sigac-description">
                                            {{ $cita['hora'] }}
                                            · {{ $cita['consultorio'] }}
                                        </span>

                                    </div>

                                    <button type="button" class="btn btn-sm sigac-btn-primary"
                                        data-cita-id="{{ $cita['id'] }}">

                                        Ver cita

                                    </button>

                                </article>
                            @endforeach

                        </div>
                    @else
                        <x-sigac.empty-state title="Sin pacientes por atender"
                            description="Aquí aparecerán las citas confirmadas de hoy." icon="person-check" />
                    @endif

                </x-sigac.card>

            </div>

        </div>

    </div>

    {{-- Modal de detalle --}}

    <div class="modal fade" id="modalDetalleCita" tabindex="-1" aria-labelledby="modalDetalleCitaLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">

            <div class="modal-content border-0 shadow">

                <div class="modal-header sigac-modal-header">

                    <h2 class="modal-title fs-5" id="modalDetalleCitaLabel">
                        <i class="bi bi-calendar-event me-2"></i>
                        Detalle de la cita
                    </h2>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar">
                    </button>

                </div>

                <div class="modal-body p-4">

                    <div class="sigac-identity">

                        <div class="sigac-icon sigac-icon-info">
                            <i class="bi bi-person"></i>
                        </div>

                        <div class="sigac-identity-content">
                            <h2 id="modalPaciente"></h2>
                            <p class="sigac-description mb-0" id="modalIdentificador">
                            </p>
                        </div>

                    </div>

                    <hr>

                    <dl class="sigac-details">

                        <div>
                            <dt>Servicio</dt>
                            <dd id="modalServicio"></dd>
                        </div>

                        <div>
                            <dt>Estado</dt>
                            <dd>
                                <span id="modalEstado" class="sigac-status"></span>
                            </dd>
                        </div>

                        <div>
                            <dt>Fecha</dt>
                            <dd id="modalFecha"></dd>
                        </div>

                        <div>
                            <dt>Hora</dt>
                            <dd id="modalHora"></dd>
                        </div>

                        <div>
                            <dt>Sede</dt>
                            <dd id="modalSede"></dd>
                        </div>

                        <div>
                            <dt>Consultorio</dt>
                            <dd id="modalConsultorio"></dd>
                        </div>

                        <div class="sigac-details-full">
                            <dt>Observaciones</dt>
                            <dd class="bg-light border rounded p-3 fw-normal" id="modalObservacion">
                            </dd>
                        </div>

                    </dl>

                    <p class="sigac-description border-top pt-3 mt-3 mb-0" id="modalMensajeEstado">
                    </p>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">

                        Cerrar

                    </button>

                    <form id="formRealizarCita" method="POST" hidden
                        onsubmit="return confirm('¿Confirma que ha finalizado la atención del paciente?');">

                        @csrf
                        @method('PATCH')

                        <button type="submit" id="btnRealizarCita" class="btn btn-success" disabled>

                            <i class="bi bi-check-circle me-1"></i>
                            Finalizar atención

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/calendario-profesional.js') }}" data-navigate-once></script>
@endpush
