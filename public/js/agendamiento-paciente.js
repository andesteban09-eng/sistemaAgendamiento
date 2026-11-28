(function () {
    function inicializarAgendamiento() {
        const formulario = document.getElementById("formAgendarCita");

        if (!formulario || formulario.dataset.inicializado === "true") {
            return;
        }

        formulario.dataset.inicializado = "true";

        const tiposServicio = JSON.parse(formulario.dataset.tipos || "[]");

        const tipoServicioSelect = document.getElementById("idtiposervicio");

        const servicioSelect = document.getElementById("idservicio");

        const contenedorHorarios =
            document.getElementById("contenedorHorarios");

        const horariosDisponibles = document.getElementById(
            "horariosDisponibles",
        );

        const mensajeHorarios = document.getElementById("mensajeHorarios");

        const contenedorDetalle = document.getElementById("contenedorDetalle");

        const btnContinuar = document.getElementById("btnContinuar");

        let solicitudActual = null;

        /*
        |--------------------------------------------------------------------------
        | REINICIAR HORARIOS
        |--------------------------------------------------------------------------
        */

        function reiniciarHorarios() {
            if (solicitudActual) {
                solicitudActual.abort();
                solicitudActual = null;
            }

            horariosDisponibles.replaceChildren();

            horariosDisponibles.setAttribute("aria-busy", "false");

            mensajeHorarios.textContent = "";

            contenedorHorarios.hidden = true;
            contenedorDetalle.hidden = true;

            btnContinuar.disabled = true;
        }

        /*
        |--------------------------------------------------------------------------
        | TIPO DE SERVICIO → SERVICIOS
        |--------------------------------------------------------------------------
        */

        function cargarServicios(servicioAnterior = "") {
            reiniciarHorarios();

            servicioSelect.replaceChildren();

            servicioSelect.disabled = true;

            const tipoSeleccionado = tipoServicioSelect.value;

            if (!tipoSeleccionado) {
                servicioSelect.add(
                    new Option("Selecciona primero un tipo de servicio", ""),
                );

                return;
            }

            const tipo = tiposServicio.find(
                (tipo) => String(tipo.idtiposervicio) === tipoSeleccionado,
            );

            if (!tipo || !tipo.servicios?.length) {
                servicioSelect.add(
                    new Option("No hay servicios disponibles", ""),
                );

                return;
            }

            servicioSelect.add(new Option("Selecciona un servicio", ""));

            tipo.servicios.forEach(function (servicio) {
                servicioSelect.add(
                    new Option(servicio.nombre, servicio.idservicio),
                );
            });

            servicioSelect.disabled = false;

            if (servicioAnterior) {
                const existe = tipo.servicios.some(
                    (servicio) =>
                        String(servicio.idservicio) ===
                        String(servicioAnterior),
                );

                if (existe) {
                    servicioSelect.value = servicioAnterior;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MENSAJES DE DISPONIBILIDAD
        |--------------------------------------------------------------------------
        */

        function mostrarMensaje(texto, error = false) {
            const mensaje = document.createElement("div");

            mensaje.className = "sigac-booking-message";

            if (error) {
                mensaje.classList.add("sigac-booking-message-error");

                mensaje.setAttribute("role", "alert");
            }

            mensaje.textContent = texto;

            horariosDisponibles.appendChild(mensaje);

            mensajeHorarios.textContent = texto;
        }

        function mostrarCarga() {
            const contenedor = document.createElement("div");

            contenedor.className = "sigac-booking-loading";

            const spinner = document.createElement("div");

            spinner.className = "spinner-border spinner-border-sm";

            spinner.setAttribute("aria-hidden", "true");

            const texto = document.createElement("p");

            texto.className = "mt-2 mb-0";

            texto.textContent = "Consultando horarios disponibles...";

            contenedor.append(spinner, texto);

            horariosDisponibles.appendChild(contenedor);

            mensajeHorarios.textContent = texto.textContent;
        }

        /*
        |--------------------------------------------------------------------------
        | SERVICIO → HORARIOS
        |--------------------------------------------------------------------------
        */

        async function cargarHorarios(horarioAnterior = "") {
            reiniciarHorarios();

            const idservicio = servicioSelect.value;

            if (!idservicio) {
                return;
            }

            const controlador = new AbortController();

            solicitudActual = controlador;

            contenedorHorarios.hidden = false;

            horariosDisponibles.setAttribute("aria-busy", "true");

            mostrarCarga();

            try {
                const url = formulario.dataset.urlHorarios.replace(
                    "__servicio__",
                    encodeURIComponent(idservicio),
                );

                const respuesta = await fetch(url, {
                    method: "GET",

                    headers: {
                        Accept: "application/json",
                    },

                    signal: controlador.signal,

                    cache: "no-store",
                });

                if (!respuesta.ok) {
                    throw new Error("No fue posible consultar los horarios.");
                }

                const horarios = await respuesta.json();

                if (
                    solicitudActual !== controlador ||
                    !formulario.isConnected
                ) {
                    return;
                }

                if (!Array.isArray(horarios)) {
                    throw new Error("La respuesta de horarios no es válida.");
                }

                mostrarHorarios(horarios, horarioAnterior);
            } catch (error) {
                if (
                    error.name === "AbortError" ||
                    solicitudActual !== controlador ||
                    !formulario.isConnected
                ) {
                    return;
                }

                console.error(error);

                horariosDisponibles.replaceChildren();

                mostrarMensaje(
                    "No fue posible consultar los horarios disponibles.",
                    true,
                );
            } finally {
                if (solicitudActual === controlador) {
                    horariosDisponibles.setAttribute("aria-busy", "false");

                    solicitudActual = null;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MOSTRAR HORARIOS AGRUPADOS POR FECHA
        |--------------------------------------------------------------------------
        */

        function mostrarHorarios(horarios, horarioAnterior = "") {
            horariosDisponibles.replaceChildren();

            if (!horarios.length) {
                mostrarMensaje(
                    "No hay horarios disponibles para este servicio.",
                );

                return;
            }

            const grupos = new Map();

            horarios.forEach(function (horario) {
                const fecha = String(horario.fecha ?? "").substring(0, 10);

                if (!/^\d{4}-\d{2}-\d{2}$/.test(fecha)) {
                    return;
                }

                if (!grupos.has(fecha)) {
                    grupos.set(fecha, []);
                }

                grupos.get(fecha).push(horario);
            });

            if (!grupos.size) {
                mostrarMensaje(
                    "No fue posible interpretar las fechas disponibles.",
                    true,
                );

                return;
            }

            let horarioRestaurado = false;

            let cantidadHorarios = 0;

            Array.from(grupos.keys())
                .sort()
                .forEach(function (fecha) {
                    const grupo = document.createElement("section");

                    grupo.className = "sigac-slot-group";

                    const titulo = document.createElement("h3");

                    titulo.className = "sigac-slot-group-title";

                    const icono = document.createElement("i");

                    icono.className = "bi bi-calendar-event";

                    icono.setAttribute("aria-hidden", "true");

                    titulo.append(
                        icono,
                        document.createTextNode(formatearFecha(fecha)),
                    );

                    const grid = document.createElement("div");

                    grid.className = "sigac-slot-grid";

                    grupos.get(fecha).forEach(function (horario) {
                        const tarjeta = crearTarjetaHorario(horario);

                        const radio = tarjeta.querySelector("input");

                        if (
                            horarioAnterior &&
                            String(horario.idhorariodispo) ===
                                String(horarioAnterior)
                        ) {
                            radio.checked = true;

                            horarioRestaurado = true;
                        }

                        grid.appendChild(tarjeta);

                        cantidadHorarios++;
                    });

                    grupo.append(titulo, grid);

                    horariosDisponibles.appendChild(grupo);
                });

            mensajeHorarios.textContent = `${cantidadHorarios} horarios disponibles.`;

            if (horarioRestaurado) {
                actualizarSeleccion();
            } else if (horarioAnterior) {
                mostrarMensaje(
                    "El horario seleccionado anteriormente ya no está disponible. Selecciona otro horario.",
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CREAR TARJETA DE HORARIO
        |--------------------------------------------------------------------------
        */

        function crearTarjetaHorario(horario) {
            const label = document.createElement("label");

            label.className = "sigac-slot";

            const radio = document.createElement("input");

            radio.type = "radio";

            radio.name = "idhorariodispo";

            radio.value = horario.idhorariodispo;

            radio.className = "sigac-slot-radio visually-hidden";

            radio.required = true;

            const contenido = document.createElement("span");

            contenido.className = "sigac-slot-content";

            const hora = document.createElement("strong");

            hora.className = "sigac-slot-time";

            const icono = document.createElement("i");

            icono.className = "bi bi-clock";

            icono.setAttribute("aria-hidden", "true");

            hora.append(
                icono,

                document.createTextNode(formatearHora(horario.horainicio)),
            );

            const sede = document.createElement("span");

            sede.className = "sigac-slot-info";

            sede.textContent = horario.sede?.nombre || "Sede sin información";

            const consultorio = document.createElement("span");

            consultorio.className = "sigac-slot-info";

            consultorio.textContent = `Consultorio: ${horario.consultorio ?? "Sin información"}`;

            const usuarioProfesional = horario.profesional?.user;

            const nombreProfesional = [
                usuarioProfesional?.name,

                usuarioProfesional?.last_name,
            ]
                .filter(Boolean)
                .join(" ");

            const profesional = document.createElement("span");

            profesional.className = "sigac-slot-info";

            profesional.textContent =
                nombreProfesional || "Profesional sin información";

            contenido.append(hora, sede, consultorio, profesional);

            radio.addEventListener("change", actualizarSeleccion);

            label.append(radio, contenido);

            return label;
        }

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR SELECCIÓN
        |--------------------------------------------------------------------------
        */

        function actualizarSeleccion() {
            const seleccionado = formulario.querySelector(
                'input[name="idhorariodispo"]:checked',
            );

            contenedorDetalle.hidden = !seleccionado;

            btnContinuar.disabled = !seleccionado;
        }

        /*
        |--------------------------------------------------------------------------
        | FORMATEAR FECHA Y HORA
        |--------------------------------------------------------------------------
        */

        function formatearFecha(fecha) {
            const [anio, mes, dia] = fecha.split("-").map(Number);

            const fechaObjeto = new Date(anio, mes - 1, dia);

            return fechaObjeto.toLocaleDateString("es-CO", {
                weekday: "long",
                day: "numeric",
                month: "long",
                year: "numeric",
            });
        }

        function formatearHora(hora) {
            const valor = String(hora ?? "");

            const partes = valor.match(/(\d{2}):(\d{2})/);

            if (!partes) {
                return valor || "Hora sin información";
            }

            return `${partes[1]}:${partes[2]}`;
        }

        /*
        |--------------------------------------------------------------------------
        | EVENTOS
        |--------------------------------------------------------------------------
        */

        tipoServicioSelect.addEventListener("change", () => cargarServicios());

        servicioSelect.addEventListener("change", () => cargarHorarios());

        /*
        |--------------------------------------------------------------------------
        | RESTAURAR INFORMACIÓN DESPUÉS DE UN ERROR
        |--------------------------------------------------------------------------
        */

        cargarServicios(formulario.dataset.servicioAnterior);

        if (servicioSelect.value) {
            cargarHorarios(formulario.dataset.horarioAnterior);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | INICIALIZACIÓN NORMAL Y NAVEGACIÓN LIVEWIRE
    |--------------------------------------------------------------------------
    */

    document.addEventListener("DOMContentLoaded", inicializarAgendamiento);

    document.addEventListener("livewire:navigated", inicializarAgendamiento);

    if (document.readyState !== "loading") {
        inicializarAgendamiento();
    }
})();
