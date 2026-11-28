@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Tipos de servicio"
            subtitle="Administra las categorías de los servicios ofrecidos por la institución."
            eyebrow="SIGAC · ADMINISTRACIÓN">

            <x-slot:actions>

                <a href="{{ route('admin.tiposervicios.create') }}" class="btn sigac-btn-light">

                    <i class="bi bi-plus-lg"></i>
                    Nuevo tipo de servicio

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        {{-- Mensajes --}}

        @if (session('success'))
            <x-sigac.alert variant="success">
                {{ session('success') }}
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

        <x-sigac.card title="Tipos de servicio registrados"
            subtitle="Consulta y administra las categorías de servicios disponibles.">

            <div class="table-responsive">

                <table class="table sigac-table align-middle">

                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($tiposServicio as $tipoServicio)
                            @php
                                $activo = $tipoServicio->estadotiposervicio === 'Activo';

                                $varianteEstado = $activo ? 'success' : 'neutral';

                                $etiquetaEstado = $activo ? 'Activo' : 'Inactivo';
                            @endphp

                            <tr>

                                <td>

                                    <div class="sigac-table-entity">

                                        <div class="sigac-icon sigac-icon-info">

                                            <i class="bi bi-grid" aria-hidden="true"></i>

                                        </div>

                                        <strong>
                                            {{ $tipoServicio->nombre }}
                                        </strong>

                                    </div>

                                </td>

                                <td>
                                    {{ $tipoServicio->descripcion ?: 'Sin descripción' }}
                                </td>

                                <td>

                                    <x-sigac.status :label="$etiquetaEstado" :variant="$varianteEstado" />

                                </td>

                                <td>

                                    <div class="sigac-table-actions">

                                        {{-- Editar --}}

                                        <a href="{{ route('admin.tiposervicios.edit', ['tipoServicio' => $tipoServicio->idtiposervicio]) }}"
                                            class="btn btn-sm btn-outline-warning" title="Editar tipo de servicio"
                                            aria-label="Editar tipo de servicio">

                                            <i class="bi bi-pencil"></i>

                                        </a>

                                        {{-- Activar / Desactivar --}}

                                        <form
                                            action="{{ route('admin.tiposervicios.toggleEstado', ['tipoServicio' => $tipoServicio->idtiposervicio]) }}"
                                            method="POST" class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            @if ($activo)
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Desactivar tipo de servicio"
                                                    aria-label="Desactivar tipo de servicio"
                                                    onclick="return confirm('¿Está seguro de que desea desactivar este tipo de servicio?')">

                                                    <i class="bi bi-toggle-off"></i>

                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    title="Activar tipo de servicio" aria-label="Activar tipo de servicio"
                                                    onclick="return confirm('¿Desea activar nuevamente este tipo de servicio?')">

                                                    <i class="bi bi-toggle-on"></i>

                                                </button>
                                            @endif

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4">

                                    <x-sigac.empty-state title="No hay tipos de servicio registrados"
                                        description="Las categorías de servicios registradas aparecerán en este listado."
                                        icon="grid">

                                        <a href="{{ route('admin.tiposervicios.create') }}" class="btn sigac-btn-primary">

                                            <i class="bi bi-plus-lg"></i>
                                            Registrar tipo de servicio

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
