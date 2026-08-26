@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="fw-bold mb-1">
                    Servicios
                </h1>

                <p class="text-muted mb-0">
                    Administra los servicios ofrecidos por el laboratorio.
                </p>
            </div>

            <a href="{{ route('admin.servicios.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Nuevo servicio
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
                                <th>Servicio</th>
                                <th>Tipo de servicio</th>
                                <th>Precio</th>
                                <th>Prerrequisitos</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($servicios as $servicio)
                                <tr>

                                    <td class="fw-semibold">
                                        {{ $servicio->nombre }}
                                    </td>

                                    <td>
                                        {{ $servicio->tipoServicio->nombre ?? 'Sin tipo' }}
                                    </td>

                                    <td>
                                        ${{ number_format($servicio->precio, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        {{ $servicio->prerrequisitos ?: 'Sin prerrequisitos' }}
                                    </td>

                                    <td>

                                        @if ($servicio->estadoServicio === 'Activo')
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

                                        <a href="{{ route('admin.servicios.edit', ['servicio' => $servicio->idservicio]) }}"
                                            class="btn btn-sm btn-outline-primary">

                                            <i class="bi bi-pencil"></i>
                                            Editar

                                        </a>

                                        <form
                                            action="{{ route('admin.servicios.toggleEstado', ['servicio' => $servicio->idservicio]) }}"
                                            method="POST" class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-outline-secondary">

                                                @if ($servicio->estadoServicio === 'Activo')
                                                    <i class="bi bi-toggle-off"></i>
                                                    Desactivar
                                                @else
                                                    <i class="bi bi-toggle-on"></i>
                                                    Activar
                                                @endif

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">

                                        No hay servicios registrados.

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
