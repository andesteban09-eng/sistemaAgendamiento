@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Gestión de citas" subtitle="Consulta y administra las citas registradas en SIGAC."
            eyebrow="SIGAC · ADMINISTRACIÓN">

            <x-slot:actions>
                <a href="{{ route('dashboard') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver al panel

                </a>
            </x-slot:actions>

        </x-sigac.page-header>

        {{-- Mensajes --}}

        @if (session('success'))
            <x-sigac.alert variant="success">
                {{ session('success') }}
            </x-sigac.alert>
        @endif

        @if (session('error'))
            <x-sigac.alert variant="danger">
                {{ session('error') }}
            </x-sigac.alert>
        @endif

        @if ($errors->any())
            <x-sigac.alert variant="danger" title="No fue posible completar la operación">

                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

            </x-sigac.alert>
        @endif

        {{-- Filtros --}}

        <x-sigac.card title="Filtros de búsqueda" subtitle="Consulta las citas según su estado y fecha.">

            <form method="GET" action="{{ route('admin.citas.index') }}" class="sigac-filters mb-0">

                <div>
                    <label for="estado" class="form-label">
                        Estado
                    </label>

                    <select name="estado" id="estado" class="form-select">

                        <option value="">Todos los estados</option>

                        @foreach (['Pendiente', 'Realizada', 'Cancelada'] as $estado)
                            <option value="{{ $estado }}" @selected(request('estado') === $estado)>

                                {{ $estado }}

                            </option>
                        @endforeach

                    </select>
                </div>

                <div>
                    <label for="fecha" class="form-label">
                        Fecha
                    </label>

                    <input type="date" name="fecha" id="fecha" class="form-control" value="{{ request('fecha') }}">
                </div>

                <div class="sigac-filter-actions">

                    <button type="submit" class="btn sigac-btn-primary">

                        <i class="bi bi-search"></i>
                        Filtrar

                    </button>

                    <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-secondary">

                        Limpiar

                    </a>

                </div>

            </form>

        </x-sigac.card>

        {{-- Listado --}}

        <x-sigac.card title="Citas registradas" subtitle="Información de los agendamientos y su estado actual.">

            <div class="table-responsive">

                <table class="table sigac-table align-middle">

                    <thead>
                        <tr>
                            <th scope="col">Paciente</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Servicio</th>
                            <th scope="col">Profesional</th>
                            <th scope="col">Sede</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($citas as $cita)
                            @php
                                $varianteEstado = match ($cita->estadocita) {
                                    'Pendiente' => 'warning',
                                    'Realizada' => 'success',
                                    'Cancelada' => 'danger',
                                    default => 'neutral',
                                };
                            @endphp

                            <tr>

                                <td>
                                    <div class="sigac-table-entity">

                                        <div class="sigac-icon sigac-icon-info">
                                            <i class="bi bi-person" aria-hidden="true"></i>
                                        </div>

                                        <strong>
                                            {{ $cita->paciente?->user?->name ?? 'Sin información' }}
                                            {{ $cita->paciente?->user?->last_name ?? '' }}
                                        </strong>

                                    </div>
                                </td>

                                <td class="text-nowrap">
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
                                    <x-sigac.status :label="$cita->estadocita" :variant="$varianteEstado" />
                                </td>

                                <td>

                                    <div class="sigac-table-actions">

                                        {{-- Consultar --}}

                                        <a href="{{ route('admin.citas.show', $cita) }}"
                                            class="btn btn-sm btn-outline-primary" title="Ver cita" aria-label="Ver cita">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        @if ($cita->estadocita === 'Pendiente')
                                            {{-- Editar --}}

                                            <a href="{{ route('admin.citas.edit', $cita) }}"
                                                class="btn btn-sm btn-outline-warning" title="Editar cita"
                                                aria-label="Editar cita">

                                                <i class="bi bi-pencil-square"></i>

                                            </a>

                                            {{-- Cancelar --}}

                                            <form action="{{ route('admin.citas.cancelar', $cita) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Está seguro de cancelar esta cita?');">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Cancelar cita" aria-label="Cancelar cita">

                                                    <i class="bi bi-x-circle"></i>

                                                </button>

                                            </form>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7">

                                    <x-sigac.empty-state title="No se encontraron citas"
                                        description="No hay citas que coincidan con los criterios de búsqueda."
                                        icon="calendar-x">

                                        <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-secondary">

                                            Limpiar filtros

                                        </a>

                                    </x-sigac.empty-state>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Paginación --}}

            @if ($citas->hasPages())
                <div class="sigac-pagination">

                    {{ $citas->links('pagination::bootstrap-5') }}

                </div>
            @endif

        </x-sigac.card>

    </div>

@endsection
