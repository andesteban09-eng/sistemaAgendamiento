@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="fw-bold mb-1">
                    Información del auxiliar
                </h1>

                <p class="text-muted mb-0">
                    Consulta la información del usuario auxiliar.
                </p>
            </div>

            <a href="{{ route('admin.auxiliares.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Volver
            </a>

        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                <div class="row g-4">

                    <div class="col-md-6">

                        <small class="text-muted">
                            Nombre
                        </small>

                        <h5 class="fw-bold">
                            {{ $auxiliar->name }}
                            {{ $auxiliar->last_name }}
                        </h5>

                    </div>

                    <div class="col-md-6">

                        <small class="text-muted">
                            Correo electrónico
                        </small>

                        <h5>
                            {{ $auxiliar->email }}
                        </h5>

                    </div>

                    <div class="col-md-6">

                        <small class="text-muted">
                            Rol
                        </small>

                        <h5>
                            Auxiliar
                        </h5>

                    </div>

                    <div class="col-md-6">

                        <small class="text-muted">
                            Estado
                        </small>

                        <div>

                            @if ($auxiliar->estado === 'activo')
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

                    <div class="col-md-6">

                        <small class="text-muted">
                            Fecha de registro
                        </small>

                        <h6>
                            {{ $auxiliar->created_at?->format('d/m/Y H:i') }}
                        </h6>

                    </div>

                    <div class="col-md-6">

                        <small class="text-muted">
                            Última actualización
                        </small>

                        <h6>
                            {{ $auxiliar->updated_at?->format('d/m/Y H:i') }}
                        </h6>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
