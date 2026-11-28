@extends('layouts.admin')

@section('contenido')
    @php
        $activo = $auxiliar->estado === 'activo';

        $varianteEstado = $activo ? 'success' : 'neutral';

        $etiquetaEstado = $activo ? 'Activo' : 'Inactivo';
    @endphp

    <div class="sigac-page">

        <x-sigac.page-header title="Información del auxiliar" subtitle="Consulta la información del usuario auxiliar."
            eyebrow="SIGAC · GESTIÓN DE AUXILIARES">

            <x-slot:actions>

                <a href="{{ route('admin.auxiliares.index') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver

                </a>

                <a href="{{ route('admin.auxiliares.edit', $auxiliar) }}" class="btn sigac-btn-light">

                    <i class="bi bi-pencil"></i>
                    Editar auxiliar

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        {{-- Identificación --}}

        <x-sigac.card>

            <div class="sigac-identity">

                <div class="sigac-icon sigac-icon-info">

                    <i class="bi bi-person-gear" aria-hidden="true"></i>

                </div>

                <div class="sigac-identity-content">

                    <h2>
                        {{ $auxiliar->name }}
                        {{ $auxiliar->last_name }}
                    </h2>

                    <p class="sigac-description mb-0">
                        Auxiliar #{{ $auxiliar->id }}
                    </p>

                </div>

                <x-sigac.status :label="$etiquetaEstado" :variant="$varianteEstado" />

            </div>

            <hr>

            <dl class="sigac-details">

                <div>

                    <dt>Nombre completo</dt>

                    <dd>
                        {{ $auxiliar->name }}
                        {{ $auxiliar->last_name }}
                    </dd>

                </div>

                <div>

                    <dt>Correo electrónico</dt>

                    <dd>{{ $auxiliar->email }}</dd>

                </div>

                <div>

                    <dt>Rol</dt>

                    <dd>Auxiliar</dd>

                </div>

                <div>

                    <dt>Estado de la cuenta</dt>

                    <dd>

                        <x-sigac.status :label="$etiquetaEstado" :variant="$varianteEstado" />

                    </dd>

                </div>

            </dl>

        </x-sigac.card>

        {{-- Información del registro --}}

        <x-sigac.card title="Información del registro" subtitle="Datos de creación y actualización de la cuenta.">

            <dl class="sigac-details">

                <div>

                    <dt>ID usuario</dt>

                    <dd>{{ $auxiliar->id }}</dd>

                </div>

                <div>

                    <dt>Fecha de registro</dt>

                    <dd>
                        {{ $auxiliar->created_at?->format('d/m/Y H:i') ?? 'No registrada' }}
                    </dd>

                </div>

                <div>

                    <dt>Última actualización</dt>

                    <dd>
                        {{ $auxiliar->updated_at?->format('d/m/Y H:i') ?? 'No registrada' }}
                    </dd>

                </div>

            </dl>

        </x-sigac.card>

    </div>
@endsection
