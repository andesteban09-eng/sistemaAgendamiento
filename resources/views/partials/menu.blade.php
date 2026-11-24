 <!-- ═══ TOPBAR ═══ -->
  <div class="topbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-1">
      <span><i class="bi bi-telephone-fill"></i> PBX: 000000000 &nbsp;|&nbsp; <i class="bi bi-phone-fill"></i> 3222142537|&nbsp; <i class="bi bi-phone-fill"></i> 3008294002</span>
      <div>
        <a href="#"><i class="bi bi-file-earmark-text"></i>Certificados</a>
        <a href="#"><i class="bi bi-clipboard2-pulse"></i>Resultados</a>
        <a href="#"><i class="bi bi-whatsapp"></i>Escríbenos</a>
      </div>
    </div>
  </div>

 <!-- ═══ NAVBAR ═══ -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('inicio') }}">
            <img
                src="{{ asset('img/logo-SIGAC.png') }}"
                alt="Logo SIGAC Sistema Integral de Gestión de Citas Medicas para IPS en Boyacá"
                class="img-fluid"
            >
        </a>

        <!-- BOTÓN HAMBURGUESA -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navMenu"
            aria-controls="navMenu"
            aria-expanded="false"
            aria-label="Abrir menú"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- MENÚ -->
        <div class="collapse navbar-collapse" id="navMenu">

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">

                <!-- INICIO -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('inicio') }}">
                        Inicio
                    </a>
                </li>

                <!-- NOSOTROS -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Nosotros
                    </a>
                </li>

                <!-- SERVICIOS -->
                <li class="nav-item dropdown">
                    <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        Servicios
                    </a>

                    <ul class="dropdown-menu">

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-flask me-2 text-muted"></i>
                                Laboratorio Clínico
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <!-- CONSULTA MÉDICA -->
                        <li>
                            <a class="dropdown-item fw-bold" href="#">
                                <i class="bi bi-person-heart me-2 text-muted"></i>
                                Consulta Médica
                            </a>
                        </li>

                        <!-- AGENDAR CITA -->
                        <li>
                            <a class="dropdown-item btn-agendar" href="#agendar-cita">
                                <i class="bi bi-calendar2-check-fill"></i>
                                Agendar Cita Médica
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-car-front me-2 text-muted"></i>
                                C.R.C. Conductores
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-shield-heart me-2 text-muted"></i>
                                Salud Ocupacional
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-house-heart me-2 text-muted"></i>
                                Cuidado en Casa
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-camera-video me-2 text-muted"></i>
                                Telemedicina
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-radioactive me-2 text-muted"></i>
                                Imagenología
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-droplet-half me-2 text-muted"></i>
                                Vacunación
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-truck me-2 text-muted"></i>
                                Ambulancia
                            </a>
                        </li>

                    </ul>
                </li>

                <!-- SEDES -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Sedes
                    </a>
                </li>

                <!-- CONTACTO -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Contacto
                    </a>
                </li>

                <!-- INICIAR SESIÓN -->
                <li class="nav-item ms-lg-2">
                    <a
                        class="nav-btn btn btn-sm px-3 py-2 text-white fw-bold rounded-3"
                        style="background: var(--aqua); font-size: .8rem;"
                        href="{{ route('login') }}"
                    >
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                        Iniciar sesión
                    </a>
                </li>

            </ul>

        </div>
    </div>
</nav>
