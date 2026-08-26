@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="mb-4">
            <h1 class="fw-bold mb-1">
                Editar disponibilidad
            </h1>

            <p class="text-muted mb-0">
                Modifique los datos de la disponibilidad del profesional.
            </p>
        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                <form action="{{ route('auxiliar.agenda.update', $agenda) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        {{-- profesional --}}

                        <div class="col-md-6">

                            <label for="IDPROFESIONALSALUD" class="form-label fw-semibold">
                                Profesional
                            </label>

                            <select name="IDPROFESIONALSALUD" id="IDPROFESIONALSALUD"
                                class="form-select @error('IDPROFESIONALSALUD') is-invalid @enderror" required>

                                <option value="">
                                    Seleccione un profesional
                                </option>

                                @foreach ($profesionales as $profesional)
                                    <option value="{{ $profesional->idprofesionalsalud }}" @selected(old('IDPROFESIONALSALUD', $agenda->idprofesionalsalud) == $profesional->idprofesionalsalud)>
                                        {{ $profesional->user->name }}
                                        {{ $profesional->user->last_name }}
                                    </option>
                                @endforeach

                            </select>

                            @error('IDPROFESIONALSALUD')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- sede --}}

                        <div class="col-md-6">

                            <label for="IDSEDE" class="form-label fw-semibold">
                                Sede
                            </label>

                            <select name="IDSEDE" id="IDSEDE" class="form-select @error('IDSEDE') is-invalid @enderror"
                                required>

                                <option value="">
                                    Seleccione una sede
                                </option>

                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->idsede }}" @selected(old('IDSEDE', $agenda->idsede) == $sede->idsede)>
                                        {{ $sede->nombre }} — {{ $sede->ciudad }}
                                    </option>
                                @endforeach

                            </select>

                            @error('IDSEDE')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- fecha --}}

                        <div class="col-md-6">

                            <label for="FECHA" class="form-label fw-semibold">
                                Fecha
                            </label>

                            <input type="date" name="FECHA" id="FECHA"
                                value="{{ old('FECHA', $agenda->fecha?->format('Y-m-d')) }}"
                                class="form-control @error('FECHA') is-invalid @enderror" required>

                            @error('FECHA')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- hora --}}

                        <div class="col-md-6">

                            <label for="HORAINICIO" class="form-label fw-semibold">
                                Hora de inicio
                            </label>

                            <input type="time" name="HORAINICIO" id="HORAINICIO"
                                value="{{ old('HORAINICIO', substr($agenda->horainicio, 3, 5)) }}"
                                class="form-control @error('HORAINICIO') is-invalid @enderror" required>

                            @error('HORAINICIO')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- consultorio --}}

                        <div class="col-md-6">

                            <label for="CONSULTORIO" class="form-label fw-semibold">
                                Consultorio
                            </label>

                            <input type="text" name="CONSULTORIO" id="CONSULTORIO"
                                value="{{ old('CONSULTORIO', $agenda->consultorio) }}"
                                class="form-control @error('CONSULTORIO') is-invalid @enderror" maxlength="45"
                                placeholder="Ej. Consultorio 101" required>

                            @error('CONSULTORIO')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    <hr class="my-4">


                    <div class="d-flex gap-2">

                        <a href="{{ route('auxiliar.agenda.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Guardar cambios
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
