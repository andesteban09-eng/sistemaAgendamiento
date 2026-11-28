@php
    $esEdicion = $servicio !== null;
    $campoTipo = $esEdicion ? 'IDTIPOSERVICIO' : 'idtiposervicio';
    $campoNombre = $esEdicion ? 'NOMBRE' : 'nombre';
    $campoPrecio = $esEdicion ? 'PRECIO' : 'precio';
    $campoPrerrequisitos = $esEdicion ? 'PREREQUISITOS' : 'prerequisitos';
@endphp

{{-- Información del servicio --}}
<x-sigac.card title="Información del servicio"
    subtitle="Define la clasificación, el precio y las condiciones del servicio.">

    <div class="sigac-form-grid">

        {{-- Tipo de servicio --}}

        <div class="sigac-form-full">

            <label for="{{ $campoTipo }}" class="form-label">
                Tipo de servicio <span class="text-danger">*</span>
            </label>

            <select name="{{ $campoTipo }}" id="{{ $campoTipo }}"
                class="form-select @error($campoTipo) is-invalid @enderror" required>

                @unless ($esEdicion)
                    <option value="">
                        Seleccione un tipo de servicio
                    </option>
                @endunless

                @foreach ($tiposServicio as $tipo)
                    <option value="{{ $tipo->idtiposervicio }}" @selected(old($campoTipo, $servicio?->idtiposervicio) == $tipo->idtiposervicio)>

                        {{ $tipo->nombre }}

                    </option>
                @endforeach

            </select>

            @error($campoTipo)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Nombre --}}

        <div>

            <label for="{{ $campoNombre }}" class="form-label">
                Nombre del servicio <span class="text-danger">*</span>
            </label>

            <input type="text" name="{{ $campoNombre }}" id="{{ $campoNombre }}"
                class="form-control @error($campoNombre) is-invalid @enderror"
                value="{{ old($campoNombre, $servicio?->nombre) }}" maxlength="80" required>

            @error($campoNombre)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        {{-- Precio --}}

        <div>

            <label for="{{ $campoPrecio }}" class="form-label">
                Precio <span class="text-danger">*</span>
            </label>

            <div class="input-group has-validation">

                <span class="input-group-text">$</span>

                <input type="number" name="{{ $campoPrecio }}" id="{{ $campoPrecio }}"
                    class="form-control @error($campoPrecio) is-invalid @enderror"
                    value="{{ old($campoPrecio, $servicio?->precio) }}" min="0" step="0.01" required>

                @error($campoPrecio)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

            </div>

            <div class="form-text">
                Ingresa el precio del servicio en pesos colombianos.
            </div>

        </div>

        {{-- Prerrequisitos --}}

        <div class="sigac-form-full">

            <label for="{{ $campoPrerrequisitos }}" class="form-label">
                Prerrequisitos
            </label>

            <textarea name="{{ $campoPrerrequisitos }}" id="{{ $campoPrerrequisitos }}"
                class="form-control @error($campoPrerrequisitos) is-invalid @enderror" rows="4"
                placeholder="Indique los requisitos necesarios para realizar el servicio...">{{ old($campoPrerrequisitos, $servicio?->prerequisitos) }}</textarea>

            <div class="form-text">
                Especifica las condiciones previas necesarias para prestar el servicio, si aplica.
            </div>

            @error($campoPrerrequisitos)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

    </div>
</x-sigac.card>
