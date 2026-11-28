@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        <x-sigac.page-header title="Gestión de sedes" subtitle="Administra las ubicaciones de atención de la IPS."
            eyebrow="SIGAC · ADMINISTRACIÓN">

            <x-slot:actions>

                <a href="{{ route('admin.sedes.create') }}" class="btn sigac-btn-light">

                    <i class="bi bi-plus-lg"></i>
                    Nueva sede

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
            <x-sigac.alert variant="danger" title="Revisa la información ingresada">

                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

            </x-sigac.alert>
        @endif

        {{-- Indicadores --}}

        <section class="sigac-metrics">

            <x-sigac.metric-card label="Sedes registradas" :value="$totalSedes" icon="building" color="navy" />

            <x-sigac.metric-card label="Sedes activas" :value="$sedesActivas" icon="check-circle" color="success" />

            <x-sigac.metric-card label="Sedes inactivas" :value="$sedesInactivas" icon="pause-circle" color="neutral" />

        </section>

        {{-- Listado --}}

        <x-sigac.card title="Sedes registradas" subtitle="Consulta las ubicaciones disponibles en el sistema.">

            {{-- Filtros --}}

            <form action="{{ route('admin.sedes.index') }}" method="GET" class="sigac-filters">

                <div>

                    <label for="buscar" class="form-label">
                        Buscar sede
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="search" id="buscar" name="buscar" class="form-control" maxlength="100"
                            value="{{ request('buscar') }}" placeholder="Nombre, ciudad o dirección">

                    </div>

                </div>

                <div>

                    <label for="estado" class="form-label">
                        Estado
                    </label>

                    <select name="estado" id="estado" class="form-select">

                        <option value="">
                            Todos los estados
                        </option>

                        <option value="Activo" @selected(request('estado') === 'Activo')>
                            Activo
                        </option>

                        <option value="Inactivo" @selected(request('estado') === 'Inactivo')>
                            Inactivo
                        </option>

                    </select>

                </div>

                <div class="sigac-filter-actions">

                    <button type="submit" class="btn sigac-btn-primary">

                        <i class="bi bi-funnel"></i>
                        Filtrar

                    </button>

                    <a href="{{ route('admin.sedes.index') }}" class="btn btn-outline-secondary">

                        Limpiar

                    </a>

                </div>

            </form>

            {{-- Tabla --}}

            <div class="table-responsive">

                <table class="table sigac-table align-middle">

                    <thead>

                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Sede</th>
                            <th scope="col">Ciudad</th>
                            <th scope="col">Dirección</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end">
                                Acciones
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($sedes as $sede)
                            <tr>

                                <td>{{ $sede->idsede }}</td>

                                <td>

                                    <div class="sigac-table-entity">

                                        <div class="sigac-icon sigac-icon-info">

                                            <i class="bi bi-building" aria-hidden="true"></i>

                                        </div>

                                        <strong>
                                            {{ $sede->nombre }}
                                        </strong>

                                    </div>

                                </td>

                                <td>{{ $sede->ciudad }}</td>

                                <td>{{ $sede->direccion }}</td>

                                <td>
                                    @php
                                        $varianteEstado = $sede->estadosede === 'Activo' ? 'success' : 'neutral';
                                    @endphp

                                    <x-sigac.status :label="$sede->estadosede" :variant="$varianteEstado" />
                                </td>
                                <td>

                                    <div class="sigac-table-actions">

                                        <a href="{{ route('admin.sedes.show', $sede) }}"
                                            class="btn btn-sm btn-outline-primary" title="Consultar sede">

                                            <i class="bi bi-eye"></i>
                                            Consultar

                                        </a>

                                        <a href="{{ route('admin.sedes.edit', $sede) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Editar sede">

                                            <i class="bi bi-pencil-square"></i>
                                            Editar

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6">

                                    <x-sigac.empty-state title="No se encontraron sedes"
                                        description="Registra una nueva sede o modifica los filtros de búsqueda."
                                        icon="building" />

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Paginación --}}

            <div class="sigac-pagination">

                <p class="sigac-results">

                    Resultados encontrados: {{ $sedes->total() }}

                </p>

                @if ($sedes->hasPages())
                    {{ $sedes->links('pagination::bootstrap-5') }}
                @endif

            </div>

        </x-sigac.card>

    </div>

@endsection
