@extends('layouts.dashboard')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="mb-4">
            <h1 class="fw-bold mb-1">               
                Consulta y gestiona tus citas programadas.
            </h1>
        </div>

        <div class="row">
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
                            <i class="bi bi-trash me-1"></i>
                            Eliminar cita
                        </button>
                    </div>
                </div>
            </div>

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
@endsection

@push('scripts')
    <!-- CDN del calendario que se descarga automáticamente(no hace falta librerias en el proyecto) -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendario');

            if (!calendarEl) {
                console.error("No se encontró el elemento #calendar en el DOM.");
                return;
            }

            // Inicialización de la librería FullCalendar
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                height: 'auto',
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
                // Insertar la consulta de las citas
                events: @json($citas ?? []),

                // Evento al hacer clic sobre una cita agendada
                eventClick: function(info) {
                    alert(
                        'Cita: ' + info.event.title +
                        '\nEstado: ' + (info.event.extendedProps.estadoCita || 'Sin estado')
                    );
                }
            });

            // Con esto se muestra en la pantalla
            calendar.render();
        });
    </script>
@endpush