(() => {
    let calendario = null;
    let elementoCalendario = null;

    const variantes = {
        Pendiente: "warning",
        Confirmada: "info",
        Realizada: "success",
        Atendida: "success",
        Cancelada: "danger",
    };

    const mensajes = {
        Pendiente: "El auxiliar debe confirmar la llegada del paciente.",
        Confirmada: "Puedes finalizar la cita cuando completes la atención.",
        Realizada: "Esta atención ya fue registrada.",
        Atendida: "Esta atención ya fue registrada.",
        Cancelada: "Esta cita fue cancelada.",
    };

    function asignarTexto(id, valor, alternativa = "Sin información") {
        const elemento = document.getElementById(id);

        if (elemento) {
            elemento.textContent = valor || alternativa;
        }
    }

    function abrirDetalleCita(evento) {
        const props = evento.extendedProps;

        asignarTexto("modalIdentificador", `Cita #${evento.id}`);
        asignarTexto("modalPaciente", props.paciente);
        asignarTexto("modalServicio", props.servicio);
        asignarTexto("modalFecha", props.fecha);
        asignarTexto("modalHora", props.hora);
        asignarTexto("modalSede", props.sede);
        asignarTexto("modalConsultorio", props.consultorio);

        asignarTexto(
            "modalObservacion",
            props.observacion,
            "Sin observaciones",
        );

        asignarTexto(
            "modalMensajeEstado",
            mensajes[props.estadoCita],
            "No hay operaciones disponibles.",
        );

        const estado = document.getElementById("modalEstado");

        estado.textContent = props.estadoCita || "Sin estado";

        estado.className = `sigac-status sigac-status-${variantes[props.estadoCita] || "neutral"}`;

        const formulario = document.getElementById("formRealizarCita");
        const boton = document.getElementById("btnRealizarCita");

        const puedeRealizar =
            props.estadoCita === "Confirmada" &&
            props.puedeRealizar === true &&
            Boolean(props.urlRealizar);

        formulario.hidden = !puedeRealizar;
        boton.disabled = !puedeRealizar;

        if (puedeRealizar) {
            formulario.action = props.urlRealizar;
        } else {
            formulario.removeAttribute("action");
        }

        const elementoModal = document.getElementById("modalDetalleCita");

        if (elementoModal && typeof bootstrap !== "undefined") {
            bootstrap.Modal.getOrCreateInstance(elementoModal).show();
        }
    }

    function destruirCalendario() {
        if (calendario) {
            calendario.destroy();
        }

        calendario = null;
        elementoCalendario = null;
    }

    function inicializarCalendario() {
        const elemento = document.getElementById("calendario");

        if (!elemento) {
            destruirCalendario();
            return;
        }

        if (calendario && elementoCalendario === elemento) {
            return;
        }

        destruirCalendario();

        if (typeof FullCalendar === "undefined") {
            elemento.textContent = "No fue posible cargar FullCalendar.";
            console.error("FullCalendar no está disponible.");
            return;
        }

        let eventos;

        try {
            eventos = JSON.parse(elemento.dataset.eventos || "[]");

            if (!Array.isArray(eventos)) {
                throw new Error("Los eventos deben ser un arreglo.");
            }
        } catch (error) {
            elemento.textContent = "No fue posible cargar las citas.";
            console.error(error);
            return;
        }

        calendario = new FullCalendar.Calendar(elemento, {
            initialView: "dayGridMonth",

            locale: "es",

            firstDay: 1,

            height: "auto",

            views: {
                dayGridMonth: {
                    contentHeight: 460,
                },
            },
            timeZone: "local",

            displayEventTime: true,

            editable: false,

            selectable: false,

            nowIndicator: true,

            dayMaxEvents: true,

            eventTimeFormat: {
                hour: "numeric",
                minute: "2-digit",
                meridiem: "short",
            },

            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay",
            },

            buttonText: {
                today: "Hoy",
                month: "Mes",
                week: "Semana",
                day: "Día",
            },

            events: eventos,

            eventClick: function (info) {
                abrirDetalleCita(info.event);
            },
        });

        calendario.render();

        elementoCalendario = elemento;
    }

    document.addEventListener("click", function (event) {
        const boton = event.target.closest("[data-cita-id]");

        if (!boton || !calendario) {
            return;
        }

        const cita = calendario.getEventById(boton.dataset.citaId);

        if (cita) {
            abrirDetalleCita(cita);
        }
    });

    document.addEventListener("DOMContentLoaded", inicializarCalendario);

    document.addEventListener("livewire:navigated", inicializarCalendario);

    document.addEventListener("livewire:navigating", destruirCalendario);

    if (document.readyState !== "loading") {
        inicializarCalendario();
    }
})();
