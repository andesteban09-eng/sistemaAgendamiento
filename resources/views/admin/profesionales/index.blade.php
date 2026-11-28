@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Gestión de profesionales"
            subtitle="Consulta y administra los profesionales de salud registrados." eyebrow="SIGAC · ADMINISTRACIÓN">

            <x-slot:actions>

                <a href="{{ route('admin.profesionales.create') }}" class="btn sigac-btn-light">

                    <i class="bi bi-person-plus"></i>
                    Nuevo profesional

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

        {{-- Listado de profesionales --}}

        <x-sigac.card title="Profesionales registrados"
            subtitle="Información personal y estado de los profesionales de salud.">

            <div class="table-responsive">

                <table class="table sigac-table align-middle">

                    <thead>

                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Profesional</th>
                            <th scope="col">Documento</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($profesionales as $profesional)
                            @php
                                $activo = $profesional->estadoprofesionalsalud === 'Activo';

                                $varianteEstado = $activo ? 'success' : 'neutral';
                            @endphp

                            <tr>

                                <td>
                                    {{ $profesional->idprofesionalsalud }}
                                </td>

                                <td>

                                    <div class="sigac-table-entity">

                                        <div class="sigac-icon sigac-icon-info">

                                            <i class="bi bi-person-badge" aria-hidden="true"></i>

                                        </div>

                                        <div>

                                            <strong>
                                                {{ $profesional->user?->name }}
                                                {{ $profesional->user?->last_name }}
                                            </strong>

                                            <div class="text-muted">
                                                {{ $profesional->user?->email }}
                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <div class="text-muted">
                                        {{ $profesional->tipodoc }}
                                    </div>

                                    <strong>
                                        {{ $profesional->numdoc }}
                                    </strong>

                                </td>

                                <td>
                                    {{ $profesional->telefono }}
                                </td>

                                <td>

                                    <x-sigac.status :label="$profesional->estadoprofesionalsalud" :variant="$varianteEstado" />

                                </td>

                                <td class="text-end">

                                    <div class="sigac-table-actions">

                                        {{-- Consultar --}}

                                        <a href="{{ route('admin.profesionales.show', $profesional) }}"
                                            class="btn btn-sm btn-outline-primary" title="Consultar profesional"
                                            aria-label="Consultar profesional">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        {{-- Editar --}}

                                        <a href="{{ route('admin.profesionales.edit', $profesional) }}"
                                            class="btn btn-sm btn-outline-warning" title="Editar profesional"
                                            aria-label="Editar profesional">

                                            <i class="bi bi-pencil"></i>

                                        </a>

                                        {{-- Activar / Desactivar --}}

                                        <form method="POST"
                                            action="{{ route('admin.profesionales.toggleEstado', $profesional) }}"
                                            class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            @if ($activo)
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Desactivar profesional" aria-label="Desactivar profesional"
                                                    onclick="return confirm('¿Está seguro de que desea desactivar este profesional?')">

                                                    <i class="bi bi-person-x"></i>

                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    title="Activar profesional" aria-label="Activar profesional"
                                                    onclick="return confirm('¿Desea activar nuevamente este profesional?')">

                                                    <i class="bi bi-person-check"></i>

                                                </button>
                                            @endif

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6">

                                    <x-sigac.empty-state title="No hay profesionales registrados"
                                        description="Los profesionales de salud registrados aparecerán en este listado."
                                        icon="person-badge">

                                        <a href="{{ route('admin.profesionales.create') }}" class="btn sigac-btn-primary">

                                            <i class="bi bi-person-plus"></i>
                                            Registrar profesional

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
