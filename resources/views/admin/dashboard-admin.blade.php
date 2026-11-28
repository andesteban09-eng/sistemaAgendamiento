@extends('layouts.admin')

@section('contenido')

    @php
        $subtitulo = 'Bienvenido, ' . auth()->user()->name . '. Supervisa y administra los recursos de la IPS.';

        $periodo = 'Citas programadas durante ' . now()->locale('es')->translatedFormat('F Y') . '.';

        $indicadores = [
            ['Usuarios activos', $usuariosActivos, 'people', 'navy'],
            ['Pacientes registrados', $pacientesRegistrados, 'person', 'info'],
            ['Profesionales activos', $profesionalesActivos, 'person-badge', 'success'],
            ['Auxiliares activos', $auxiliaresActivos, 'person-gear', 'navy'],
            ['Sedes activas', $sedesActivas, 'building', 'info'],
            ['Servicios registrados', $serviciosRegistrados, 'clipboard2-pulse', 'success'],
        ];

        $colores = [
            'Pendiente' => 'warning',
            'Confirmada' => 'info',
            'Realizada' => 'success',
            'Atendida' => 'success',
            'Cancelada' => 'danger',
        ];

        $modulos = [
            ['Pacientes', 'Gestiona los pacientes registrados.', 'people', 'admin.pacientes.index'],
            ['Profesionales', 'Administra los profesionales de salud.', 'person-badge', 'admin.profesionales.index'],
            ['Auxiliares', 'Gestiona el personal auxiliar.', 'person-gear', 'admin.auxiliares.index'],
            ['Citas', 'Consulta y administra las citas.', 'calendar-check', 'admin.citas.index'],
            ['Tipos de servicio', 'Administra las categorías de servicios.', 'tags', 'admin.tiposervicios.index'],
            ['Servicios', 'Gestiona los servicios ofrecidos.', 'clipboard2-pulse', 'admin.servicios.index'],
            ['Sedes', 'Gestiona las ubicaciones de atención.', 'building', 'admin.sedes.index'],
        ];
    @endphp

    <div class="sigac-page">

        {{-- Encabezado --}}

        <x-sigac.page-header title="Panel de administración" :subtitle="$subtitulo" eyebrow="SIGAC · ADMINISTRACIÓN GENERAL" />

        {{-- Indicadores administrativos --}}

        <section class="sigac-metrics sigac-grid-three" aria-label="Indicadores administrativos">

            @foreach ($indicadores as [$label, $value, $icon, $color])
                <x-sigac.metric-card :label="$label" :value="$value" :icon="$icon" :color="$color" />
            @endforeach

        </section>

        {{-- Actividad mensual --}}

        <x-sigac.card title="Actividad mensual" :subtitle="$periodo">

            <x-slot:actions>

                <div class="text-end">

                    <span class="sigac-description">
                        Total de citas
                    </span>

                    <strong class="sigac-metric-value mt-0">
                        {{ $citasMes }}
                    </strong>

                </div>

            </x-slot:actions>

            @if ($citasMes > 0)
                {{-- Distribución de citas --}}

                <div class="progress-stacked mb-3" style="height: 12px;" role="img"
                    aria-label="Distribución mensual de citas por estado">

                    @foreach ($estadosMes as $estado => $total)
                        @php
                            $color = $colores[$estado] ?? 'neutral';
                            $porcentaje = ($total / $citasMes) * 100;
                        @endphp

                        <div class="progress" style="width: {{ $porcentaje }}%;">

                            <div class="progress-bar" style="background-color: var(--sigac-{{ $color }});">
                            </div>

                        </div>
                    @endforeach

                </div>

                {{-- Leyenda --}}

                <div class="row g-3">

                    @foreach ($estadosMes as $estado => $total)
                        @php
                            $varianteEstado = $colores[$estado] ?? 'neutral';
                        @endphp

                        <div class="col-12 col-sm-6 col-lg-3">

                            <div class="d-flex align-items-center justify-content-between gap-2 border rounded-3 p-3">

                                <x-sigac.status :label="$estado" :variant="$varianteEstado" />

                                <strong>{{ $total }}</strong>

                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                <x-sigac.empty-state title="Sin actividad mensual" description="No hay citas programadas para este mes."
                    icon="calendar-x" />
            @endif

        </x-sigac.card>

        {{-- Módulos administrativos --}}

        <x-sigac.card title="Gestión del sistema" subtitle="Accede a los diferentes módulos administrativos de SIGAC.">

            <div class="sigac-modules sigac-grid-three">

                @foreach ($modulos as [$label, $description, $icon, $route])
                    <x-sigac.module-card :title="$label" :description="$description" :href="route($route)" :icon="$icon"
                        color="info" />
                @endforeach

            </div>

        </x-sigac.card>

    </div>

@endsection
