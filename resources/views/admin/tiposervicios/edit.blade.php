@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="mb-4">

            <h1 class="fw-bold mb-1">
                Editar tipo de servicio
            </h1>

            <p class="text-muted mb-0">
                Modifica la información del tipo de servicio.
            </p>

        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <form action="{{ route('admin.tiposervicios.update', $tipoServicio) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="mb-3">

                        <label for="nombre" class="form-label">
                            Nombre
                        </label>

                        <input type="text" name="nombre" id="nombre"
                            class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre', $tipoServicio->nombre) }}" maxlength="80" required>

                        @error('nombre')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="mb-3">

                        <label for="descripcion" class="form-label">
                            Descripción
                        </label>

                        <textarea name="descripcion" id="descripcion" rows="4"
                            class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $tipoServicio->descripcion) }}</textarea>

                        @error('descripcion')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="d-flex justify-content-end gap-2">

                        <a href="{{ route('admin.tiposervicios.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Guardar cambios
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
