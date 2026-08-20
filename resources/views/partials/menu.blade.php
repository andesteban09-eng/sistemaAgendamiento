 <!-- ═══ TOPBAR ═══ -->
  <div class="topbar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-1">
      <span><i class="bi bi-telephone-fill"></i> PBX: 6087493280 &nbsp;|&nbsp; <i class="bi bi-phone-fill"></i> 3009122674</span>
      <div>
        <a href="#"><i class="bi bi-file-earmark-text"></i>Certificados</a>
        <a href="#"><i class="bi bi-clipboard2-pulse"></i>Resultados</a>
        <a href="#"><i class="bi bi-whatsapp"></i>Escríbenos</a>
      </div>
    </div>
  </div>

  <!-- ═══ NAVBAR ═══ -->
  <nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid row">
      <img src="{{ asset('img/LOGO-CARVAJAL-LABORATORIOS-IPS-3-1.webp') }}" alt="logo-carvajal-laboratorios-ips" class="col-lg-4 col-md-5 col-sm-6 col-xs-7 d-flex align-items-center justify-content-center">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
      </a>
    </div>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
          <li class="nav-item"><a class="nav-link" href={{ route('inicio') }}>Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Nosotros</a></li>

          <!-- SERVICIOS con submenu -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Servicios</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#"><i class="bi bi-flask me-2 text-muted"></i>Laboratorio Clínico</a></li>

              <!-- Consulta Médica con botón agendar cita -->
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item fw-bold" href="#">
                  <i class="bi bi-person-heart me-2 text-muted"></i>Consulta Médica
                </a>
              </li>
              <li>
                <a class="dropdown-item btn-agendar" href="#agendar-cita">
                  <i class="bi bi-calendar2-check-fill"></i> Agendar Cita Médica
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>

              <li><a class="dropdown-item" href="#"><i class="bi bi-car-front me-2 text-muted"></i>C.R.C. Conductores</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-shield-heart me-2 text-muted"></i>Salud Ocupacional</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-house-heart me-2 text-muted"></i>Cuidado en Casa</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-camera-video me-2 text-muted"></i>Telemedicina</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-radioactive me-2 text-muted"></i>Imagenología</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-droplet-half me-2 text-muted"></i>Vacunación</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-truck me-2 text-muted"></i>Ambulancia</a></li>
            </ul>
          </li>

          <li class="nav-item"><a class="nav-link" href="#">Sedes</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
          <li class="nav-item ms-lg-2">
            <a class="nav-link btn btn-sm px-3 py-2 text-white fw-bold rounded-3"
               style="background:var(--aqua);font-size:.8rem;" href="{{ route('login') }}"> 
               <i class="bi bi-box-arrow-in-right"></i>Inicio sesion
            </a>
          </li>
        </ul>
      </div>
  </nav>
