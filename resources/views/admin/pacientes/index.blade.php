@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Gestión de pacientes"
            subtitle="Consulta y administra los pacientes registrados en el sistema." eyebrow="SIGAC · ADMINISTRACIÓN">

            <x-slot:actions>

                <a href="{{ route('admin.pacientes.create') }}" class="btn sigac-btn-light">

                    <i class="bi bi-person-plus"></i>
                    Nuevo paciente

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        {{-- Mensajes --}}

        @if (session('success'))
            <x-sigac.alert variant="success" class="alert alert-dismissible fade show pe-5">

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar">
                </button>

            </x-sigac.alert>
        @endif

        @if ($errors->any())
            <x-sigac.alert variant="danger" title="No fue posible completar la operación">

                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

            </x-sigac.alert>
        @endif

        {{-- Listado --}}

        <x-sigac.card title="Pacientes registrados" subtitle="Información personal y estado de los pacientes.">

            <div class="table-responsive">

                <table class="table sigac-table align-middle">

                    <thead>

                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Paciente</th>
                            <th scope="col">Documento</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Ciudad</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end">
                                Acciones
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($pacientes as $paciente)
                            @php
                                $activo = $paciente->estadopaciente === 'Activo';

                                $varianteEstado = $activo ? 'success' : 'neutral';
                            @endphp

                            <tr>

                                <td>{{ $paciente->idpaciente }}</td>

                                <td>

                                    <div class="sigac-table-entity">

                                        <div class="sigac-icon sigac-icon-info">
                                            <i class="bi bi-person" aria-hidden="true"></i>
                                        </div>

                                        <div>

                                            <strong>
                                                {{ $paciente->user?->name }}
                                                {{ $paciente->user?->last_name }}
                                            </strong>

                                            <div class="text-muted">
                                                {{ $paciente->user?->email }}
                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <div class="text-muted">
                                        {{ $paciente->tipodoc }}
                                    </div>

                                    <strong>
                                        {{ $paciente->numdoc }}
                                    </strong>

                                </td>

                                <td>{{ $paciente->telefono }}</td>

                                <td>{{ $paciente->ciudad }}</td>

                                <td>

                                    <x-sigac.status :label="$paciente->estadopaciente" :variant="$varianteEstado" />

                                </td>

                                <td>

                                    <div class="sigac-table-actions">

                                        <a href="{{ route('admin.pacientes.show', $paciente) }}"
                                            class="btn btn-sm btn-outline-primary" title="Consultar paciente"
                                            aria-label="Consultar paciente">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        <a href="{{ route('admin.pacientes.edit', $paciente) }}"
                                            class="btn btn-sm btn-outline-warning" title="Editar paciente"
                                            aria-label="Editar paciente">

                                            <i class="bi bi-pencil"></i>

                                        </a>

                                        <form method="POST"
                                            action="{{ route('admin.pacientes.toggleEstado', $paciente) }}"
                                            class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            @if ($activo)
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Desactivar paciente" aria-label="Desactivar paciente"
                                                    onclick="return confirm('¿Está seguro de que desea desactivar este paciente?')">

                                                    <i class="bi bi-person-x"></i>

                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    title="Activar paciente" aria-label="Activar paciente"
                                                    onclick="return confirm('¿Desea activar nuevamente este paciente?')">

                                                    <i class="bi bi-person-check"></i>

                                                </button>
                                            @endif

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7">

                                    <x-sigac.empty-state title="No hay pacientes registrados"
                                        description="Los pacientes registrados aparecerán en este listado." icon="people">

                                        <a href="{{ route('admin.pacientes.create') }}" class="btn sigac-btn-primary">

                                            <i class="bi bi-person-plus"></i>
                                            Registrar paciente

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
