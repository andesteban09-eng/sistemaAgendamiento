@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        ```
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="fw-bold mb-1">
                    Gestión de auxiliares
                </h1>

                <p class="text-muted mb-0">
                    Consulta y administra los auxiliares registrados en el sistema.
                </p>
            </div>

            <a href="{{ route('admin.auxiliares.create') }}" class="btn btn-info">

                <i class="bi bi-person-plus me-2"></i>

                Nuevo auxiliar

            </a>

        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>
        @endif

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>
                                <th>ID</th>
                                <th>Auxiliar</th>
                                <th>Correo electrónico</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($auxiliares as $auxiliar)
                                <tr>

                                    <td>
                                        {{ $auxiliar->id }}
                                    </td>

                                    <td>

                                        <strong>
                                            {{ $auxiliar->name }}
                                            {{ $auxiliar->last_name }}
                                        </strong>

                                    </td>

                                    <td>
                                        {{ $auxiliar->email }}
                                    </td>

                                    <td>

                                        @if ($auxiliar->estado === 'activo')
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

                                        <a href="{{ route('admin.auxiliares.show', $auxiliar) }}"
                                            class="btn btn-sm btn-outline-primary" title="Ver">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        <a href="{{ route('admin.auxiliares.edit', $auxiliar) }}"
                                            class="btn btn-sm btn-outline-warning" title="Editar">

                                            <i class="bi bi-pencil"></i>

                                        </a>

                                        <form action="{{ route('admin.auxiliares.toggleEstado', $auxiliar) }}"
                                            method="POST" class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                title="{{ $auxiliar->estado === 'activo' ? 'Inactivar' : 'Activar' }}">

                                                @if ($auxiliar->estado === 'activo')
                                                    <i class="bi bi-person-slash"></i>
                                                @else
                                                    <i class="bi bi-person-check"></i>
                                                @endif

                                            </button>

                                        </form>

                                        <form action="{{ route('admin.auxiliares.destroy', $auxiliar) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Está seguro de eliminar este auxiliar?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="text-center py-5">

                                        <i class="bi bi-person-gear fs-1 text-muted"></i>

                                        <p class="text-muted mt-3 mb-0">
                                            No hay auxiliares registrados.
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
    ```
@endsection
