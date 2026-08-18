<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Carvajal Laboratorios IPS</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
  <!-- Estilos propios -->
  <link rel="stylesheet" href="{{ asset('css/principal.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/footer.css') }}"/>
</head>
<body>

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
          <li class="nav-item"><a class="nav-link" href="#">Inicio</a></li>
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
               style="background:var(--aqua);font-size:.8rem;" href="#agendar-cita">
              <i class="bi bi-calendar2-check me-1"></i>Agendar Cita
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- ═══ HERO ═══ -->
  <section class="hero">
    <div class="container.fluid">
      <div class="row align-items-center">
        <div class="col-lg-7">
          <h1>Laboratorio Clínico<br>Especializado en <span>Boyacá</span></h1>
          <p>En Carvajal Laboratorios IPS cuidamos tu salud con innovación, empatía y calidad. Agenda tu cita médica de forma fácil y rápida, sin filas ni llamadas.</p>
          <div class="hero-badges">
            <span class="hero-badge"><i class="bi bi-patch-check-fill"></i> Certificados ISO 9001</span>
            <span class="hero-badge"><i class="bi bi-people-fill"></i> +3.500 reseñas 5 estrellas</span>
            <span class="hero-badge"><i class="bi bi-geo-alt-fill"></i> Múltiples sedes en Boyacá</span>
          </div>
          <div class="hero-ctas">
            <a href="#agendar-cita" class="btn-hero-primary">
              <i class="bi bi-calendar2-check-fill"></i> Agendar Cita Médica
            </a>
            <a href="#servicios" class="btn-hero-secondary">
              <i class="bi bi-grid-3x3-gap-fill"></i> Ver Servicios
            </a>
          </div>
        </div>
        <div class="col-lg-5 d-none d-lg-flex justify-content-center mt-4 mt-lg-0">
          <div style="width:340px;height:300px;background:rgba(255,255,255,.07);border-radius:20px;border:1px solid rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;">
            <i class="bi bi-hospital" style="font-size:5rem;color:var(--aqua-claro);opacity:.7;"></i>
            <span style="color:rgba(255,255,255,.5);font-size:.85rem;"></span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══ STATS ═══ -->
  <div class="stats-strip">
    <div class="container">
      <div class="row justify-content-center align-items-center g-3">
        <div class="col-6 col-md-3">
          <div class="stat-item">
            <div class="num">+20<span>años</span></div>
            <p>Experiencia clínica</p>
          </div>
        </div>
        <div class="col-auto d-none d-md-block"><div class="stat-divider"></div></div>
        <div class="col-6 col-md-3">
          <div class="stat-item">
            <div class="num">3.5<span>K+</span></div>
            <p>Pacientes satisfechos</p>
          </div>
        </div>
        <div class="col-auto d-none d-md-block"><div class="stat-divider"></div></div>
        <div class="col-6 col-md-3">
          <div class="stat-item">
            <div class="num">12<span>+</span></div>
            <p>Especialidades médicas</p>
          </div>
        </div>
        <div class="col-auto d-none d-md-block"><div class="stat-divider"></div></div>
        <div class="col-6 col-md-3">
          <div class="stat-item">
            <div class="num">5<span>★</span></div>
            <p>Calificación Google</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ ACCESOS RÁPIDOS ═══ -->
  <section class="accesos">
    <div class="container">
      <h2 class="text-center">Accesos Rápidos</h2>
      <p class="subtitle text-center">Todo lo que necesitas, a un clic de distancia</p>
      <div class="row g-3 justify-content-center">

        <div class="col-6 col-md-4 col-lg-2">
          <a href="#agendar-cita" class="acceso-card">
            <div class="icon-wrap" style="background:#e8f6fd;color:var(--aqua);">
              <i class="bi bi-calendar2-check-fill"></i>
            </div>
            <h6>Agendar Cita</h6>
            <p>Consulta médica</p>
          </a>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
          <a href="#" class="acceso-card">
            <div class="icon-wrap" style="background:#eaf4ee;color:var(--verde);">
              <i class="bi bi-file-earmark-medical-fill"></i>
            </div>
            <h6>Resultados</h6>
            <p>Consulta en línea</p>
          </a>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
          <a href="#" class="acceso-card">
            <div class="icon-wrap" style="background:#fff3e0;color:#e67e22;">
              <i class="bi bi-award-fill"></i>
            </div>
            <h6>Certificados</h6>
            <p>Laborales y médicos</p>
          </a>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
          <a href="vista/gestionarusuarios/formcotizar.php" class="acceso-card">
            <div class="icon-wrap" style="background:#f0ebff;color:#7b2ff7;">
              <i class="bi bi-cash-coin"></i>
            </div>
            <h6>Cotizar</h6>
            <p>Exámenes y paquetes</p>
          </a>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
          <a href="#" class="acceso-card">
            <div class="icon-wrap" style="background:#e8f6fd;color:var(--azul-oscuro);">
              <i class="bi bi-clipboard2-pulse-fill"></i>
            </div>
            <h6>Preparación</h6>
            <p>Para tus exámenes</p>
          </a>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
          <a href="#" class="acceso-card">
            <div class="icon-wrap" style="background:#fff0f0;color:#e74c3c;">
              <i class="bi bi-geo-alt-fill"></i>
            </div>
            <h6>Sedes</h6>
            <p>Horarios y ubicaciones</p>
          </a>
        </div>

      </div>
    </div>
  </section>

  <!-- ═══ SERVICIOS ═══ -->
  <section class="servicios" id="servicios">
    <div class="container">
      <div class="text-center mb-5">
        <h2>Nuestros Servicios</h2>
        <p class="subtitle">¡Cuidamos bien de ti, de tu familia y de tu empresa!</p>
      </div>
      <div class="row g-4">

        <div class="col-6 col-md-4 col-lg-3">
          <div class="servicio-card">
            <div class="servicio-img" style="background:linear-gradient(135deg,#0a2558,#1a5f8a);">🔬</div>
            <div class="servicio-body">
              <p class="tag">Laboratorio</p>
              <h5>Laboratorio Clínico Especializado</h5>
            </div>
          </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
          <div class="servicio-card">
            <div class="servicio-img" style="background:linear-gradient(135deg,#00796b,#00b4d8);">🩺</div>
            <div class="servicio-body">
              <p class="tag">Consulta Médica</p>
              <h5>Especialistas Clínicos</h5>
            </div>
          </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
          <div class="servicio-card">
            <div class="servicio-img" style="background:linear-gradient(135deg,#5c35c9,#00b4d8);">🏠</div>
            <div class="servicio-body">
              <p class="tag">Home Care</p>
              <h5>Cuidado en Casa</h5>
            </div>
          </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
          <div class="servicio-card">
            <div class="servicio-img" style="background:linear-gradient(135deg,#1565c0,#42a5f5);">🚗</div>
            <div class="servicio-body">
              <p class="tag">CRC</p>
              <h5>Exámenes para Conductores</h5>
            </div>
          </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
          <div class="servicio-card">
            <div class="servicio-img" style="background:linear-gradient(135deg,#1a3a7a,#00b4d8);">💻</div>
            <div class="servicio-body">
              <p class="tag">Telesalud</p>
              <h5>Atención de Telemedicina</h5>
            </div>
          </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
          <div class="servicio-card">
            <div class="servicio-img" style="background:linear-gradient(135deg,#b71c1c,#e53935);">🩻</div>
            <div class="servicio-body">
              <p class="tag">Imágenes</p>
              <h5>Imágenes Diagnósticas</h5>
            </div>
          </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
          <div class="servicio-card">
            <div class="servicio-img" style="background:linear-gradient(135deg,#2e7d32,#66bb6a);">💉</div>
            <div class="servicio-body">
              <p class="tag">Prevención</p>
              <h5>Vacunación</h5>
            </div>
          </div>
        </div>

        <div class="col-6 col-md-4 col-lg-3">
          <div class="servicio-card">
            <div class="servicio-img" style="background:linear-gradient(135deg,#e65100,#ff9800);">🚑</div>
            <div class="servicio-body">
              <p class="tag">Emergencias</p>
              <h5>Traslado en Ambulancia</h5>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ═══ SECCIÓN AGENDAR CITA ═══ -->
  <section id="agendar-cita">
    <div class="container">
      <div class="row align-items-center justify-content-between g-4">

        <!-- Texto izquierdo -->
        <div class="col-lg-6 text-white">
          <p class="cita-etiqueta">
            <i class="bi bi-calendar2-heart-fill me-2"></i>Consulta Médica
          </p>
          <h2 class="cita-titulo">
            Agenda tu Cita Médica<br>de forma fácil y rápida
          </h2>
          <p class="cita-descripcion">
            Sin filas, sin llamadas. Completa el formulario en línea y uno de nuestros asesores confirmará tu cita a la mayor brevedad posible.
          </p>
          <ul class="cita-lista">
            <li><i class="bi bi-check-circle-fill"></i> Disponible las 24 horas</li>
            <li><i class="bi bi-check-circle-fill"></i> Confirmación rápida por WhatsApp o llamada</li>
            <li><i class="bi bi-check-circle-fill"></i> Todas las especialidades disponibles</li>
          </ul>
        </div>

        <!-- Tarjeta CTA derecha -->
        <div class="col-lg-5">
          <div class="cita-card">
            <div class="cita-card-icon">
              <i class="bi bi-calendar2-check-fill"></i>
            </div>
            <h5>¿Listo para agendar?</h5>
            <p>Haz clic en el botón para acceder al formulario de agendamiento de citas médicas.</p>
            <a href="{{ route('login') }}" class="btn-submit">
              <i class="bi bi-calendar2-check-fill"></i> Ir al Formulario de Cita
            </a>
            <p class="nota-privacidad">
              <i class="bi bi-shield-lock-fill me-1"></i> Tus datos están protegidos
            </p>
          </div>
        </div>

      </div>
    </div>
  </section>

    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center">
                        <div class="footer-logo">
                            <i class="bi-hospital text-white"></i>
                        </div>
                        <h3>Carvajal Laboratorios</h3>
                    </div>
                    <div >
                        <h2 class="footer-info">
                            Carvajal Laboratorios IPS
                        </h2>
                        <h2 class="footer-info">
                         Boyacá, Colombia
                        </h2>
                    </div>
                    <div class="footer-social">
                        <a href="" class="bi-facebook"></a>
                        <a href="" class="bi-instagram"></a>
                        <a href="" class="bi-whatsapp"></a>
                    </div>
                </div>

                <div class="col-6 col-lg-4">
                    <h3>Enlaces Útiles</h3>
                    <a href="../index.php">Inicio</a>
                    <a href="#">Nosotros</a>
                    <a href="#">Contacto</a>
                    <a href="#">Políticas de Privacidad</a>
                </div>

                <div class="col-6 col-lg-4">
                    <h3>Reguladores</h3>
                    <div class="row">
                        <div class="col-4 text-center">
                            <img src="./img/supersalud.png" class="img-fluid" style="width: 130px; height: auto;">
                        </div>
                        <div class="col-4 text-center">
                            <img src="./img/Minsalud.png" class="img-fluid" style="width: 130px; height: auto;">
                        </div>
                        <div class="col-4 text-center">
                            <img src="./img/goberBoyaca.png" class="img-fluid" style="width: 130px; height: auto;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

  <!-- WhatsApp flotante -->
  <a href="https://api.whatsapp.com/send?phone=573188881314" class="whatsapp-float" target="_blank" title="Escríbenos por WhatsApp">
    <i class="bi bi-whatsapp"></i>
  </a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
