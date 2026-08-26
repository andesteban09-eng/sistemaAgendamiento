@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="mb-4">

            <h1 class="fw-bold mb-1">
                Editar servicio
            </h1>

            <p class="text-muted mb-0">
                Actualiza la información del servicio.
            </p>

        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                <form action="{{ route('admin.servicios.update', ['servicio' => $servicio->idservicio]) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        <div class="col-md-6">

                            <label for="IDTIPOSERVICIO" class="form-label">
                                Tipo de servicio
                            </label>

                            <select name="IDTIPOSERVICIO" id="IDTIPOSERVICIO"
                                class="form-select @error('IDTIPOSERVICIO') is-invalid @enderror" required>

                                @foreach ($tiposServicio as $tipo)
                                    <option value="{{ $tipo->idtiposervicio }}"
                                        {{ old('IDTIPOSERVICIO', $servicio->idtiposervicio) == $tipo->idtiposervicio ? 'selected' : '' }}>

                                        {{ $tipo->nombre }}

                                    </option>
                                @endforeach

                            </select>

                            @error('IDTIPOSERVICIO')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label for="NOMBRE" class="form-label">
                                Nombre del servicio
                            </label>

                            <input type="text" name="NOMBRE" id="NOMBRE"
                                class="form-control @error('NOMBRE') is-invalid @enderror"
                                value="{{ old('NOMBRE', $servicio->nombre) }}" maxlength="80" required>

                            @error('NOMBRE')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label for="PRECIO" class="form-label">
                                Precio
                            </label>

                            <input type="number" name="PRECIO" id="PRECIO"
                                class="form-control @error('PRECIO') is-invalid @enderror"
                                value="{{ old('PRECIO', $servicio->precio) }}" min="0" step="0.01" required>

                            @error('PRECIO')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-12">

                            <label for="PREREQUISITOS" class="form-label">
                                Prerrequisitos
                            </label>

                            <textarea name="PREREQUISITOS" id="PREREQUISITOS" class="form-control @error('PREREQUISITOS') is-invalid @enderror"
                                rows="4">{{ old('PREREQUISITOS', $servicio->prerequisitos) }}</textarea>

                            @error('PREREQUISITOS')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    <div class="mt-4 d-flex gap-2">

                        <a href="{{ route('admin.servicios.index') }}" class="btn btn-secondary">

                            Cancelar

                        </a>

                        <button type="submit" class="btn btn-primary">

                            <i class="bi bi-save me-1"></i>
                            Actualizar servicio

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
