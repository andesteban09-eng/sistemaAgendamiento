<footer>
    <div class="container">
        <div class="row g-4 footer-content">
            <div class="col-12 col-lg-4">
                <div class="d-flex align-items-center">
                    <div class="footer-logo">
                        <img src="{{ asset('img/favicon.png') }}" class="img-fluid">
                    </div>
                    <h3>SIGAC</h3>
                </div>
                <div>
                    <h2 class="footer-info">
                        SIGAC
                    </h2>
                    <h2 class="footer-info">
                        Sistema integral web de gestión, automatización y agendamiento de citas médicas para IPS en
                        Boyacá
                    </h2>
                </div>
                <div class="footer-social">
                    <a href="" class="bi-facebook"></a>
                    <a href="" class="bi-instagram"></a>
                    <a href="" class="bi-whatsapp"></a>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <h3>Enlaces Útiles</h3>
                <a href="{{ route('inicio') }}">Inicio</a>
                <a href="#">Nosotros</a>
                <a href="#">Contacto</a>
                <a href="#">Políticas de Privacidad</a>
            </div>

            <div class="col-12 col-lg-4">
                <h3>Reguladores</h3>

                <div class="row">
                    <div class="col-4 text-center">
                        <img src="{{ asset('img/supersalud.png') }}" class="img-fluid footer-regulador">
                    </div>

                    <div class="col-4 text-center">
                        <img src="{{ asset('img/Minsalud.png') }}" class="img-fluid footer-regulador">
                    </div>

                    <div class="col-4 text-center">
                        <img src="{{ asset('img/goberBoyaca.png') }}" class="img-fluid footer-regulador">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- WhatsApp flotante -->
    <a href="https://api.whatsapp.com/send?phone=573222142537" class="whatsapp-float" target="_blank"
        title="Escríbenos por WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

</footer>
