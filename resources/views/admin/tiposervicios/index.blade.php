@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="fw-bold mb-1">
                    Tipos de servicio
                </h1>

                <p class="text-muted mb-0">
                    Administra las categorías de los servicios ofrecidos por el laboratorio.
                </p>
            </div>

            <a href="{{ route('admin.tiposervicios.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Nuevo tipo de servicio
            </a>

        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($tiposServicio as $tipoServicio)
                                <tr>

                                    <td class="fw-semibold">
                                        {{ $tipoServicio->nombre }}
                                    </td>

                                    <td>
                                        {{ $tipoServicio->descripcion ?: 'Sin descripción' }}
                                    </td>

                                    <td>
                                        @if ($tipoServicio->estadotiposervicio === 'Activo')
                                            <span class="badge bg-success">
                                                Activo
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-end">

                                        <a href="{{ route('admin.tiposervicios.edit', ['tipoServicio' => $tipoServicio->idtiposervicio]) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                            Editar
                                        </a>
                                        <form
                                            action="{{ route('admin.tiposervicios.toggleEstado', ['tipoServicio' => $tipoServicio->idtiposervicio]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <td>
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                @if ($tipoServicio->estadotiposervicio === 'Activo')
                                                    <i class="bi bi-toggle-off"></i>
                                                    Desactivar
                                                @else
                                                    <i class="bi bi-toggle-on"></i>
                                                    Activar
                                                @endif
                                            </button>
                                        </form>
                                    </td>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No hay tipos de servicio registrados.
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
