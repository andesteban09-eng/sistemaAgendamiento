@php
    $esEdicion = $agenda !== null;

    $horaActual = $esEdicion ? substr($agenda->horainicio, 3, 5) : null;
@endphp

<x-sigac.card title="Información de la disponibilidad"
    subtitle="Define el profesional, la ubicación y el horario de atención.">

    <div class="sigac-form-grid">

        {{-- Profesional --}}

        <div>

            <label for="IDPROFESIONALSALUD" class="form-label">
                Profesional <span class="text-danger">*</span>
            </label>

            <select name="IDPROFESIONALSALUD" id="IDPROFESIONALSALUD"
                class="form-select @error('IDPROFESIONALSALUD') is-invalid @enderror" required>

                <option value="">
                    Seleccione un profesional
                </option>

                @foreach ($profesionales as $profesional)
                    <option value="{{ $profesional->idprofesionalsalud }}" @selected(old('IDPROFESIONALSALUD', $agenda?->idprofesionalsalud) == $profesional->idprofesionalsalud)>

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

        {{-- Servicio: únicamente durante el registro --}}

        @unless ($esEdicion)

            <div>

                <label for="IDSERVICIO" class="form-label">
                    Servicio <span class="text-danger">*</span>
                </label>

                <select name="IDSERVICIO" id="IDSERVICIO" class="form-select @error('IDSERVICIO') is-invalid @enderror"
                    aria-describedby="mensaje-servicio" required disabled>

                    <option value="">
                        Seleccione primero un profesional
                    </option>

                </select>

                @error('IDSERVICIO')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

                <div id="mensaje-servicio" class="form-text" aria-live="polite">

                    Seleccione un profesional para consultar sus servicios.

                </div>

            </div>

        @endunless

        {{-- Sede --}}

        <div>

            <label for="IDSEDE" class="form-label">
                Sede <span class="text-danger">*</span>
            </label>

            <select name="IDSEDE" id="IDSEDE" class="form-select @error('IDSEDE') is-invalid @enderror" required>

                <option value="">
                    Seleccione una sede
                </option>

                @foreach ($sedes as $sede)
                    <option value="{{ $sede->idsede }}" @selected(old('IDSEDE', $agenda?->idsede) == $sede->idsede)>

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

        {{-- Fecha --}}

        <div>

            <label for="FECHA" class="form-label">
                Fecha <span class="text-danger">*</span>
            </label>

            <input type="date" name="FECHA" id="FECHA"
                value="{{ old('FECHA', $agenda?->fecha?->format('Y-m-d')) }}"
                class="form-control @error('FECHA') is-invalid @enderror" required>

            @error('FECHA')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        {{-- Hora --}}

        <div>

            <label for="HORAINICIO" class="form-label">
                Hora de inicio <span class="text-danger">*</span>
            </label>

            <input type="time" name="HORAINICIO" id="HORAINICIO" value="{{ old('HORAINICIO', $horaActual) }}"
                class="form-control @error('HORAINICIO') is-invalid @enderror" required>

            @error('HORAINICIO')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        {{-- Consultorio --}}

        <div @class(['sigac-form-full' => $esEdicion])>

            <label for="CONSULTORIO" class="form-label">
                Consultorio <span class="text-danger">*</span>
            </label>

            <input type="text" name="CONSULTORIO" id="CONSULTORIO"
                value="{{ old('CONSULTORIO', $agenda?->consultorio) }}"
                class="form-control @error('CONSULTORIO') is-invalid @enderror" maxlength="45"
                placeholder="Ej. Consultorio 101" required>

            @error('CONSULTORIO')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</x-sigac.card>
