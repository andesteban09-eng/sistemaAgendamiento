@php
    $esEdicion = $profesional !== null;

    $tiposDocumento = [
        'Cedula Ciudadania' => 'Cédula de Ciudadanía',
        'Tarjeta Identidad' => 'Tarjeta de Identidad',
        'Cedula Extranjeria' => 'Cédula de Extranjería',
        'Pasaporte' => 'Pasaporte',
        'Registro Civil' => 'Registro Civil',
        'Permiso Proteccion Temporal' => 'Permiso de Protección Temporal',
        'Otro' => 'Otro',
    ];

    $serviciosSeleccionados = (array) old('servicios', session()->hasOldInput() ? [] : $serviciosAsignados);
@endphp

{{-- Datos de acceso --}}

<x-sigac.card title="Datos de acceso" subtitle="Información de la cuenta del profesional.">

    <div class="sigac-form-grid">

        <div>
            <label for="name" class="form-label">
                Nombre <span class="text-danger">*</span>
            </label>

            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $profesional?->user?->name) }}" required>

            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="last_name" class="form-label">
                Apellido <span class="text-danger">*</span>
            </label>

            <input type="text" name="last_name" id="last_name"
                class="form-control @error('last_name') is-invalid @enderror"
                value="{{ old('last_name', $profesional?->user?->last_name) }}" required>

            @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="email" class="form-label">
                Correo electrónico <span class="text-danger">*</span>
            </label>

            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $profesional?->user?->email) }}" required>

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password" class="form-label">

                @if ($esEdicion)
                    Nueva contraseña
                @else
                    Contraseña <span class="text-danger">*</span>
                @endif

            </label>

            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror" autocomplete="new-password"
                @required(!$esEdicion)>

            @if ($esEdicion)
                <div class="form-text">
                    Déjalo vacío si no deseas cambiar la contraseña.
                </div>
            @endif

            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

</x-sigac.card>

{{-- Información profesional --}}

<x-sigac.card title="Información profesional" subtitle="Datos de identificación y contacto.">

    <div class="sigac-form-grid">

        <div>
            <label for="tipodoc" class="form-label">
                Tipo de documento <span class="text-danger">*</span>
            </label>

            <select name="tipodoc" id="tipodoc" class="form-select @error('tipodoc') is-invalid @enderror" required>

                <option value="">Seleccione...</option>

                @foreach ($tiposDocumento as $valor => $texto)
                    <option value="{{ $valor }}" @selected(old('tipodoc', $profesional?->tipodoc) === $valor)>

                        {{ $texto }}

                    </option>
                @endforeach

            </select>

            @error('tipodoc')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="numdoc" class="form-label">
                Número de documento <span class="text-danger">*</span>
            </label>

            <input type="text" name="numdoc" id="numdoc"
                class="form-control @error('numdoc') is-invalid @enderror"
                value="{{ old('numdoc', $profesional?->numdoc) }}" required>

            @error('numdoc')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="telefono" class="form-label">
                Teléfono <span class="text-danger">*</span>
            </label>

            <input type="text" name="telefono" id="telefono"
                class="form-control @error('telefono') is-invalid @enderror"
                value="{{ old('telefono', $profesional?->telefono) }}" required>

            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if ($esEdicion)
            <div>
                <label for="estadoprofesionalsalud" class="form-label">
                    Estado
                </label>

                <select name="estadoprofesionalsalud" id="estadoprofesionalsalud"
                    class="form-select @error('estadoprofesionalsalud') is-invalid @enderror">

                    <option value="Activo" @selected(old('estadoprofesionalsalud', $profesional->estadoprofesionalsalud) === 'Activo')>
                        Activo
                    </option>

                    <option value="Inactivo" @selected(old('estadoprofesionalsalud', $profesional->estadoprofesionalsalud) === 'Inactivo')>
                        Inactivo
                    </option>

                </select>

                @error('estadoprofesionalsalud')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @endif

    </div>

</x-sigac.card>

{{-- Asignación de servicios --}}

<x-sigac.card title="Servicios que puede prestar"
    subtitle="Selecciona los servicios que este profesional está autorizado para prestar.">

    @error('servicios')
        <x-sigac.alert variant="danger">
            {{ $message }}
        </x-sigac.alert>
    @enderror

    @forelse ($servicios->groupBy('tipoServicio.nombre') as $nombreTipo => $serviciosTipo)

        <fieldset class="mb-4">

            <legend class="fs-6 fw-semibold border-bottom pb-2 mb-3">
                {{ $nombreTipo }}
            </legend>

            <div class="row g-3">

                @foreach ($serviciosTipo as $servicio)
                    <div class="col-12 col-md-6 col-lg-4">

                        <div class="border rounded-3 p-3 h-100">

                            <div class="form-check mb-0">

                                <input type="checkbox" class="form-check-input" name="servicios[]"
                                    value="{{ $servicio->idservicio }}" id="servicio{{ $servicio->idservicio }}"
                                    @checked(in_array($servicio->idservicio, $serviciosSeleccionados))>

                                <label class="form-check-label" for="servicio{{ $servicio->idservicio }}">

                                    {{ $servicio->nombre }}

                                </label>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

        </fieldset>

    @empty

        <x-sigac.empty-state title="No hay servicios disponibles"
            description="No hay servicios activos disponibles para asignar." icon="clipboard-x" />

    @endforelse

</x-sigac.card>
