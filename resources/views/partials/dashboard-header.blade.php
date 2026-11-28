@php
    $usuario = auth()->user();

    $rol = match (strtolower((string) $usuario->rol)) {
        'administrador' => 'Administrador',
        'auxiliar' => 'Auxiliar',
        'profesional' => 'Profesional de salud',
        'paciente' => 'Paciente',
        default => 'Usuario',
    };

    $nombreCompleto = trim($usuario->name . ' ' . ($usuario->last_name ?? ''));

    $fecha = now()->locale('es');

    $fechaCompleta = ucfirst($fecha->isoFormat('dddd, D [de] MMMM [de] YYYY'));
@endphp

<nav class="navbar sigac-dashboard-header" aria-label="Navegación de la cuenta">

    <div class="sigac-header-content">

        {{-- Fecha --}}

        <div class="sigac-header-date">

            <i class="bi bi-calendar3" aria-hidden="true"></i>

            <time datetime="{{ $fecha->format('Y-m-d') }}" title="{{ $fechaCompleta }}">

                <span class="sigac-header-date-full">
                    {{ $fechaCompleta }}
                </span>

                <span class="sigac-header-date-short">
                    {{ $fecha->format('d/m/Y') }}
                </span>

            </time>

        </div>

        {{-- Identidad institucional --}}

        <a href="{{ route('dashboard') }}" class="sigac-header-logo" aria-label="Volver al panel principal">

            <img src="{{ asset('img/logo-SIGAC.png') }}" alt="SIGAC">

        </a>

        {{-- Menú del usuario --}}

        <div class="dropdown sigac-header-user">

            <button type="button" class="btn dropdown-toggle sigac-header-user-button" data-bs-toggle="dropdown"
                aria-expanded="false" aria-label="Menú de usuario: {{ $nombreCompleto }}">

                <i class="bi bi-person-circle" aria-hidden="true"></i>

                <span class="sigac-header-user-text">

                    <span class="sigac-header-user-name">
                        {{ $usuario->name }}
                    </span>

                    <span class="sigac-header-user-role">
                        {{ $rol }}
                    </span>

                </span>

            </button>

            <ul class="dropdown-menu dropdown-menu-end sigac-header-menu">

                {{-- Identificación del usuario --}}

                <li>

                    <div class="sigac-header-menu-identity">

                        <strong>
                            {{ $nombreCompleto }}
                        </strong>

                        <span>
                            {{ $rol }}
                        </span>

                    </div>

                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                {{-- Panel principal --}}

                <li>

                    <a class="dropdown-item" href="{{ route('dashboard') }}">

                        <i class="bi bi-grid" aria-hidden="true"></i>

                        Panel principal

                    </a>

                </li>

                {{-- Perfil --}}

                <li>

                    <a class="dropdown-item" href="{{ route('profile') }}"
                        @if ($navegacionLivewire ?? false) wire:navigate @endif>

                        <i class="bi bi-person" aria-hidden="true"></i>

                        Mi perfil

                    </a>

                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                {{-- Cierre de sesión --}}

                <li>

                    <form method="POST" action="{{ route('logout') }}">

                        @csrf

                        <button type="submit" class="dropdown-item text-danger">

                            <i class="bi bi-box-arrow-right" aria-hidden="true"></i>

                            Cerrar sesión

                        </button>

                    </form>

                </li>

            </ul>

        </div>

    </div>

</nav>
