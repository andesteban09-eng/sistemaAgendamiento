<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container col-3 d-flex flex-column flex-lg-row align-items-center gap-2">
        <p class="text-secondary mb-0">
            <i class="bi bi-calendar3"></i>
            {{ ucfirst(now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}
        </p>
    </div>

    <div class="container col-6 d-flex align-items-center gap-3">
        <img src="{{ asset('img/LOGO-CARVAJAL-LABORATORIOS-IPS-3-1.webp') }}"
             alt="logo"
             class="d-block"
             style="height:60px;">
    </div>

    <div class="dropdown ms-auto d-flex align-items-center gap-2">
        <span class="text-secondary fw-bold me-2">
            Bienvenido(a), {{ auth()->user()->name }}
        </span>

        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="{{ route('profile') }}" wire:navigate>
                    Mi perfil
                </a>
            </li>

            <li><hr class="dropdown-divider"></li>

            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        Cerrar sesión
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>