@extends('layouts.admin')

@section('contenido')

    @php
        $varianteEstado = $profesional->estadoprofesionalsalud === 'Activo' ? 'success' : 'neutral';

        $perfilesActivos = $profesional->perfilesServicio->where('estadoperfil', 'Activo');

        $cantidadServicios = $perfilesActivos->count();
    @endphp

    <div class="sigac-page">

        <x-sigac.page-header title="Información del profesional"
            subtitle="Consulta la información personal y los servicios asignados." eyebrow="SIGAC · GESTIÓN DE PROFESIONALES">

            <x-slot:actions>

                <a href="{{ route('admin.profesionales.index') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver

                </a>

                <a href="{{ route('admin.profesionales.edit', $profesional) }}" class="btn sigac-btn-light">

                    <i class="bi bi-pencil"></i>
                    Editar profesional

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        {{-- Identificación --}}

        <x-sigac.card>

            <div class="sigac-identity">

                <div class="sigac-icon sigac-icon-info">

                    <i class="bi bi-person-badge" aria-hidden="true"></i>

                </div>

                <div class="sigac-identity-content">

                    <h2>
                        {{ $profesional->user?->name }}
                        {{ $profesional->user?->last_name }}
                    </h2>

                    <p class="sigac-description mb-0">
                        Profesional #{{ $profesional->idprofesionalsalud }}
                    </p>

                </div>

                <x-sigac.status :label="$profesional->estadoprofesionalsalud" :variant="$varianteEstado" />

            </div>

            <hr>

            <dl class="sigac-details">

                <div>
                    <dt>Nombre completo</dt>

                    <dd>
                        {{ $profesional->user?->name }}
                        {{ $profesional->user?->last_name }}
                    </dd>
                </div>

                <div>
                    <dt>Correo electrónico</dt>
                    <dd>{{ $profesional->user?->email }}</dd>
                </div>

                <div>
                    <dt>Tipo de documento</dt>
                    <dd>{{ $profesional->tipodoc }}</dd>
                </div>

                <div>
                    <dt>Número de documento</dt>
                    <dd>{{ $profesional->numdoc }}</dd>
                </div>

                <div>
                    <dt>Teléfono</dt>
                    <dd>{{ $profesional->telefono }}</dd>
                </div>

                <div>
                    <dt>Estado del profesional</dt>

                    <dd>
                        <x-sigac.status :label="$profesional->estadoprofesionalsalud" :variant="$varianteEstado" />
                    </dd>
                </div>

            </dl>

        </x-sigac.card>

        {{-- Indicadores profesionales --}}

        <section class="sigac-metrics">

            <x-sigac.metric-card label="Servicios asignados activos" :value="$cantidadServicios" icon="clipboard2-check"
                color="success" />

        </section>

        {{-- Servicios asignados --}}

        <x-sigac.card title="Servicios asignados" subtitle="Servicios asociados al profesional mediante perfiles activos.">

            @forelse ($perfilesActivos->groupBy('tipoServicio.nombre') as $nombreTipo => $perfiles)
                <div class="mb-4">

                    <h3 class="fs-6 fw-semibold border-bottom pb-2 mb-3">

                        {{ $nombreTipo }}

                    </h3>

                    <div class="row g-3">

                        @foreach ($perfiles as $perfil)
                            <div class="col-12 col-md-6 col-lg-4">

                                <div class="sigac-resource h-100">

                                    <div class="sigac-icon sigac-icon-success">

                                        <i class="bi bi-check-circle" aria-hidden="true"></i>

                                    </div>

                                    <div>

                                        <div class="fw-semibold">

                                            {{ $perfil->servicio?->nombre }}

                                        </div>

                                        @if ($perfil->servicio?->precio !== null)
                                            <span>

                                                ${{ number_format($perfil->servicio->precio, 0, ',', '.') }}

                                            </span>
                                        @endif

                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>

                </div>

            @empty

                <x-sigac.empty-state title="No hay servicios asignados"
                    description="Este profesional no tiene perfiles de servicio activos." icon="clipboard-x" />
            @endforelse

        </x-sigac.card>

    </div>

@endsection
