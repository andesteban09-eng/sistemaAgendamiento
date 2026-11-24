@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Gestión de citas</h1>
                <p class="text-muted mb-0">
                    Consulta las citas registradas en SIGAC.
                </p>
            </div>

            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Volver al panel
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.citas.index') }}" class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="">Todos los estados</option>
                            @foreach (['Pendiente', 'Realizada', 'Cancelada'] as $estado)
                                <option value="{{ $estado }}" @selected(request('estado') === $estado)>
                                    {{ $estado }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" name="fecha" id="fecha" class="form-control"
                            value="{{ request('fecha') }}">
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Filtrar
                        </button>

                        <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-secondary">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Paciente</th>
                                <th>Fecha</th>
                                <th>Servicio</th>
                                <th>Profesional</th>
                                <th>Sede</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($citas as $cita)
                                <tr>
                                    <td>
                                        {{ $cita->paciente?->user?->name ?? 'Sin información' }}
                                        {{ $cita->paciente?->user?->last_name ?? '' }}
                                    </td>
                                    <td>
                                        {{ $cita->fechacita?->format('d/m/Y h:i A') }}
                                    </td>
                                    <td>
                                        {{ $cita->servicio?->nombre ?? 'Sin información' }}
                                    </td>
                                    <td>
                                        {{ $cita->agenda?->profesional?->user?->name ?? 'Sin información' }}
                                        {{ $cita->agenda?->profesional?->user?->last_name ?? '' }}
                                    </td>
                                    <td>
                                        {{ $cita->agenda?->sede?->nombre ?? 'Sin información' }}
                                    </td>
                                    <td>
                                        @php
                                            $color = match ($cita->estadocita) {
                                                'Pendiente' => 'warning text-dark',
                                                'Realizada' => 'success',
                                                'Cancelada' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp

                                        <span class="badge bg-{{ $color }}">
                                            {{ $cita->estadocita }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.citas.show', $cita) }}"
                                            class="btn btn-sm btn-outline-primary" title="Ver cita">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if ($cita->estadocita === 'Pendiente')
                                            <a href="{{ route('admin.citas.edit', $cita) }}"
                                                class="btn btn-sm btn-outline-warning" title="Editar cita">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="{{ route('admin.citas.cancelar', $cita) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Está seguro de cancelar esta cita?');">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Cancelar cita">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        No se encontraron citas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($citas->hasPages())
                <div class="card-footer bg-white">
                    {{ $citas->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
