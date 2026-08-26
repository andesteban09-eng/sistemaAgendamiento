@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        ```
        <div class="mb-4">
            <h1 class="fw-bold mb-1">
                Registrar disponibilidad
            </h1>

            <p class="text-muted mb-0">
                Asigne un horario disponible a un profesional de la salud.
            </p>
        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                <form action="{{ route('auxiliar.agenda.store') }}" method="POST">

                    @csrf

                    <div class="row g-4">

                        {{-- PROFESIONAL --}}

                        <div class="col-md-6">

                            <label for="IDPROFESIONALSALUD" class="form-label fw-semibold">
                                Profesional
                            </label>

                            <select name="IDPROFESIONALSALUD" id="IDPROFESIONALSALUD"
                                class="form-select @error('IDPROFESIONALSALUD') is-invalid @enderror" required>

                                <option value="">
                                    Seleccione un profesional
                                </option>

                                @foreach ($profesionales as $profesional)
                                    <option value="{{ $profesional->idprofesionalsalud }}" @selected(old('IDPROFESIONALSALUD') == $profesional->idprofesionalsalud)>
                                        {{ $profesional->user->name }}
                                        {{ $profesional->user->last_name }}
                                    </option>
                                @endforeach

                            </select>

                            @error('IDPROFESIONALSALUD')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- SERVICIO --}}

                        <div class="col-md-6">

                            <label for="IDSERVICIO" class="form-label fw-semibold">
                                Servicio
                            </label>

                            <select name="IDSERVICIO" id="IDSERVICIO"
                                class="form-select @error('IDSERVICIO') is-invalid @enderror" required disabled>

                                <option value="">
                                    Seleccione primero un profesional
                                </option>

                            </select>

                            @error('IDSERVICIO')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div id="mensaje-servicio" class="form-text">
                                Seleccione un profesional para consultar sus servicios.
                            </div>

                        </div>


                        {{-- SEDE --}}

                        <div class="col-md-6">

                            <label for="IDSEDE" class="form-label fw-semibold">
                                Sede
                            </label>

                            <select name="IDSEDE" id="IDSEDE" class="form-select @error('IDSEDE') is-invalid @enderror"
                                required>

                                <option value="">
                                    Seleccione una sede
                                </option>

                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->idsede }}" @selected(old('IDSEDE') == $sede->idsede)>
                                        {{ $sede->nombre }} — {{ $sede->ciudad }}
                                    </option>
                                @endforeach

                            </select>

                            @error('IDSEDE')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- FECHA --}}

                        <div class="col-md-6">

                            <label for="FECHA" class="form-label fw-semibold">
                                Fecha
                            </label>

                            <input type="date" name="FECHA" id="FECHA" value="{{ old('FECHA') }}"
                                class="form-control @error('FECHA') is-invalid @enderror" required>

                            @error('FECHA')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- HORA --}}

                        <div class="col-md-6">

                            <label for="HORAINICIO" class="form-label fw-semibold">
                                Hora de inicio
                            </label>

                            <input type="time" name="HORAINICIO" id="HORAINICIO" value="{{ old('HORAINICIO') }}"
                                class="form-control @error('HORAINICIO') is-invalid @enderror" required>

                            @error('HORAINICIO')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- CONSULTORIO --}}

                        <div class="col-md-6">

                            <label for="CONSULTORIO" class="form-label fw-semibold">
                                Consultorio
                            </label>

                            <input type="text" name="CONSULTORIO" id="CONSULTORIO" value="{{ old('CONSULTORIO') }}"
                                class="form-control @error('CONSULTORIO') is-invalid @enderror" maxlength="45"
                                placeholder="Ej. Consultorio 101" required>

                            @error('CONSULTORIO')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    <hr class="my-4">


                    <div class="d-flex gap-2">

                        <a href="{{ route('auxiliar.agenda.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary">

                            <i class="bi bi-calendar-plus me-1"></i>

                            Registrar disponibilidad

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <script>
        const serviciosPorProfesional = {};

        @foreach ($profesionales as $profesional)

            serviciosPorProfesional["{{ $profesional->idprofesionalsalud }}"] = [];

            @foreach ($profesional->perfilesServicio as $perfil)

                @if ($perfil->estadoperfil === 'Activo' && $perfil->servicio && $perfil->servicio->estadoservicio === 'Activo')

                    serviciosPorProfesional["{{ $profesional->idprofesionalsalud }}"].push({
                        id: "{{ $perfil->servicio->idservicio }}",
                        nombre: @json($perfil->servicio->nombre),
                        tipo: @json($perfil->tipoServicio?->nombre)
                    });
                @endif
            @endforeach
        @endforeach


        const profesionalSelect =
            document.getElementById('IDPROFESIONALSALUD');

        const servicioSelect =
            document.getElementById('IDSERVICIO');

        const mensajeServicio =
            document.getElementById('mensaje-servicio');


        function cargarServicios() {

            const profesionalId = profesionalSelect.value;

            servicioSelect.innerHTML = '';


            if (!profesionalId) {

                servicioSelect.disabled = true;

                const opcion =
                    document.createElement('option');

                opcion.value = '';
                opcion.textContent =
                    'Seleccione primero un profesional';

                servicioSelect.appendChild(opcion);

                mensajeServicio.textContent =
                    'Seleccione un profesional para consultar sus servicios.';

                return;
            }


            const servicios =
                serviciosPorProfesional[profesionalId] || [];


            const opcionInicial =
                document.createElement('option');

            opcionInicial.value = '';
            opcionInicial.textContent =
                'Seleccione un servicio';

            servicioSelect.appendChild(opcionInicial);


            if (servicios.length === 0) {

                servicioSelect.disabled = true;

                mensajeServicio.textContent =
                    'Este profesional no tiene servicios activos asignados.';

                return;
            }


            servicioSelect.disabled = false;


            servicios.forEach(function(servicio) {

                const opcion =
                    document.createElement('option');

                opcion.value = servicio.id;

                if (servicio.tipo) {

                    opcion.textContent =
                        servicio.nombre + ' — ' + servicio.tipo;

                } else {

                    opcion.textContent =
                        servicio.nombre;

                }

                servicioSelect.appendChild(opcion);

            });


            mensajeServicio.textContent =
                servicios.length +
                ' servicio(s) autorizado(s) para este profesional.';
        }


        profesionalSelect.addEventListener(
            'change',
            cargarServicios
        );


        document.addEventListener(
            'DOMContentLoaded',
            function() {

                if (profesionalSelect.value) {

                    cargarServicios();

                }

            }
        );
    </script>
@endsection
