<x-sigac.card title="Información del tipo de servicio" subtitle="Define el nombre y la descripción de la categoría.">

    <div class="sigac-form-grid">

        {{-- Nombre --}}

        <div class="sigac-form-full">

            <label for="nombre" class="form-label">
                Nombre <span class="text-danger">*</span>
            </label>

            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
                value="{{ old('nombre', $tipoServicio?->nombre) }}" maxlength="80" required>

            @error('nombre')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        {{-- Descripción --}}

        <div class="sigac-form-full">

            <label for="descripcion" class="form-label">
                Descripción
            </label>

            <textarea name="descripcion" id="descripcion" rows="4"
                class="form-control @error('descripcion') is-invalid @enderror" placeholder="Describe la categoría de servicios...">{{ old('descripcion', $tipoServicio?->descripcion) }}</textarea>

            @error('descripcion')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>

</x-sigac.card>
