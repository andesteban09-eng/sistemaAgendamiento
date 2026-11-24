<nav class="navbar bg-white shadow-sm dashboard-role-header">
    <div class="container-fluid px-4">

        <div class="dashboard-role-header-content">

            {{-- Fecha --}}
            <div class="dashboard-role-date">
                <span class="text-secondary">
                    <i class="bi bi-calendar3 me-2"></i>
                    {{ ucfirst(now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}
                </span>
            </div>

            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" class="dashboard-role-logo">
                <img src="{{ asset('img/logo-SIGAC.png') }}"
                    alt="SIGAC: Sistema integral web de gestión, automatización y agendamiento de citas medicas para IPS">
            </a>

            {{-- Usuario --}}
            <div class="dropdown dashboard-role-user">

                <div class="dashboard-role-welcome">
                    <span class="text-secondary fw-bold">
                        Bienvenido(a), {{ auth()->user()->name }}
                    </span>
                </div>

                <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                    <li>
                        <a class="dropdown-item" href="{{ route('profile') }}" wire:navigate>
                            <i class="bi bi-person me-2"></i>
                            Mi perfil
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar sesión
                            </button>
                        </form>
                    </li>

                </ul>

            </div>

        </div>

    </div>
</nav>
