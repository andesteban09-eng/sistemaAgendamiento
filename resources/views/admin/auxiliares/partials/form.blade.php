@php
    $esEdicion = $auxiliar !== null;
@endphp

<x-sigac.card title="Datos del auxiliar" subtitle="Información personal y credenciales de acceso al sistema.">

    <div class="sigac-form-grid">

        {{-- Nombre --}}

        <div>

            <label for="name" class="form-label">
                Nombre <span class="text-danger">*</span>
            </label>

            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $auxiliar?->name) }}" required>

            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Apellido --}}

        <div>

            <label for="last_name" class="form-label">
                Apellido <span class="text-danger">*</span>
            </label>

            <input type="text" name="last_name" id="last_name"
                class="form-control @error('last_name') is-invalid @enderror"
                value="{{ old('last_name', $auxiliar?->last_name) }}" required>

            @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Correo electrónico --}}

        <div>

            <label for="email" class="form-label">
                Correo electrónico <span class="text-danger">*</span>
            </label>

            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $auxiliar?->email) }}"
                required>

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Contraseña --}}

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

            <div class="form-text">

                @if ($esEdicion)
                    Déjalo vacío si no deseas cambiar la contraseña.
                @else
                    La contraseña debe tener al menos 8 caracteres.
                @endif

            </div>

            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

    </div>

</x-sigac.card>
