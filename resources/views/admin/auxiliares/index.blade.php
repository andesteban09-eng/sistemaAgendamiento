@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header
            title="Gestión de auxiliares"
            subtitle="Consulta y administra los auxiliares registrados en el sistema."
            eyebrow="SIGAC · ADMINISTRACIÓN">

            <x-slot:actions>

                <a href="{{ route('admin.auxiliares.create') }}"
                    class="btn sigac-btn-light">

                    <i class="bi bi-person-plus"></i>
                    Nuevo auxiliar

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        {{-- Mensajes --}}

        @if (session('success'))

            <x-sigac.alert variant="success"
                class="alert alert-dismissible fade show pe-5">

                {{ session('success') }}

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar">
                </button>

            </x-sigac.alert>

        @endif

        @if ($errors->any())

            <x-sigac.alert variant="danger"
                title="No fue posible completar la operación">

                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

            </x-sigac.alert>

        @endif

        {{-- Listado --}}

        <x-sigac.card
            title="Auxiliares registrados"
            subtitle="Información y estado de las cuentas de auxiliares.">

            <div class="table-responsive">

                <table class="table sigac-table align-middle">

                    <thead>

                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Auxiliar</th>
                            <th scope="col">Correo electrónico</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($auxiliares as $auxiliar)

                            @php
                                $activo = $auxiliar->estado === 'activo';

                                $varianteEstado = $activo
                                    ? 'success'
                                    : 'neutral';

                                $etiquetaEstado = $activo
                                    ? 'Activo'
                                    : 'Inactivo';
                            @endphp

                            <tr>

                                <td>{{ $auxiliar->id }}</td>

                                <td>

                                    <div class="sigac-table-entity">

                                        <div class="sigac-icon sigac-icon-info">

                                            <i class="bi bi-person-gear"
                                                aria-hidden="true"></i>

                                        </div>

                                        <strong>
                                            {{ $auxiliar->name }}
                                            {{ $auxiliar->last_name }}
                                        </strong>

                                    </div>

                                </td>

                                <td>{{ $auxiliar->email }}</td>

                                <td>

                                    <x-sigac.status
                                        :label="$etiquetaEstado"
                                        :variant="$varianteEstado"
                                    />

                                </td>

                                <td>

                                    <div class="sigac-table-actions">

                                        {{-- Consultar --}}

                                        <a href="{{ route('admin.auxiliares.show', $auxiliar) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Consultar auxiliar"
                                            aria-label="Consultar auxiliar">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        {{-- Editar --}}

                                        <a href="{{ route('admin.auxiliares.edit', $auxiliar) }}"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Editar auxiliar"
                                            aria-label="Editar auxiliar">

                                            <i class="bi bi-pencil"></i>

                                        </a>

                                        {{-- Activar / Desactivar --}}

                                        <form method="POST"
                                            action="{{ route('admin.auxiliares.toggleEstado', $auxiliar) }}"
                                            class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            @if ($activo)

                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Desactivar auxiliar"
                                                    aria-label="Desactivar auxiliar"
                                                    onclick="return confirm('¿Está seguro de que desea desactivar este auxiliar?')">

                                                    <i class="bi bi-person-x"></i>

                                                </button>

                                            @else

                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-success"
                                                    title="Activar auxiliar"
                                                    aria-label="Activar auxiliar"
                                                    onclick="return confirm('¿Desea activar nuevamente este auxiliar?')">

                                                    <i class="bi bi-person-check"></i>

                                                </button>

                                            @endif

                                        </form>

                                        {{-- Eliminar --}}

                                        <form method="POST"
                                            action="{{ route('admin.auxiliares.destroy', $auxiliar) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Está seguro de eliminar este auxiliar?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Eliminar auxiliar"
                                                aria-label="Eliminar auxiliar">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5">

                                    <x-sigac.empty-state
                                        title="No hay auxiliares registrados"
                                        description="Los auxiliares registrados aparecerán en este listado."
                                        icon="person-gear">

                                        <a href="{{ route('admin.auxiliares.create') }}"
                                            class="btn sigac-btn-primary">

                                            <i class="bi bi-person-plus"></i>
                                            Registrar auxiliar

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
