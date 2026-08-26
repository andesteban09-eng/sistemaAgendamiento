<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Profesional - Agenda</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

    <link rel="stylesheet" href="{{ asset('css/principal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">

    <header class="bg-white shadow-sm">
        @include('profesionales.marcadores.headermedico')
    </header>

    <div class="container-fluid mt-4 mb-4 flex-grow-1">
        <div class="row">
            <!-- PANEL LATERAL -->
            <div class="col-md-3 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header bg-primary text-white text-center fw-bold">
                        Opciones de citas
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <button class="btn btn-success w-100">Nueva cita</button>
                        <button class="btn btn-warning text-dark w-100">Editar cita</button>
                        <button class="btn btn-danger w-100">Eliminar cita</button>
                    </div>
                </div>
            </div>

            <!-- CALENDARIO -->
            <div class="col-md-9">
                <div class="card shadow">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');

            let calendar = new FullCalendar.Calendar(calendarEl, {
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
                // Pasa el listado de citas desde PHP a JavaScript
                events: @json($citas ?? []),
                
                eventClick: function(info) {
                    alert('Cita: ' + info.event.title + '\nEstado: ' + info.event.extendedProps.estadoCita);
                }
            });

            calendar.render();
        });
    </script>    

    <footer class="bg-light text-center text-lg-start mt-auto">
        @include('profesionales.marcadores.footer')
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>