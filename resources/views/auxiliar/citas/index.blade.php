@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Gestión de citas</h2>
                <p class="text-muted mb-0">
                    Consulta las citas y confirma la llegada de los pacientes.
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('auxiliar.citas.index') }}" method="GET" class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label for="estado" class="form-label">
                            Estado de la cita
                        </label>

                        <select name="estado" id="estado" class="form-select">
                            <option value="">Todos los estados</option>

                            @foreach (['Pendiente', 'Confirmada', 'Realizada', 'Cancelada'] as $estado)
                                <option value="{{ $estado }}" @selected(request('estado') === $estado)>
                                    {{ $estado }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="fecha" class="form-label">
                            Fecha de la cita
                        </label>

                        <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}"
                            class="form-control">
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Filtrar
                        </button>

                        <a href="{{ route('auxiliar.citas.index') }}" class="btn btn-outline-secondary">
                            Limpiar
                        </a>
                    </div>

                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Cita</th>
                                <th>Paciente</th>
                                <th>Servicio</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($citas as $cita)
                                <tr>
                                    <td>#{{ $cita->idcita }}</td>

                                    <td>
                                        {{ $cita->paciente?->user?->name ?? 'Sin información' }}
                                    </td>

                                    <td>
                                        {{ $cita->servicio?->nombre ?? 'Sin información' }}
                                    </td>

                                    <td>
                                        {{ $cita->fechacita?->format('d/m/Y h:i A') ?? 'Sin fecha' }}
                                    </td>

                                    <td>
                                        @php
                                            $color = match ($cita->estadocita) {
                                                'Pendiente' => 'warning',
                                                'Confirmada' => 'info',
                                                'Realizada' => 'success',
                                                'Cancelada' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp

                                        <span class="badge bg-{{ $color }}">
                                            {{ $cita->estadocita }}
                                        </span>
                                    </td>

                                    <td class="text-end">
                                        @if ($cita->estadocita === 'Pendiente')
                                            <form action="{{ route('auxiliar.citas.confirmar', $cita) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Verificaste el documento de identidad y la orden médica del paciente?')">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Confirmar llegada
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small">
                                                Sin acciones disponibles
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No se encontraron citas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $citas->links() }}
                </div>

            </div>
        </div>

    </div>
@endsection
