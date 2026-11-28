@php
    $esEdicion = $paciente !== null;

    $tiposDocumento = [
        'Cedula Ciudadania' => 'Cédula de Ciudadanía',
        'Tarjeta Identidad' => 'Tarjeta de Identidad',
        'Cedula Extranjeria' => 'Cédula de Extranjería',
        'Pasaporte' => 'Pasaporte',
        'Registro Civil' => 'Registro Civil',
        'Permiso Proteccion Temporal' => 'Permiso de Protección Temporal',
        'Otro' => 'Otro',
    ];
@endphp

{{-- Datos de acceso --}}

<x-sigac.card title="Datos de acceso" subtitle="Información de la cuenta de usuario.">

    <div class="sigac-form-grid">

        {{-- Nombre --}}

        <div>

            <label for="name" class="form-label">
                Nombre
                <span class="text-danger">*</span>
            </label>

            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $paciente?->user?->name) }}" required>

            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Apellido --}}

        <div>

            <label for="last_name" class="form-label">
                Apellido
                <span class="text-danger">*</span>
            </label>

            <input type="text" name="last_name" id="last_name"
                class="form-control @error('last_name') is-invalid @enderror"
                value="{{ old('last_name', $paciente?->user?->last_name) }}" required>

            @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Correo electrónico --}}

        <div>

            <label for="email" class="form-label">
                Correo electrónico
                <span class="text-danger">*</span>
            </label>

            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $paciente?->user?->email) }}" required>

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Contraseña: únicamente durante el registro --}}

        @unless ($esEdicion)

            <div>

                <label for="password" class="form-label">
                    Contraseña
                    <span class="text-danger">*</span>
                </label>

                <input type="password" name="password" id="password"
                    class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>

                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

            </div>

        @endunless

    </div>

</x-sigac.card>

{{-- Información del paciente --}}

<x-sigac.card title="Información del paciente" subtitle="Datos de identificación y contacto.">

    <div class="sigac-form-grid">

        {{-- Tipo de documento --}}

        <div>

            <label for="tipodoc" class="form-label">
                Tipo de documento
                <span class="text-danger">*</span>
            </label>

            <select name="tipodoc" id="tipodoc" class="form-select @error('tipodoc') is-invalid @enderror" required>

                <option value="">Seleccione...</option>

                @foreach ($tiposDocumento as $valor => $etiqueta)
                    <option value="{{ $valor }}" @selected(old('tipodoc', $paciente?->tipodoc) === $valor)>

                        {{ $etiqueta }}

                    </option>
                @endforeach

            </select>

            @error('tipodoc')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Número de documento --}}

        <div>

            <label for="numdoc" class="form-label">
                Número de documento
                <span class="text-danger">*</span>
            </label>

            <input type="text" name="numdoc" id="numdoc"
                class="form-control @error('numdoc') is-invalid @enderror"
                value="{{ old('numdoc', $paciente?->numdoc) }}" required>

            @error('numdoc')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Teléfono --}}

        <div>

            <label for="telefono" class="form-label">
                Teléfono
                <span class="text-danger">*</span>
            </label>

            <input type="text" name="telefono" id="telefono"
                class="form-control @error('telefono') is-invalid @enderror"
                value="{{ old('telefono', $paciente?->telefono) }}" required>

            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Ciudad --}}

        <div>

            <label for="ciudad" class="form-label">
                Ciudad
                <span class="text-danger">*</span>
            </label>

            <input type="text" name="ciudad" id="ciudad"
                class="form-control @error('ciudad') is-invalid @enderror"
                value="{{ old('ciudad', $paciente?->ciudad) }}" required>

            @error('ciudad')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Dirección --}}

        <div class="sigac-form-full">

            <label for="direccion" class="form-label">
                Dirección
                <span class="text-danger">*</span>
            </label>

            <input type="text" name="direccion" id="direccion"
                class="form-control @error('direccion') is-invalid @enderror"
                value="{{ old('direccion', $paciente?->direccion) }}" required>

            @error('direccion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

    </div>

</x-sigac.card>

{{-- Estado: únicamente durante la edición --}}

@if ($esEdicion)
    <x-sigac.card title="Estado del paciente" subtitle="Administra el estado del paciente en el sistema.">

        <div class="sigac-form-grid">

            <div>

                <label for="estadopaciente" class="form-label">
                    Estado
                    <span class="text-danger">*</span>
                </label>

                <select name="estadopaciente" id="estadopaciente"
                    class="form-select @error('estadopaciente') is-invalid @enderror" required>

                    <option value="Activo" @selected(old('estadopaciente', $paciente->estadopaciente) === 'Activo')>

                        Activo

                    </option>

                    <option value="Inactivo" @selected(old('estadopaciente', $paciente->estadopaciente) === 'Inactivo')>

                        Inactivo

                    </option>

                </select>

                @error('estadopaciente')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

            </div>

        </div>

    </x-sigac.card>
@endif
