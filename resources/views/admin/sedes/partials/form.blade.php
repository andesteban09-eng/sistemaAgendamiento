<div class="sigac-form-grid">

    {{-- Nombre --}}
    <div class="sigac-form-full">

        <label for="nombre" class="form-label">
            Nombre de la sede
            <span class="text-danger">*</span>
        </label>

        <input type="text" name="nombre" id="nombre" maxlength="80"
            class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $sede?->nombre ?? '') }}"
            placeholder="Ej. Sede Principal" required>

        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

    </div>

    {{-- Ciudad --}}
    <div>

        <label for="ciudad" class="form-label">
            Ciudad
            <span class="text-danger">*</span>
        </label>

        <input type="text" name="ciudad" id="ciudad" maxlength="100"
            class="form-control @error('ciudad') is-invalid @enderror" value="{{ old('ciudad', $sede?->ciudad ?? '') }}"
            placeholder="Ej. Tunja" required>

        @error('ciudad')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

    </div>

    {{-- Dirección --}}
    <div>

        <label for="direccion" class="form-label">
            Dirección
            <span class="text-danger">*</span>
        </label>

        <input type="text" name="direccion" id="direccion" maxlength="60"
            class="form-control @error('direccion') is-invalid @enderror"
            value="{{ old('direccion', $sede?->direccion ?? '') }}" placeholder="Ej. Carrera 10 # 20-30" required>

        @error('direccion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

    </div>

    {{-- Detalles --}}
    <div class="sigac-form-full">

        <label for="detalles" class="form-label">
            Detalles de la sede
            <span class="text-danger">*</span>
        </label>

        <textarea name="detalles" id="detalles" rows="3" maxlength="150"
            class="form-control @error('detalles') is-invalid @enderror" placeholder="Información adicional sobre la ubicación."
            required>{{ old('detalles', $sede?->detalles ?? '') }}</textarea>

        <div class="form-text">
            Máximo 150 caracteres.
        </div>

        @error('detalles')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

    </div>

</div>
