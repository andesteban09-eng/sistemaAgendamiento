@extends('layouts.dashboard')

@section('contenido')
    <div class="container py-4">
        <div class="mb-4">

            <h1 class="fw-bold mb-1">
                Agendar una cita
            </h1>

            <p class="text-muted mb-0">
                Selecciona el servicio que necesitas para consultar
                las disponibilidades.
            </p>

        </div>


        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">
                @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>

                        <div>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>

                        <div>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-4" role="alert">

                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>
                @endif

                {{-- ===================================================== --}}
                {{-- FORMULARIO --}}
                {{-- ===================================================== --}}

                <form method="POST" action="{{ route('Paciente.citas.store') }}">
                    @csrf

                    {{-- ================================================= --}}
                    {{-- TIPO DE SERVICIO --}}
                    {{-- ================================================= --}}

                    <div class="mb-4">

                        <label for="idtiposervicio" class="form-label fw-bold">
                            Tipo de servicio
                        </label>

                        <select id="idtiposervicio" name="idtiposervicio" class="form-select">

                            <option value="">
                                Selecciona un tipo de servicio
                            </option>

                            @foreach ($tiposServicio as $tipo)
                                <option value="{{ $tipo->idtiposervicio }}">
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- ================================================= --}}
                    {{-- SERVICIO --}}
                    {{-- ================================================= --}}

                    <div class="mb-4">

                        <label for="idservicio" class="form-label fw-bold">
                            Servicio
                        </label>

                        <select id="idservicio" name="idservicio" class="form-select" disabled>

                            <option value="">
                                Selecciona primero un tipo de servicio
                            </option>

                        </select>

                    </div>


                    {{-- ================================================= --}}
                    {{-- HORARIOS --}}
                    {{-- ================================================= --}}

                    <div id="contenedorHorarios" class="mb-4" style="display: none;">

                        <label class="form-label fw-bold">
                            Horarios disponibles
                        </label>

                        <div id="horariosDisponibles"></div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- DETALLE --}}
                    {{-- ================================================= --}}

                    <div id="contenedorDetalle" class="mb-4" style="display: none;">

                        <label for="detalle" class="form-label fw-bold">

                            Detalle
                        </label>

                        <textarea id="detalle" name="detalle" class="form-control" rows="3" maxlength="100"
                            placeholder="Escribe alguna observación..."></textarea>

                    </div>


                    {{-- ================================================= --}}
                    {{-- BOTÓN --}}
                    {{-- ================================================= --}}

                    <div id="contenedorContinuar" class="d-flex justify-content-end" style="display: none !important;">

                        <button type="submit" id="btnContinuar" class="btn btn-primary" disabled>
                            Continuar
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>
        const tiposServicio = @json($tiposServicio);

        const tipoServicioSelect =
            document.getElementById('idtiposervicio');

        const servicioSelect =
            document.getElementById('idservicio');

        const contenedorHorarios =
            document.getElementById('contenedorHorarios');

        const horariosDisponibles =
            document.getElementById('horariosDisponibles');

        const contenedorDetalle =
            document.getElementById('contenedorDetalle');

        const contenedorContinuar =
            document.getElementById('contenedorContinuar');

        const btnContinuar =
            document.getElementById('btnContinuar');


        /*
        |--------------------------------------------------------------------------
        | TIPO DE SERVICIO → SERVICIOS
        |--------------------------------------------------------------------------
        */

        tipoServicioSelect.addEventListener('change', function() {

            const tipoSeleccionado = this.value;


            // Reiniciar servicio
            servicioSelect.innerHTML = '';

            // Reiniciar horarios
            horariosDisponibles.innerHTML = '';

            contenedorHorarios.style.display = 'none';
            contenedorDetalle.style.display = 'none';
            contenedorContinuar.style.display = 'none';

            btnContinuar.disabled = true;


            if (!tipoSeleccionado) {

                servicioSelect.disabled = true;

                const option =
                    document.createElement('option');

                option.value = '';
                option.textContent =
                    'Selecciona primero un tipo de servicio';

                servicioSelect.appendChild(option);

                return;
            }


            const tipo =
                tiposServicio.find(function(tipo) {

                    return String(tipo.idtiposervicio) ===
                        String(tipoSeleccionado);

                });


            if (
                !tipo ||
                !tipo.servicios ||
                tipo.servicios.length === 0
            ) {

                servicioSelect.disabled = true;

                const option =
                    document.createElement('option');

                option.value = '';
                option.textContent =
                    'No hay servicios disponibles';

                servicioSelect.appendChild(option);

                return;
            }


            const optionInicial =
                document.createElement('option');

            optionInicial.value = '';
            optionInicial.textContent =
                'Selecciona un servicio';

            servicioSelect.appendChild(optionInicial);


            tipo.servicios.forEach(function(servicio) {

                const option =
                    document.createElement('option');

                option.value =
                    servicio.idservicio;

                option.textContent =
                    servicio.nombre;

                servicioSelect.appendChild(option);

            });


            servicioSelect.disabled = false;

        });


        /*
        |--------------------------------------------------------------------------
        | SERVICIO → HORARIOS
        |--------------------------------------------------------------------------
        */

        servicioSelect.addEventListener('change', function() {

            const idservicio = this.value;


            // Limpiar resultados anteriores
            horariosDisponibles.innerHTML = '';

            contenedorHorarios.style.display = 'none';
            contenedorDetalle.style.display = 'none';
            contenedorContinuar.style.display = 'none';

            btnContinuar.disabled = true;


            if (!idservicio) {
                return;
            }


            // Mensaje mientras se consulta
            contenedorHorarios.style.display = 'block';

            horariosDisponibles.innerHTML = `
            <div class="text-center py-4">
                <div
                    class="spinner-border text-primary"
                    role="status">
                </div>

                <p class="text-muted mt-2 mb-0">
                    Consultando horarios disponibles...
                </p>
            </div>
        `;


            /*
            |--------------------------------------------------------------------------
            | CONSULTAR HORARIOS
            |--------------------------------------------------------------------------
            */

            fetch(
                    `{{ url('/paciente/citas/horarios') }}/${idservicio}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                )
                .then(function(response) {

                    if (!response.ok) {
                        throw new Error(
                            'No fue posible consultar los horarios.'
                        );
                    }

                    return response.json();

                })
                .then(function(horarios) {

                    mostrarHorarios(horarios);

                })
                .catch(function(error) {

                    console.error(error);

                    horariosDisponibles.innerHTML = `
                <div class="alert alert-danger">
                    No fue posible consultar los horarios disponibles.
                </div>
            `;

                });

        });


        /*
        |--------------------------------------------------------------------------
        | MOSTRAR HORARIOS
        |--------------------------------------------------------------------------
        */

        function mostrarHorarios(horarios) {

            horariosDisponibles.innerHTML = '';


            if (!horarios || horarios.length === 0) {

                horariosDisponibles.innerHTML = `
                <div class="alert alert-info">
                    No hay horarios disponibles para este servicio.
                </div>
            `;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | AGRUPAR HORARIOS POR FECHA
            |--------------------------------------------------------------------------
            */

            const grupos = {};


            horarios.forEach(function(horario) {

                const fecha =
                    horario.fecha.substring(0, 10);


                if (!grupos[fecha]) {
                    grupos[fecha] = [];
                }


                grupos[fecha].push(horario);

            });


            /*
            |--------------------------------------------------------------------------
            | CREAR GRUPOS
            |--------------------------------------------------------------------------
            */

            Object.keys(grupos).forEach(function(fecha) {

                const card =
                    document.createElement('div');

                card.className =
                    'card border-0 shadow-sm mb-3';


                const header =
                    document.createElement('div');

                header.className =
                    'card-header bg-light fw-bold';

                header.innerHTML =
                    `<i class="bi bi-calendar-event me-2"></i>
                 ${formatearFecha(fecha)}`;


                const body =
                    document.createElement('div');

                body.className =
                    'card-body';


                const contenedor =
                    document.createElement('div');

                contenedor.className =
                    'd-flex flex-wrap gap-2';


                /*
                |--------------------------------------------------------------------------
                | HORARIOS DEL DÍA
                |--------------------------------------------------------------------------
                */

                grupos[fecha].forEach(function(horario) {

                    const label =
                        document.createElement('label');

                    label.className =
                        'mb-0';


                    const radio =
                        document.createElement('input');

                    radio.type = 'radio';

                    radio.name = 'idhorariodispo';

                    radio.value =
                        horario.idhorariodispo;

                    radio.className =
                        'd-none';


                    const boton =
                        document.createElement('span');

                    boton.className =
                        'btn btn-outline-primary btn-sm';

                    boton.innerHTML =
                        `<i class="bi bi-clock me-1"></i>
                     ${formatearHora(horario.horainicio)}
                     - Consultorio ${horario.consultorio}`;


                    radio.addEventListener('change', function() {

                        document
                            .querySelectorAll(
                                'input[name="idhorariodispo"]'
                            )
                            .forEach(function(input) {

                                input.nextElementSibling
                                    .classList.remove('active');

                            });


                        boton.classList.add('active');

                        contenedorDetalle.style.display =
                            'block';

                        contenedorContinuar.style.setProperty(
                            'display',
                            'flex',
                            'important'
                        );

                        btnContinuar.disabled = false;

                    });


                    label.appendChild(radio);

                    label.appendChild(boton);

                    contenedor.appendChild(label);

                });


                body.appendChild(contenedor);

                card.appendChild(header);

                card.appendChild(body);

                horariosDisponibles.appendChild(card);

            });

        }


        /*
        |--------------------------------------------------------------------------
        | FORMATEAR FECHA
        |--------------------------------------------------------------------------
        */

        function formatearFecha(fecha) {

            const partes =
                fecha.split('-');

            const fechaObjeto =
                new Date(
                    partes[0],
                    partes[1] - 1,
                    partes[2]
                );


            return fechaObjeto.toLocaleDateString(
                'es-CO', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FORMATEAR HORA
        |--------------------------------------------------------------------------
        */

        function formatearHora(hora) {

            if (!hora) {
                return '';
            }


            /*
            | Oracle puede devolver el INTERVAL como
            | "0 08:30:00" dependiendo del driver.
            */

            const partes =
                hora.match(/(\d{2}):(\d{2})/);


            if (!partes) {
                return hora;
            }


            return `${partes[1]}:${partes[2]}`;

        }
    </script>
@endsection
