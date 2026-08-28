@extends('layouts.dashboard')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="mb-4">
            <h1 class="fw-bold mb-1">
                Consulta y gestiona tus citas programadas.
            </h1>
        </div>

        <div class="row">
            {{-- Panel lateral --}}
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white text-center fw-bold">
                        Opciones de citas
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <button type="button" class="btn btn-warning text-dark">
                            <i class="bi bi-pencil-square me-1"></i>
                            Editar cita
                        </button>
                        <button type="button" class="btn btn-danger">
                            <i class="bi bi-calendar-x me-1"></i>
                            Cancelar cita
                        </button>
                    </div>
                </div>
            </div>

            {{-- Calendario --}}
            <div class="col-md-9 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white text-center fw-bold fs-3">
                        Calendario de citas
                    </div>
                    <div class="card-body">
                        <div id="calendario" style="min-height: 600px;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL DETALLE DE CITA -->
    <div class="modal fade" id="modalDetalleCita" tabindex="-1" aria-labelledby="modalDetalleCitaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalDetalleCitaLabel">
                        <i class="bi bi-calendar-event me-2"></i>Detalle de la Cita
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="text-muted small fw-bold text-uppercase">Paciente</label>
                            <p class="fs-5 fw-semibold mb-0 text-dark" id="modalPaciente"></p>
                        </div>

                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase">Servicio</label>
                            <p class="fw-medium mb-0 text-secondary" id="modalServicio"></p>
                        </div>

                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase">Hora de la Cita</label>
                            <p class="fw-bold mb-0 text-primary" id="modalHora"></p>
                        </div>

                        <div class="col-12">
                            <label class="text-muted small fw-bold text-uppercase">Estado</label>
                            <div><span class="badge fs-6" id="modalEstado"></span></div>
                        </div>

                        <div class="col-12">
                            <label class="text-muted small fw-bold text-uppercase">Observaciones</label>
                            <p class="bg-light p-2 rounded border text-secondary mb-0" id="modalObservacion"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function inicializarCalendario() {
            const miCalendario = document.getElementById('calendario');

            if (!miCalendario) return;

            const calendar = new FullCalendar.Calendar(miCalendario, {
                initialView: 'dayGridMonth',
                locale: 'es',
                height: 'auto',
                // Deja la hora local
                timeZone: 'local',
                displayEventTime: true,
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short'
                },

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día'
                },

                events: @json($citas ?? []),

                eventClick: function(info) {
                    const props = info.event.extendedProps;

                    // Inyección de textos en el HTML del modal
                    document.getElementById('modalPaciente').textContent = props.paciente || 'Sin paciente';
                    document.getElementById('modalServicio').textContent = props.servicio || 'Sin servicio';
                    document.getElementById('modalHora').textContent = props.hora || 'Sin hora registrada';
                    document.getElementById('modalObservacion').textContent = props.observacion || 'Sin observaciones';

                    // Formato del Badge según estado
                    const badgeEstado = document.getElementById('modalEstado');
                    badgeEstado.textContent = props.estadoCita || 'Sin estado';
                    badgeEstado.className = 'badge fs-6 ' + (
                        props.estadoCita === 'Pendiente' ? 'bg-warning text-dark' :
                        (props.estadoCita === 'Atendida' || props.estadoCita === 'Realizada') ? 'bg-success' :
                        props.estadoCita === 'Cancelada' ? 'bg-danger' : 'bg-primary'
                    );

                    // Despliegue seguro del Modal Bootstrap
                    const elModal = document.getElementById('modalDetalleCita');
                    if (elModal && typeof bootstrap !== 'undefined') {
                        const modal = bootstrap.Modal.getOrCreateInstance(elModal);
                        modal.show();
                    } else {
                        console.error('Bootstrap JS no se ha cargado correctamente.');
                    }
                }
            });

            calendar.render();
        }

        document.addEventListener('DOMContentLoaded', inicializarCalendario);
        document.addEventListener('livewire:navigated', inicializarCalendario);
    </script>
@endpush