@extends('layouts.admin')

@section('contenido')

    <div class="sigac-page">

        {{-- Encabezado --}}

        <x-sigac.page-header title="Gestión de citas"
            subtitle="Consulta las citas, identifica a los pacientes y confirma su llegada."
            eyebrow="SIGAC · GESTIÓN OPERATIVA">

            <x-slot:actions>

                <a href="{{ route('dashboard') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver al panel

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

        @if (session('error'))
            <x-sigac.alert variant="danger" class="alert alert-dismissible fade show pe-5">

                {{ session('error') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar">
                </button>

            </x-sigac.alert>
        @endif

        @if ($errors->any())
            <x-sigac.alert variant="danger" title="Revisa los filtros de búsqueda">

                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

            </x-sigac.alert>
        @endif

        {{-- Buscador y filtros --}}

        <x-sigac.card title="Buscar y filtrar citas"
            subtitle="Localiza las citas mediante el documento del paciente, su estado o fecha.">

            <form action="{{ route('auxiliar.citas.index') }}" method="GET" class="sigac-form">

                <div class="row g-3 align-items-end">

                    {{-- Documento --}}

                    <div class="col-12 col-md-5">

                        <label for="documento" class="form-label">
                            Documento de identidad
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-person-vcard"></i>
                            </span>

                            <input type="text" name="documento" id="documento"
                                class="form-control @error('documento') is-invalid @enderror"
                                value="{{ request('documento') }}" placeholder="Número de documento" maxlength="100"
                                autocomplete="off">

                        </div>

                    </div>

                    {{-- Estado --}}

                    <div class="col-12 col-md-3">

                        <label for="estado" class="form-label">
                            Estado de la cita
                        </label>

                        <select name="estado" id="estado" class="form-select">

                            <option value="">
                                Todos los estados
                            </option>

                            @foreach (['Pendiente', 'Confirmada', 'Realizada', 'Cancelada'] as $estado)
                                <option value="{{ $estado }}" @selected(request('estado') === $estado)>

                                    {{ $estado }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                    {{-- Fecha --}}

                    <div class="col-12 col-md-4">

                        <label for="fecha" class="form-label">
                            Fecha de la cita
                        </label>

                        <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}"
                            class="form-control">

                    </div>

                    {{-- Acciones de búsqueda --}}

                    <div class="col-12">

                        <div class="d-flex justify-content-end flex-wrap gap-2">

                            <a href="{{ route('auxiliar.citas.index') }}" class="btn btn-outline-secondary">

                                <i class="bi bi-x-lg me-1"></i>
                                Limpiar

                            </a>

                            <button type="submit" class="btn sigac-btn-primary">

                                <i class="bi bi-search me-1"></i>
                                Buscar citas

                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </x-sigac.card>

        {{-- Listado de citas --}}

        <x-sigac.card title="Citas registradas" subtitle="Consulta la información del paciente y gestiona su llegada.">

            <div class="table-responsive">

                <table class="table sigac-table align-middle">

                    <thead>

                        <tr>
                            <th scope="col">Cita</th>
                            <th scope="col">Paciente</th>
                            <th scope="col">Servicio</th>
                            <th scope="col">Fecha y hora</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($citas as $cita)
                            @php
                                $paciente = $cita->paciente;
                                $usuario = $paciente?->user;

                                $nombreCompleto = trim(($usuario?->name ?? '') . ' ' . ($usuario?->last_name ?? ''));

                                $varianteEstado = match ($cita->estadocita) {
                                    'Pendiente' => 'warning',
                                    'Confirmada' => 'info',
                                    'Realizada' => 'success',
                                    'Cancelada' => 'danger',
                                    default => 'neutral',
                                };

                                $variantePaciente = $paciente?->estadopaciente === 'Activo' ? 'success' : 'neutral';
                            @endphp

                            <tr>

                                {{-- Identificador de la cita --}}

                                <td class="text-nowrap">

                                    <strong>
                                        #{{ $cita->idcita }}
                                    </strong>

                                </td>

                                {{-- Información del paciente --}}

                                <td>

                                    <div class="sigac-table-entity">

                                        <div class="sigac-icon sigac-icon-info">

                                            <i class="bi bi-person" aria-hidden="true"></i>

                                        </div>

                                        <div>

                                            <strong>
                                                {{ $nombreCompleto ?: 'Sin información' }}
                                            </strong>

                                            <div class="sigac-description">

                                                {{ $paciente?->tipodoc ?? '' }}
                                                {{ $paciente?->numdoc ?? 'Sin documento' }}

                                            </div>

                                        </div>

                                    </div>

                                    @if ($paciente)
                                        <details class="mt-2">

                                            <summary class="text-primary small fw-semibold">
                                                Información del paciente
                                            </summary>

                                            <div class="border rounded-3 p-3 mt-2">

                                                <dl class="small mb-0">

                                                    <dt class="text-muted">
                                                        Identificador
                                                    </dt>

                                                    <dd>
                                                        #{{ $paciente->idpaciente }}
                                                    </dd>

                                                    <dt class="text-muted">
                                                        Nombre completo
                                                    </dt>

                                                    <dd>
                                                        {{ $nombreCompleto ?: 'Sin información' }}
                                                    </dd>

                                                    <dt class="text-muted">
                                                        Tipo de documento
                                                    </dt>

                                                    <dd>
                                                        {{ $paciente->tipodoc ?? 'Sin información' }}
                                                    </dd>

                                                    <dt class="text-muted">
                                                        Número de documento
                                                    </dt>

                                                    <dd>
                                                        {{ $paciente->numdoc ?? 'Sin información' }}
                                                    </dd>

                                                    <dt class="text-muted">
                                                        Correo electrónico
                                                    </dt>

                                                    <dd class="text-break">
                                                        {{ $usuario?->email ?? 'Sin información' }}
                                                    </dd>

                                                    <dt class="text-muted">
                                                        Teléfono
                                                    </dt>

                                                    <dd>
                                                        {{ $paciente->telefono ?? 'Sin información' }}
                                                    </dd>

                                                    <dt class="text-muted">
                                                        Ciudad
                                                    </dt>

                                                    <dd>
                                                        {{ $paciente->ciudad ?? 'Sin información' }}
                                                    </dd>

                                                    <dt class="text-muted">
                                                        Dirección
                                                    </dt>

                                                    <dd class="text-break">
                                                        {{ $paciente->direccion ?? 'Sin información' }}
                                                    </dd>

                                                    <dt class="text-muted">
                                                        Fecha de registro
                                                    </dt>

                                                    <dd>
                                                        {{ $paciente->fecharegistro ?? 'Sin información' }}
                                                    </dd>

                                                    <dt class="text-muted">
                                                        Estado del paciente
                                                    </dt>

                                                    <dd class="mb-0">

                                                        <x-sigac.status :label="$paciente->estadopaciente" :variant="$variantePaciente" />

                                                    </dd>

                                                </dl>

                                            </div>

                                        </details>
                                    @endif

                                </td>

                                {{-- Servicio --}}

                                <td>

                                    <strong>
                                        {{ $cita->servicio?->nombre ?? 'Sin información' }}
                                    </strong>

                                    <div class="sigac-description">
                                        {{ $cita->tipoServicio?->nombre ?? '' }}
                                    </div>

                                </td>

                                {{-- Fecha --}}

                                <td class="text-nowrap">

                                    {{ $cita->fechacita?->format('d/m/Y h:i A') ?? 'Sin fecha' }}

                                </td>

                                {{-- Estado --}}

                                <td>

                                    <x-sigac.status :label="$cita->estadocita" :variant="$varianteEstado" />

                                </td>

                                {{-- Acciones --}}

                                <td>

                                    <div class="sigac-table-actions">

                                        @if ($cita->estadocita === 'Pendiente')
                                            <form action="{{ route('auxiliar.citas.confirmar', $cita) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Verificaste el documento de identidad y la orden médica del paciente?');">

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

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6">

                                    <x-sigac.empty-state title="No se encontraron citas"
                                        description="No existen citas que coincidan con los criterios de búsqueda."
                                        icon="calendar-x">

                                        <a href="{{ route('auxiliar.citas.index') }}" class="btn btn-outline-secondary">

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

            <div class="sigac-pagination">

                <p class="sigac-results">

                    @if ($citas->total() > 0)
                        Mostrando {{ $citas->firstItem() }}
                        a {{ $citas->lastItem() }}
                        de {{ $citas->total() }} citas.
                    @else
                        0 citas encontradas.
                    @endif

                </p>

                @if ($citas->hasPages())
                    {{ $citas->links('pagination::bootstrap-5') }}
                @endif

            </div>

        </x-sigac.card>

    </div>

@endsection
