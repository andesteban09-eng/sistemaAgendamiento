@extends('layouts.dashboard')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="mb-4">

            <h1 class="fw-bold mb-1">
                Dashboard profesional
            </h1>

            <p class="text-muted mb-0">
                Consulta y gestiona tus citas programadas.
            </p>

        </div>

        <div class="row">

            {{-- panel lateral --}}
            <div class="col-md-3 mb-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-header bg-primary text-white text-center fw-bold">
                        Opciones de citas
                    </div>

                    <div class="card-body d-flex flex-column gap-2">

                        <button type="button" class="btn btn-success w-100">
                            <i class="bi bi-plus-lg me-1"></i>
                            Nueva cita
                        </button>

                        <button type="button" class="btn btn-warning text-dark w-100">
                            <i class="bi bi-pencil-square me-1"></i>
                            Editar cita
                        </button>

                        <button type="button" class="btn btn-danger w-100">
                            <i class="bi bi-trash me-1"></i>
                            Eliminar cita
                        </button>

                    </div>

                </div>

            </div>

            {{-- calendario --}}
            <div class="col-md-9">

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white text-center fw-bold fs-5">
                        Calendario de citas
                    </div>

                    <div class="card-body">

                        <div id="calendar"></div>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {

                initialView: 'dayGridMonth',

                locale: 'es',

                height: '75vh',

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

                    alert(
                        'Cita: ' +
                        info.event.title +
                        '\nEstado: ' +
                        info.event.extendedProps.estadocita
                    );

                }

            });

            calendar.render();

        });
    </script>
@endpush
