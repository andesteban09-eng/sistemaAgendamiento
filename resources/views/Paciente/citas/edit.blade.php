@extends('layouts.dashboard')

@section('contenido')

    ```
    <div class="container py-4">

        <div class="mb-4">

            <h1 class="fw-bold mb-1">
                Editar cita
            </h1>

            <p class="text-muted mb-0">
                Modifica el horario o el detalle de tu cita.
            </p>

        </div>


        @if (session('error'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
            </div>
        @endif


        @if ($errors->any())
            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>
        @endif


        <form action="{{ route('Paciente.citas.update', $cita) }}" method="POST">

            @csrf
            @method('PUT')


            {{-- SERVICIO --}}

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-4">

                    <p class="text-muted mb-1">
                        Servicio
                    </p>

                    <h4 class="fw-bold mb-0">
                        {{ $cita->servicio?->nombre ?? 'Sin información' }}
                    </h4>

                </div>

            </div>


            {{-- HORARIOS --}}

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-4">

                    <h4 class="fw-bold mb-4">
                        Selecciona un nuevo horario
                    </h4>


                    @if ($horarios->isEmpty())
                        <div class="alert alert-info">
                            No hay horarios disponibles para este servicio.
                        </div>
                    @else
                        @php
                            $grupos = $horarios->groupBy(fn($horario) => $horario->fecha->format('Y-m-d'));
                        @endphp


                        @foreach ($grupos as $fecha => $horariosFecha)
                            <div class="mb-4">

                                <h5 class="fw-bold mb-3">

                                    <i class="bi bi-calendar-event me-2"></i>

                                    {{ \Carbon\Carbon::parse($fecha)->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}

                                </h5>


                                <div class="d-flex flex-wrap gap-2">

                                    @foreach ($horariosFecha as $horario)
                                        @php
                                            $seleccionado = $horario->idhorariodispo == $cita->idhorariodispo;
                                        @endphp


                                        <label>

                                            <input type="radio" name="idhorariodispo"
                                                value="{{ $horario->idhorariodispo }}" class="d-none"
                                                @checked($seleccionado)>

                                            <span
                                                class="btn
                                                {{ $seleccionado ? 'btn-primary' : 'btn-outline-primary' }}">

                                                <i class="bi bi-clock me-1"></i>

                                                {{ $horario->horainicio }}

                                                -

                                                {{ $horario->sede?->nombre }}

                                                -

                                                {{ $horario->consultorio }}

                                            </span>

                                        </label>
                                    @endforeach

                                </div>

                            </div>
                        @endforeach
                    @endif

                </div>

            </div>


            {{-- DETALLE --}}

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-4">

                    <label for="detalle" class="form-label fw-bold">
                        Detalle
                    </label>

                    <textarea id="detalle" name="detalle" class="form-control" rows="3" maxlength="100">{{ old('detalle', $cita->detalle) }}</textarea>

                </div>

            </div>


            {{-- ACCIONES --}}

            <div class="d-flex justify-content-end gap-2">

                <a href="{{ route('Paciente.citas.show', $cita) }}" class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

@endsection
