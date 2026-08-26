@extends('layouts.admin')

@section('contenido')

    <div class="container-fluid px-4 py-4">

        <div class="mb-4">

            <h1 class="fw-bold">
                Registrar profesional
            </h1>

            <p class="text-muted">
                Registra los datos personales, profesionales y los servicios que puede prestar.
            </p>

        </div>


        @if ($errors->any())

            <div class="alert alert-danger">

                <strong>Hay errores en el formulario:</strong>

                <ul class="mb-0 mt-2">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('admin.profesionales.store') }}"
        >

            @csrf


            {{-- ========================================================= --}}
            {{-- DATOS DE ACCESO --}}
            {{-- ========================================================= --}}

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white">

                    <h5 class="mb-0 fw-bold">
                        Datos de acceso
                    </h5>

                </div>


                <div class="card-body">

                    <div class="row g-3">


                        <div class="col-md-6">

                            <label class="form-label">
                                Nombre
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                required
                            >

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Apellido
                            </label>

                            <input
                                type="text"
                                name="last_name"
                                class="form-control @error('last_name') is-invalid @enderror"
                                value="{{ old('last_name') }}"
                                required
                            >

                            @error('last_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                required
                            >

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Contraseña
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                            >

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- INFORMACIÓN PROFESIONAL --}}
            {{-- ========================================================= --}}

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white">

                    <h5 class="mb-0 fw-bold">
                        Información profesional
                    </h5>

                </div>


                <div class="card-body">

                    <div class="row g-3">


                        <div class="col-md-6">

                            <label class="form-label">
                                Tipo de documento
                            </label>

                            <select
                                name="tipodoc"
                                class="form-select @error('tipodoc') is-invalid @enderror"
                                required
                            >

                                <option value="">
                                    Seleccione...
                                </option>

                                @foreach ([
                                    'Cedula Ciudadania' => 'Cédula de Ciudadanía',
                                    'Tarjeta Identidad' => 'Tarjeta de Identidad',
                                    'Cedula Extranjeria' => 'Cédula de Extranjería',
                                    'Pasaporte' => 'Pasaporte',
                                    'Registro Civil' => 'Registro Civil',
                                    'Permiso Proteccion Temporal' => 'Permiso de Protección Temporal',
                                    'Otro' => 'Otro',
                                ] as $valor => $texto)

                                    <option
                                        value="{{ $valor }}"
                                        @selected(old('tipodoc') === $valor)
                                    >
                                        {{ $texto }}
                                    </option>

                                @endforeach

                            </select>

                            @error('tipodoc')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Número de documento
                            </label>

                            <input
                                type="text"
                                name="numdoc"
                                class="form-control @error('numdoc') is-invalid @enderror"
                                value="{{ old('numdoc') }}"
                                required
                            >

                            @error('numdoc')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Teléfono
                            </label>

                            <input
                                type="text"
                                name="telefono"
                                class="form-control @error('telefono') is-invalid @enderror"
                                value="{{ old('telefono') }}"
                                required
                            >

                            @error('telefono')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- SERVICIOS --}}
            {{-- ========================================================= --}}

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white">

                    <h5 class="mb-1 fw-bold">
                        Servicios que puede prestar
                    </h5>

                    <p class="text-muted mb-0 small">
                        Selecciona los servicios que este profesional está autorizado para prestar.
                    </p>

                </div>


                <div class="card-body">


                    @forelse ($servicios->groupBy('tipoServicio.nombre') as $nombreTipo => $serviciosTipo)

                        <div class="mb-4">

                            <h6 class="fw-bold border-bottom pb-2 mb-3">
                                {{ $nombreTipo }}
                            </h6>


                            <div class="row g-3">

                                @foreach ($serviciosTipo as $servicio)

                                    <div class="col-md-6 col-lg-4">

                                        <div class="form-check">

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="servicios[]"
                                                value="{{ $servicio->idservicio }}"
                                                id="servicio{{ $servicio->idservicio }}"

                                                @checked(
                                                    in_array(
                                                        $servicio->idservicio,
                                                        old('servicios', [])
                                                    )
                                                )
                                            >

                                            <label
                                                class="form-check-label"
                                                for="servicio{{ $servicio->idservicio }}"
                                            >

                                                {{ $servicio->nombre }}

                                            </label>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @empty

                        <div class="text-center text-muted py-4">

                            <i class="bi bi-info-circle fs-3"></i>

                            <p class="mb-0 mt-2">
                                No hay servicios activos disponibles para asignar.
                            </p>

                        </div>

                    @endforelse


                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- BOTONES --}}
            {{-- ========================================================= --}}

            <div class="d-flex justify-content-between">

                <a
                    href="{{ route('admin.profesionales.index') }}"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="btn btn-success"
                >

                    <i class="bi bi-person-plus me-2"></i>

                    Registrar profesional

                </button>

            </div>


        </form>

    </div>

@endsection
