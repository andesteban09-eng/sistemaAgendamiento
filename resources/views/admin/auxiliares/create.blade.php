@extends('layouts.admin')

@section('contenido')

<div class="container-fluid px-4 py-4">

    <div class="mb-4">

        <h1 class="fw-bold">
            Nuevo auxiliar
        </h1>

        <p class="text-muted">
            Registra un nuevo auxiliar en el sistema.
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

    <form method="POST" action="{{ route('admin.auxiliares.store') }}">

        @csrf

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white">

                <h5 class="mb-0 fw-bold">
                    Datos del auxiliar
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
                            class="form-control"
                            value="{{ old('name') }}"
                            required
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Apellido
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            class="form-control"
                            value="{{ old('last_name') }}"
                            required
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            required
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Contraseña
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required
                        >

                        <small class="text-muted">
                            La contraseña debe tener al menos 8 caracteres.
                        </small>

                    </div>

                </div>

            </div>

        </div>


        <div class="d-flex justify-content-between">

            <a
                href="{{ route('admin.auxiliares.index') }}"
                class="btn btn-secondary"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >

                <i class="bi bi-person-plus me-2"></i>

                Registrar auxiliar

            </button>

        </div>

    </form>

</div>

@endsection
