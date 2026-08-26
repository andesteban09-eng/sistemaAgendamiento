@extends('layouts.admin')

@section('contenido')

    <div class="container-fluid px-4 py-4">

        {{-- ========================================================= --}}
        {{-- ENCABEZADO --}}
        {{-- ========================================================= --}}

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h1 class="fw-bold mb-1">
                    Información del profesional
                </h1>

                <p class="text-muted mb-0">
                    Consulta la información y los servicios asignados.
                </p>

            </div>


            <div class="d-flex gap-2">

                <a href="{{ route('admin.profesionales.edit', $profesional) }}" class="btn btn-warning">

                    <i class="bi bi-pencil me-1"></i>

                    Editar

                </a>


                <a href="{{ route('admin.profesionales.index') }}" class="btn btn-secondary">

                    <i class="bi bi-arrow-left me-1"></i>

                    Volver

                </a>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- DATOS PERSONALES Y DE ACCESO --}}
        {{-- ========================================================= --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white">

                <h5 class="mb-0 fw-bold">
                    Datos del profesional
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-4">


                    <div class="col-md-6">

                        <small class="text-muted d-block">
                            Nombre completo
                        </small>

                        <strong>
                            {{ $profesional->user?->name }}
                            {{ $profesional->user?->last_name }}
                        </strong>

                    </div>


                    <div class="col-md-6">

                        <small class="text-muted d-block">
                            Correo electrónico
                        </small>

                        <strong>
                            {{ $profesional->user?->email }}
                        </strong>

                    </div>


                    <div class="col-md-6">

                        <small class="text-muted d-block">
                            Tipo de documento
                        </small>

                        <strong>
                            {{ $profesional->tipodoc }}
                        </strong>

                    </div>


                    <div class="col-md-6">

                        <small class="text-muted d-block">
                            Número de documento
                        </small>

                        <strong>
                            {{ $profesional->numdoc }}
                        </strong>

                    </div>


                    <div class="col-md-6">

                        <small class="text-muted d-block">
                            Teléfono
                        </small>

                        <strong>
                            {{ $profesional->telefono }}
                        </strong>

                    </div>


                    <div class="col-md-6">

                        <small class="text-muted d-block">
                            Estado
                        </small>


                        @if ($profesional->estadoprofesionalsalud === 'Activo')
                            <span class="badge text-bg-success">
                                Activo
                            </span>
                        @else
                            <span class="badge text-bg-secondary">
                                Inactivo
                            </span>
                        @endif

                    </div>


                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- SERVICIOS ASIGNADOS --}}
        {{-- ========================================================= --}}

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="mb-1 fw-bold">
                            Servicios asignados
                        </h5>

                        <p class="text-muted mb-0 small">
                            Servicios que este profesional está autorizado para prestar.
                        </p>

                    </div>


                    <span class="badge text-bg-primary">

                        {{ $profesional->perfilesServicio->count() }}

                        {{ $profesional->perfilesServicio->count() === 1 ? 'servicio' : 'servicios' }}

                    </span>

                </div>

            </div>


            <div class="card-body">


                @forelse ($profesional->perfilesServicio
                            ->where('estadoperfil', 'Activo')
                            ->groupBy('tipoServicio.nombre')
                        as $nombreTipo => $perfiles)
                    <div class="mb-4">


                        <h6 class="fw-bold border-bottom pb-2 mb-3">

                            {{ $nombreTipo }}

                        </h6>


                        <div class="row g-3">


                            @foreach ($perfiles as $perfil)
                                <div class="col-md-6 col-lg-4">


                                    <div class="border rounded p-3 h-100">


                                        <div class="d-flex align-items-start gap-2">


                                            <i class="bi bi-check-circle-fill text-success"></i>


                                            <div>


                                                <div class="fw-semibold">

                                                    {{ $perfil->servicio?->nombre }}

                                                </div>


                                                @if ($perfil->servicio?->precio !== null)
                                                    <small class="text-muted">

                                                        ${{ number_format($perfil->servicio->precio, 0, ',', '.') }}

                                                    </small>
                                                @endif


                                            </div>


                                        </div>


                                    </div>


                                </div>
                            @endforeach


                        </div>


                    </div>


                @empty


                    <div class="text-center text-muted py-5">

                        <i class="bi bi-clipboard-x fs-1"></i>

                        <p class="mt-3 mb-0">

                            Este profesional no tiene servicios asignados.

                        </p>

                    </div>
                @endforelse


            </div>

        </div>

    </div>

@endsection
