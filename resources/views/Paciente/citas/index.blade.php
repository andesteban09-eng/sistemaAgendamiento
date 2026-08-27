@extends('layouts.dashboard')

@section('contenido')

    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="fw-bold mb-1">
                    Mis citas
                </h1>

                <p class="text-muted mb-0">
                    Consulta tus citas programadas.
                </p>
            </div>

            <a href="{{ route('Paciente.citas.create') }}" class="btn btn-primary">
                <i class="bi bi-calendar-plus me-1"></i>
                Agendar cita
            </a>

        </div>


        @if ($citas->isEmpty())
            <div class="card border-0 shadow-sm">

                <div class="card-body text-center py-5">

                    <i class="bi bi-calendar-x display-4 text-muted"></i>

                    <h4 class="fw-bold mt-3">
                        No tienes citas
                    </h4>

                    <p class="text-muted">
                        Actualmente no tienes citas registradas.
                    </p>

                    <a href="{{ route('Paciente.citas.create') }}" class="btn btn-primary">
                        <i class="bi bi-calendar-plus me-1"></i>
                        Agendar una cita
                    </a>

                </div>

            </div>
        @else
            <div class="card border-0 shadow-sm">

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>
                                    <th>Fecha</th>
                                    <th>Servicio</th>
                                    <th>Profesional</th>
                                    <th>Sede</th>
                                    <th>Consultorio</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($citas as $cita)
                                    <tr>

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
                                            {{ $cita->agenda?->consultorio ?? 'Sin información' }}
                                        </td>

                                        <td>

                                            @switch($cita->estadocita)
                                                @case('Pendiente')
                                                    <span class="badge bg-warning text-dark">
                                                        Pendiente
                                                    </span>
                                                @break

                                                @case('Atendida')
                                                @case('Realizada')
                                                    <span class="badge bg-success">
                                                        {{ $cita->estadocita }}
                                                    </span>
                                                @break

                                                @case('Cancelada')
                                                    <span class="badge bg-danger">
                                                        Cancelada
                                                    </span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary">
                                                        {{ $cita->estadocita }}
                                                    </span>
                                            @endswitch

                                        </td>
                                        <td>

                                            <a href="{{ route('Paciente.citas.show', $cita) }}"
                                                class="btn btn-sm btn-outline-primary" title="Ver cita">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if ($cita->estadocita === 'Pendiente')
                                                <a href="{{ route('Paciente.citas.edit', $cita) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Editar cita">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <form action="{{ route('Paciente.citas.cancelar', $cita) }}" method="POST"
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
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>
        @endif

    </div>

@endsection
