@extends('layouts.admin')

@section('contenido')
    <div class="container-fluid px-4 py-4">

        <div class="mb-4">

            <h1 class="fw-bold mb-1">
                Nuevo servicio
            </h1>

            <p class="text-muted mb-0">
                Registra un nuevo servicio ofrecido por el laboratorio.
            </p>

        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                <form action="{{ route('admin.servicios.store') }}" method="POST">

                    @csrf

                    <div class="row g-4">

                        {{-- Tipo de servicio --}}
                        <div class="col-12">

                            <label for="idtiposervicio" class="form-label">
                                Tipo de servicio
                            </label>

                            <select name="idtiposervicio" id="idtiposervicio"
                                class="form-select @error('idtiposervicio') is-invalid @enderror" required>

                                <option value="">
                                    Seleccione un tipo de servicio
                                </option>

                                @foreach ($tiposServicio as $tipoServicio)
                                    <option value="{{ $tipoServicio->idtiposervicio }}"
                                        {{ old('idtiposervicio') == $tipoServicio->idtiposervicio ? 'selected' : '' }}>
                                        {{ $tipoServicio->nombre }}
                                    </option>
                                @endforeach

                            </select>

                            @error('idtiposervicio')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- Nombre --}}
                        <div class="col-md-6">

                            <label for="nombre" class="form-label">
                                Nombre del servicio
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

                        {{-- Precio --}}
                        <div class="col-md-6">

                            <label for="precio" class="form-label">
                                Precio
                            </label>

                            <input type="number" name="precio" id="precio"
                                class="form-control @error('precio') is-invalid @enderror" value="{{ old('precio') }}"
                                min="0" step="0.01" required>

                            @error('precio')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- Prerrequisitos --}}
                        <div class="col-12">

                            <label for="prerequisitos" class="form-label">
                                Prerrequisitos
                            </label>

                            <textarea name="prerequisitos" id="prerequisitos" class="form-control @error('prerequisitos') is-invalid @enderror"
                                rows="4" placeholder="Indique los requisitos necesarios para realizar el servicio...">{{ old('prerequisitos') }}</textarea>

                            @error('prerequisitos')
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
                            Guardar servicio
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
