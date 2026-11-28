@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Gestión de agenda" subtitle="Disponibilidad de los profesionales de la salud."
            eyebrow="SIGAC · GESTIÓN OPERATIVA">

            <x-slot:actions>

                <a href="{{ route('dashboard') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver

                </a>

                <a href="{{ route('auxiliar.agenda.create') }}" class="btn sigac-btn-light">

                    <i class="bi bi-plus-lg"></i>
                    Nueva disponibilidad

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

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

        <x-sigac.card title="Disponibilidades registradas"
            subtitle="Consulta y administra los horarios de los profesionales.">

            <div class="table-responsive">

                <table class="table sigac-table align-middle">

                    <thead>
                        <tr>
                            <th scope="col">Profesional</th>
                            <th scope="col">Sede</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Hora</th>
                            <th scope="col">Consultorio</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($agendas as $agenda)
                            <tr>

                                <td>

                                    <div class="sigac-table-entity">

                                        <div class="sigac-icon sigac-icon-info">
                                            <i class="bi bi-person-badge" aria-hidden="true"></i>
                                        </div>

                                        <strong>
                                            {{ $agenda->profesional->user->name }}
                                            {{ $agenda->profesional->user->last_name }}
                                        </strong>

                                    </div>

                                </td>

                                <td>
                                    {{ $agenda->sede->nombre }}
                                </td>

                                <td class="text-nowrap">
                                    {{ $agenda->fecha->format('d/m/Y') }}
                                </td>

                                <td class="text-nowrap">
                                    {{ $agenda->horainicio }}
                                </td>

                                <td>
                                    {{ $agenda->consultorio }}
                                </td>

                                <td>

                                    <div class="sigac-table-actions">

                                        <a href="{{ route('auxiliar.agenda.edit', $agenda) }}"
                                            class="btn btn-sm btn-outline-warning" title="Editar disponibilidad"
                                            aria-label="Editar disponibilidad">

                                            <i class="bi bi-pencil"></i>

                                        </a>

                                        <form action="{{ route('auxiliar.agenda.destroy', $agenda) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Está seguro de eliminar esta disponibilidad?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Eliminar disponibilidad" aria-label="Eliminar disponibilidad">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6">

                                    <x-sigac.empty-state title="No hay disponibilidades registradas"
                                        description="Los horarios registrados aparecerán en este listado."
                                        icon="calendar-x">

                                        <a href="{{ route('auxiliar.agenda.create') }}" class="btn sigac-btn-primary">

                                            <i class="bi bi-calendar-plus"></i>
                                            Registrar disponibilidad

                                        </a>

                                    </x-sigac.empty-state>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </x-sigac.card>

    </div>

@endsection
