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

                        <label for="NOMBRE" class="form-label">
                            Nombre
                        </label>

                        <input type="text" name="NOMBRE" id="NOMBRE"
                            class="form-control @error('NOMBRE') is-invalid @enderror" value="{{ old('NOMBRE') }}"
                            maxlength="80" required>

                        @error('NOMBRE')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="mb-3">

                        <label for="DESCRIPCION" class="form-label">
                            Descripción
                        </label>

                        <textarea name="DESCRIPCION" id="DESCRIPCION" rows="4"
                            class="form-control @error('DESCRIPCION') is-invalid @enderror">{{ old('DESCRIPCION') }}</textarea>

                        @error('DESCRIPCION')
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
