@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="mb-4">

            <h1 class="fw-bold mb-1">
                Nuevo tipo de servicio
            </h1>

            <p class="text-muted mb-0">
                Registra una nueva categoría de servicios.
            </p>

        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <form action="{{ route('admin.tiposervicios.store') }}" method="POST">

                    @csrf

                    <div class="mb-3">

                        <label for="nombre" class="form-label">
                            Nombre
                        </label>

                        <input type="text" name="nombre" id="nombre"
                            class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}"
                            maxlength="80" required>

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
                            class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>

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
                            Guardar tipo de servicio
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
