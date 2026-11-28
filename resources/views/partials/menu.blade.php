{{-- Menú público de SIGAC. Identidad independiente y diseño compacto. --}}

{{-- En autenticación se omite la barra de contactos para aprovechar el alto de pantalla. --}}
@unless (request()->routeIs('login', 'register'))
    <div class="topbar sigac-topbar">
        <div
            class="container-fluid px-3 px-lg-4 sigac-topbar-inner d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="sigac-topbar-contact d-flex align-items-center flex-wrap gap-2">
                {{-- Revisar que estos teléfonos correspondan actualmente a SIGAC antes de publicar. --}}
                <span><i class="bi bi-telephone-fill" aria-hidden="true"></i> PBX: 000000000</span>
                <span class="sigac-topbar-separator" aria-hidden="true">|</span>
                <span><i class="bi bi-phone-fill" aria-hidden="true"></i> 3222142537</span>
                <span class="sigac-topbar-separator" aria-hidden="true">|</span>
                <span><i class="bi bi-phone-fill" aria-hidden="true"></i> 3008294002</span>
            </div>

            <div class="sigac-topbar-links d-flex flex-wrap align-items-center gap-3">
                <a href="#"><i class="bi bi-file-earmark-text" aria-hidden="true"></i> Certificados</a>
                <a href="#"><i class="bi bi-clipboard2-pulse" aria-hidden="true"></i> Resultados</a>
                <a href="#"><i class="bi bi-whatsapp" aria-hidden="true"></i> Escríbenos</a>
            </div>
        </div>
    </div>
@endunless

<nav class="navbar navbar-expand-lg sticky-top sigac-navbar" aria-label="Navegación principal">
    <div class="container-fluid px-3 px-lg-4">
        {{-- Logo de SIGAC --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ route('inicio') }}" aria-label="SIGAC: ir al inicio">
            <img src="{{ asset('img/logo-SIGAC.png') }}" alt="SIGAC - Agendamiento de citas médicas">
        </a>

        {{-- Menú de navegación móvil --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
            aria-controls="navMenu" aria-expanded="false" aria-label="Abrir menú de navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('inicio') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Nosotros</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Servicios
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-flask me-2 text-muted" aria-hidden="true"></i>
                                Laboratorio Clínico
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item fw-bold" href="#">
                                <i class="bi bi-person-heart me-2 text-muted" aria-hidden="true"></i>
                                Consulta Médica
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item btn-agendar" href="{{ route('inicio') }}">
                                <i class="bi bi-calendar2-check-fill" aria-hidden="true"></i>
                                Agendar Cita Médica
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-car-front me-2 text-muted" aria-hidden="true"></i>
                                C.R.C. Conductores
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-shield-heart me-2 text-muted" aria-hidden="true"></i>
                                Salud Ocupacional
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-house-heart me-2 text-muted" aria-hidden="true"></i>
                                Cuidado en Casa
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-camera-video me-2 text-muted" aria-hidden="true"></i>
                                Telemedicina
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-radioactive me-2 text-muted" aria-hidden="true"></i>
                                Imagenología
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-droplet-half me-2 text-muted" aria-hidden="true"></i>
                                Vacunación
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-truck me-2 text-muted" aria-hidden="true"></i>
                                Ambulancia
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Sedes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contacto</a>
                </li>

                {{-- Ocultar el botón de acceso cuando ya estamos en el login. --}}
                @guest
                    @unless (request()->routeIs('login'))
                        <li class="nav-item ms-lg-2 sigac-nav-action">
                            <a class="nav-btn btn sigac-login-button" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Iniciar sesión
                            </a>
                        </li>
                    @endunless
                @endguest
            </ul>
        </div>
    </div>
</nav>
