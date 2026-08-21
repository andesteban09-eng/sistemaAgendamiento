<nav class="navbar bg-white shadow-sm">
    <div class="container-fluid px-4">

        {{-- Fecha --}}
        <div class="d-flex align-items-center">
            <span class="text-secondary">
                <i class="bi bi-calendar3 me-2"></i>

                {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
            </span>
        </div>

        {{-- Logo --}}
        <div class="position-absolute start-50 translate-middle-x">
            <img
                src="{{ asset('img/LOGO-CARVAJAL-LABORATORIOS-IPS-3-1.webp') }}"
                alt="Laboratorios Carvajal IPS"
                style="height: 60px;"
            >
        </div>

        {{-- Usuario --}}
        <div class="dropdown ms-auto">

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
</nav>
