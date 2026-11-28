@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header
            title="Gestión de servicios"
            subtitle="Administra los servicios ofrecidos por la institución."
            eyebrow="SIGAC · ADMINISTRACIÓN">

            <x-slot:actions>

                <a href="{{ route('admin.servicios.create') }}"
                    class="btn sigac-btn-light">

                    <i class="bi bi-plus-lg"></i>
                    Nuevo servicio

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

            <x-sigac.alert
                variant="danger"
                title="No fue posible completar la operación">

                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

            </x-sigac.alert>

        @endif

        {{-- Listado --}}

        <x-sigac.card
            title="Servicios registrados"
            subtitle="Consulta los servicios, sus precios y condiciones de prestación.">

            <div class="table-responsive">

                <table class="table sigac-table align-middle">

                    <thead>

                        <tr>
                            <th scope="col">Servicio</th>
                            <th scope="col">Tipo de servicio</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Prerrequisitos</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($servicios as $servicio)

                            @php
                                $activo = $servicio->estadoServicio === 'Activo';

                                $varianteEstado = $activo
                                    ? 'success'
                                    : 'neutral';

                                $etiquetaEstado = $activo
                                    ? 'Activo'
                                    : 'Inactivo';
                            @endphp

                            <tr>

                                <td>

                                    <div class="sigac-table-entity">

                                        <div class="sigac-icon sigac-icon-info">

                                            <i class="bi bi-clipboard2-pulse"
                                                aria-hidden="true"></i>

                                        </div>

                                        <strong>
                                            {{ $servicio->nombre }}
                                        </strong>

                                    </div>

                                </td>

                                <td>
                                    {{ $servicio->tipoServicio->nombre ?? 'Sin tipo' }}
                                </td>

                                <td class="fw-semibold text-nowrap">

                                    ${{ number_format($servicio->precio, 0, ',', '.') }}

                                </td>

                                <td>

                                    {{ $servicio->prerrequisitos ?: 'Sin prerrequisitos' }}

                                </td>

                                <td>

                                    <x-sigac.status
                                        :label="$etiquetaEstado"
                                        :variant="$varianteEstado"
                                    />

                                </td>

                                <td>

                                    <div class="sigac-table-actions">

                                        {{-- Editar --}}

                                        <a href="{{ route('admin.servicios.edit', ['servicio' => $servicio->idservicio]) }}"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Editar servicio"
                                            aria-label="Editar servicio">

                                            <i class="bi bi-pencil"></i>

                                        </a>

                                        {{-- Activar / Desactivar --}}

                                        <form method="POST"
                                            action="{{ route('admin.servicios.toggleEstado', ['servicio' => $servicio->idservicio]) }}"
                                            class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            @if ($activo)

                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Desactivar servicio"
                                                    aria-label="Desactivar servicio"
                                                    onclick="return confirm('¿Está seguro de que desea desactivar este servicio?')">

                                                    <i class="bi bi-toggle-off"></i>

                                                </button>

                                            @else

                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-success"
                                                    title="Activar servicio"
                                                    aria-label="Activar servicio"
                                                    onclick="return confirm('¿Desea activar nuevamente este servicio?')">

                                                    <i class="bi bi-toggle-on"></i>

                                                </button>

                                            @endif

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6">

                                    <x-sigac.empty-state
                                        title="No hay servicios registrados"
                                        description="Los servicios ofrecidos por la institución aparecerán en este listado."
                                        icon="clipboard2-pulse">

                                        <a href="{{ route('admin.servicios.create') }}"
                                            class="btn sigac-btn-primary">

                                            <i class="bi bi-plus-lg"></i>
                                            Registrar servicio

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
