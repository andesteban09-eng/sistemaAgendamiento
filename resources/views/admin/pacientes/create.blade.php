@extends('layouts.admin')

@section('contenido')

    <div class="container-fluid px-4 py-4">

        <div class="mb-4">
            <h1 class="fw-bold">
                Registrar paciente
            </h1>

            <p class="text-muted">
                Registra los datos personales y de acceso del nuevo paciente.
            </p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Hay errores en el formulario:</strong>

                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pacientes.store') }}">

            @csrf

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

                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Apellido
                            </label>

                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Correo electrónico
                            </label>

                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Contraseña
                            </label>

                            <input type="password" name="password" class="form-control" required>
                        </div>

                    </div>

                </div>

            </div>

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        Información del paciente
                    </h5>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                Tipo de documento
                            </label>

                            <select name="tipodoc" class="form-select" required>

                                <option value="">
                                    Seleccione...
                                </option>

                                <option value="Cedula Ciudadania">
                                    Cédula de Ciudadanía
                                </option>

                                <option value="Tarjeta Identidad">
                                    Tarjeta de Identidad
                                </option>

                                <option value="Cedula Extranjeria">
                                    Cédula de Extranjería
                                </option>

                                <option value="Pasaporte">
                                    Pasaporte
                                </option>

                                <option value="Registro Civil">
                                    Registro Civil
                                </option>

                                <option value="Permiso Proteccion Temporal">
                                    Permiso de Protección Temporal
                                </option>

                                <option value="Otro">
                                    Otro
                                </option>

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Número de documento
                            </label>

                            <input type="text" name="numdoc" class="form-control" value="{{ old('numdoc') }}" required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Teléfono
                            </label>

                            <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}"
                                required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Ciudad
                            </label>

                            <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad') }}" required>

                        </div>

                        <div class="col-12">

                            <label class="form-label">
                                Dirección
                            </label>

                            <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}"
                                required>

                        </div>

                    </div>

                </div>

            </div>

            <div class="d-flex justify-content-between">

                <a href="{{ route('admin.pacientes.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>
                    Registrar paciente
                </button>

            </div>

        </form>

    </div>

@endsection
