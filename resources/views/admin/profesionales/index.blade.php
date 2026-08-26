@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="fw-bold mb-1">
                    Gestión de profesionales
                </h1>

                <p class="text-muted mb-0">
                    Consulta y administra los profesionales de salud registrados.
                </p>
            </div>

            <a href="{{ route('admin.profesionales.create') }}" class="btn btn-success">

                <i class="bi bi-person-plus me-2"></i>

                Nuevo profesional

            </a>

        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert">
                </button>

            </div>
        @endif

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>
                                <th>ID</th>
                                <th>Profesional</th>
                                <th>Documento</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($profesionales as $profesional)
                                <tr>

                                    <td>
                                        {{ $profesional->idprofesionalsalud }}
                                    </td>

                                    <td>

                                        <strong>
                                            {{ $profesional->user?->name }}
                                            {{ $profesional->user?->last_name }}
                                        </strong>

                                        <br>

                                        <small class="text-muted">
                                            {{ $profesional->user?->email }}
                                        </small>

                                    </td>

                                    <td>

                                        {{ $profesional->tipodoc }}

                                        <br>

                                        <strong>
                                            {{ $profesional->numdoc }}
                                        </strong>

                                    </td>

                                    <td>
                                        {{ $profesional->telefono }}
                                    </td>

                                    <td>

                                        @if ($profesional->estadoprofesionalsalud === 'Activo')
                                            <span class="badge text-bg-success">
                                                Activo
                                            </span>
                                        @else
                                            <span class="badge text-bg-secondary">
                                                Inactivo
                                            </span>
                                        @endif

                                    </td>

                                    <td class="text-end">

                                        {{-- Ver --}}
                                        <a href="{{ route('admin.profesionales.show', $profesional) }}"
                                            class="btn btn-sm btn-outline-primary" title="Ver profesional">

                                            <i class="bi bi-eye"></i>

                                        </a>


                                        {{-- Editar --}}
                                        <a href="{{ route('admin.profesionales.edit', $profesional) }}"
                                            class="btn btn-sm btn-outline-warning" title="Editar profesional">

                                            <i class="bi bi-pencil"></i>

                                        </a>


                                        {{-- Activar / Desactivar --}}
                                        <form action="{{ route('admin.profesionales.toggleEstado', $profesional) }}"
                                            method="POST" class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            @if ($profesional->estadoprofesionalsalud === 'Activo')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Desactivar profesional">

                                                    <i class="bi bi-person-x"></i>

                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    title="Activar profesional">

                                                    <i class="bi bi-person-check"></i>

                                                </button>
                                            @endif

                                        </form>

                                    </td>
                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center py-5">

                                        <i class="bi bi-person-badge fs-1 text-muted"></i>

                                        <p class="text-muted mt-3 mb-0">
                                            No hay profesionales registrados.
                                        </p>

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
@endsection
