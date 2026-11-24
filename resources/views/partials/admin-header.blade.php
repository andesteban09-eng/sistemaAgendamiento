<nav class="navbar bg-white shadow-sm dashboard-header">
    <div class="container-fluid px-4">

        <div class="dashboard-header-content">

            {{-- Fecha --}}
            <div class="dashboard-date">
                <span class="text-secondary">
                    <i class="bi bi-calendar3 me-2"></i>
                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                </span>
            </div>

            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" class="dashboard-logo">
                <img
                    src="{{ asset('img/logo-SIGAC.png') }}"
                    alt="SIGAC para IPS en Boyacá"
                >
            </a>

            {{-- Usuario --}}
            <div class="dropdown dashboard-user">

                <button
                    class="btn btn-light dropdown-toggle d-flex align-items-center gap-2"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="bi bi-person-circle fs-5"></i>

                    <span class="fw-semibold">
                        {{ Auth::user()->name }}
                    </span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                    <li>
                        <h6 class="dropdown-header">
                            Administrador
                        </h6>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#">
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

                            <button
                                type="submit"
                                class="dropdown-item text-danger"
                            >
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
