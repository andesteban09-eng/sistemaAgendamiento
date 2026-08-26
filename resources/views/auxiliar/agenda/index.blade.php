@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="fw-bold mb-1">
                    Gestión de agenda
                </h1>

                <p class="text-muted mb-0">
                    Disponibilidad de los profesionales de la salud.
                </p>
            </div>

            <a href="{{ route('auxiliar.agenda.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Nueva disponibilidad
            </a>

        </div>


        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif


        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>Profesional</th>
                                <th>Sede</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Consultorio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($agendas as $agenda)
                                <tr>

                                    <td>
                                        {{ $agenda->profesional->user->name }}
                                        {{ $agenda->profesional->user->last_name }}
                                    </td>

                                    <td>
                                        {{ $agenda->sede->nombre }}
                                    </td>

                                    <td>
                                        {{ $agenda->fecha->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        {{ $agenda->horainicio }}
                                    </td>

                                    <td>
                                        {{ $agenda->consultorio }}
                                    </td>
                                    <td>
                                        <form action="{{ route('auxiliar.agenda.destroy', $agenda) }}" method="POST"
                                            onsubmit="return confirm('¿Está seguro de eliminar esta disponibilidad?');">

                                            @csrf
                                            @method('DELETE')

                                            <a href="{{ route('auxiliar.agenda.edit', $agenda) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </form>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No hay disponibilidades registradas.
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
