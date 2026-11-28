@extends('layouts.admin')

@section('contenido')
    <div class="sigac-page">

        <x-sigac.page-header title="Detalle de sede" subtitle="Consulta la información de la ubicación registrada."
            eyebrow="SIGAC · GESTIÓN DE SEDES">

            <x-slot:actions>

                <a href="{{ route('admin.sedes.index') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        {{-- Identificación --}}

        <x-sigac.card>

            <div class="sigac-identity">

                <div class="sigac-icon sigac-icon-info">

                    <i class="bi bi-building" aria-hidden="true"></i>

                </div>

                <div class="sigac-identity-content">

                    <h2>{{ $sede->nombre }}</h2>

                    <p class="sigac-description mb-0">
                        Sede #{{ $sede->idsede }}
                    </p>

                </div>

                @php
                    $varianteEstado = $sede->estadosede === 'Activo' ? 'success' : 'neutral';
                @endphp

                <x-sigac.status :label="$sede->estadosede" :variant="$varianteEstado" />

            </div>

            <hr>

            <dl class="sigac-details">

                <div>
                    <dt>Identificador</dt>
                    <dd>{{ $sede->idsede }}</dd>
                </div>

                <div>
                    <dt>Nombre</dt>
                    <dd>{{ $sede->nombre }}</dd>
                </div>

                <div>
                    <dt>Ciudad</dt>
                    <dd>{{ $sede->ciudad }}</dd>
                </div>

                <div>
                    <dt>Dirección</dt>
                    <dd>{{ $sede->direccion }}</dd>
                </div>

                <div class="sigac-details-full">
                    <dt>Detalles</dt>
                    <dd>{{ $sede->detalles }}</dd>
                </div>

            </dl>

        </x-sigac.card>

        {{-- Información operativa --}}

        <x-sigac.card title="Información operativa" subtitle="Recursos asociados a esta sede.">

            <div class="sigac-resource">

                <div class="sigac-icon sigac-icon-info">

                    <i class="bi bi-calendar-week" aria-hidden="true"></i>

                </div>

                <div>

                    <span>Horarios registrados</span>

                    <strong>
                        {{ $sede->agendas_count }}
                    </strong>

                </div>

            </div>

            <x-sigac.alert variant="info" class="mt-3 mb-0">

                Los horarios asociados incluyen registros
                históricos y futuros de esta sede.

            </x-sigac.alert>

        </x-sigac.card>

    </div>
@endsection
