@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="fw-bold mb-1">
                    Gestión de pacientes
                </h1>

                <p class="text-muted mb-0">
                    Consulta y administra los pacientes registrados en el sistema.
                </p>
            </div>
            <a href="{{ route('admin.pacientes.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i>
                Nuevo paciente
            </a>

        </div>


        <div class="card border-0 shadow-sm">

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Documento</th>
                                <th>Teléfono</th>
                                <th>Ciudad</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($pacientes as $paciente)
                                <tr>

                                    <td>
                                        {{ $paciente->idpaciente }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $paciente->user?->name }}
                                            {{ $paciente->user?->last_name }}
                                        </strong>

                                        <br>

                                        <small class="text-muted">
                                            {{ $paciente->user?->email }}
                                        </small>
                                    </td>

                                    <td>
                                        {{ $paciente->tipodoc }}
                                        <br>
                                        <strong>
                                            {{ $paciente->numdoc }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $paciente->telefono }}
                                    </td>

                                    <td>
                                        {{ $paciente->ciudad }}
                                    </td>

                                    <td>

                                        @if ($paciente->estadopaciente === 'Activo')
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
                                        <a href="{{ route('admin.pacientes.show', $paciente) }}"
                                            class="btn btn-sm btn-outline-primary" title="Ver paciente">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.pacientes.edit', $paciente) }}"
                                            class="btn btn-sm btn-outline-warning" title="Editar paciente">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form method="POST"
                                            action="{{ route('admin.pacientes.toggleEstado', $paciente) }}"
                                            class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            @if ($paciente->estadopaciente === 'Activo')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Desactivar paciente"
                                                    onclick="return confirm('¿Está seguro de que desea desactivar este paciente?')">
                                                    <i class="bi bi-person-x"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    title="Activar paciente"
                                                    onclick="return confirm('¿Desea activar nuevamente este paciente?')">
                                                    <i class="bi bi-person-check"></i>
                                                </button>
                                            @endif

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="text-center py-5">

                                        <i class="bi bi-people fs-1 text-muted"></i>

                                        <p class="text-muted mt-3 mb-0">
                                            No hay pacientes registrados.
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
