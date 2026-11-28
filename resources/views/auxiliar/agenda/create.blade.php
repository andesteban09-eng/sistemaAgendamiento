@extends('layouts.admin')

@section('contenido')

    @php
        $serviciosPorProfesional = [];

        foreach ($profesionales as $profesional) {
            $serviciosPorProfesional[$profesional->idprofesionalsalud] = [];

            foreach ($profesional->perfilesServicio as $perfil) {
                if (
                    $perfil->estadoperfil === 'Activo' &&
                    $perfil->servicio &&
                    $perfil->servicio->estadoservicio === 'Activo'
                ) {
                    $serviciosPorProfesional[$profesional->idprofesionalsalud][] = [
                        'id' => $perfil->servicio->idservicio,
                        'nombre' => $perfil->servicio->nombre,
                        'tipo' => $perfil->tipoServicio?->nombre,
                    ];
                }
            }
        }
    @endphp

    <div class="sigac-page">

        <x-sigac.page-header title="Registrar disponibilidad"
            subtitle="Asigna un horario disponible a un profesional de la salud." eyebrow="SIGAC · GESTIÓN DE AGENDA">

            <x-slot:actions>

                <a href="{{ route('auxiliar.agenda.index') }}" class="btn sigac-btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Volver

                </a>

            </x-slot:actions>

        </x-sigac.page-header>

        @if (session('error'))
            <x-sigac.alert variant="danger">
                {{ session('error') }}
            </x-sigac.alert>
        @endif

        @if ($errors->any())
            <x-sigac.alert variant="danger" title="Revisa la información ingresada">

                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach

            </x-sigac.alert>
        @endif

        <form action="{{ route('auxiliar.agenda.store') }}" method="POST" class="sigac-form sigac-form-card">

            @csrf

            @include('auxiliar.agenda.partials.form', [
                'agenda' => null,
            ])

            <div class="sigac-form-actions">

                <a href="{{ route('auxiliar.agenda.index') }}" class="btn btn-outline-secondary">

                    Cancelar

                </a>

                <button type="submit" class="btn sigac-btn-primary">

                    <i class="bi bi-calendar-plus"></i>
                    Registrar disponibilidad

                </button>

            </div>

        </form>

    </div>

    <script>
        (() => {

            const serviciosPorProfesional =
                {{ \Illuminate\Support\Js::from($serviciosPorProfesional) }};

            const servicioAnterior =
                {{ \Illuminate\Support\Js::from(old('IDSERVICIO')) }};

            const profesionalSelect =
                document.getElementById('IDPROFESIONALSALUD');

            const servicioSelect =
                document.getElementById('IDSERVICIO');

            const mensajeServicio =
                document.getElementById('mensaje-servicio');

            function cargarServicios(servicioSeleccionado = null) {

                const profesionalId = profesionalSelect.value;

                servicioSelect.replaceChildren();
                servicioSelect.disabled = true;

                if (!profesionalId) {

                    servicioSelect.add(
                        new Option(
                            'Seleccione primero un profesional',
                            ''
                        )
                    );

                    mensajeServicio.textContent =
                        'Seleccione un profesional para consultar sus servicios.';

                    return;

                }

                const servicios =
                    serviciosPorProfesional[profesionalId] || [];

                servicioSelect.add(
                    new Option('Seleccione un servicio', '')
                );

                if (servicios.length === 0) {

                    mensajeServicio.textContent =
                        'Este profesional no tiene servicios activos asignados.';

                    return;

                }

                servicioSelect.disabled = false;

                servicios.forEach(servicio => {

                    const etiqueta = servicio.tipo ?
                        `${servicio.nombre} — ${servicio.tipo}` :
                        servicio.nombre;

                    servicioSelect.add(
                        new Option(etiqueta, servicio.id)
                    );

                });

                if (servicioSeleccionado !== null) {

                    const servicioValido = servicios.some(
                        servicio =>
                        String(servicio.id) === String(servicioSeleccionado)
                    );

                    if (servicioValido) {

                        servicioSelect.value =
                            String(servicioSeleccionado);

                    }

                }

                mensajeServicio.textContent =
                    `${servicios.length} servicio(s) autorizado(s) para este profesional.`;

            }

            profesionalSelect.addEventListener('change', () => {

                cargarServicios();

            });

            cargarServicios(servicioAnterior);

        })();
    </script>

@endsection
