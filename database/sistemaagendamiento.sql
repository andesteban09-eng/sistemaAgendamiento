-- =========================================================
-- 02_Crear_Base_Datos.sql
-- Base de datos: SIGAP - Sistema de Gestión de Agendamiento
-- Motor: Oracle Database
-- Usuario: SISTEMAAGENDAMIENTO
-- =========================================================


-- =========================================================
-- TABLA: USERS
-- Usuarios del sistema y control de autenticación/roles
-- =========================================================

CREATE TABLE users (
    id                  NUMBER GENERATED ALWAYS AS IDENTITY,
    name                VARCHAR2(255) NOT NULL,
    last_name           VARCHAR2(255) NOT NULL,
    email               VARCHAR2(255) NOT NULL,
    email_verified_at   TIMESTAMP,
    password            VARCHAR2(255) NOT NULL,
    rol                 VARCHAR2(255) NOT NULL,
    estado              VARCHAR2(255) NOT NULL,
    remember_token      VARCHAR2(100),
    created_at          TIMESTAMP,
    updated_at          TIMESTAMP,

    CONSTRAINT users_id_pk
        PRIMARY KEY (id),

    CONSTRAINT users_email_uk
        UNIQUE (email)
);


-- =========================================================
-- TABLA: PACIENTE
-- Información complementaria de los usuarios con rol paciente
-- =========================================================

CREATE TABLE paciente (
    idpaciente       NUMBER GENERATED ALWAYS AS IDENTITY,
    tipodoc          VARCHAR2(40) NOT NULL,
    numdoc           VARCHAR2(45) NOT NULL,
    telefono         VARCHAR2(20) NOT NULL,
    direccion        VARCHAR2(150) NOT NULL,
    ciudad           VARCHAR2(60) NOT NULL,
    fecharegistro    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estadopaciente   VARCHAR2(10) DEFAULT 'Activo' NOT NULL,
    idusuario        NUMBER NOT NULL,

    CONSTRAINT pk_paciente
        PRIMARY KEY (idpaciente),

    CONSTRAINT fk_paciente_user
        FOREIGN KEY (idusuario)
        REFERENCES users (id),

    CONSTRAINT uq_paciente
        UNIQUE (idusuario),

    CONSTRAINT uq_paciente_numdoc
        UNIQUE (numdoc),

    CONSTRAINT chk_paciente_tipodoc
        CHECK (
            tipodoc IN (
                'Cedula Ciudadania',
                'Tarjeta Identidad',
                'Cedula Extranjeria',
                'Pasaporte',
                'Registro Civil',
                'Permiso Proteccion Temporal',
                'Otro'
            )
        ),

    CONSTRAINT chk_paciente_estado
        CHECK (estadopaciente IN ('Activo', 'Inactivo'))
);


-- =========================================================
-- TABLA: PROFESIONALSALUD
-- Información complementaria de los usuarios profesionales
-- =========================================================

CREATE TABLE profesionalsalud (
    idprofesionalsalud       NUMBER GENERATED ALWAYS AS IDENTITY,
    tipodoc                  VARCHAR2(40) NOT NULL,
    numdoc                   VARCHAR2(45) NOT NULL,
    telefono                 VARCHAR2(20) NOT NULL,
    estadoprofesionalsalud   VARCHAR2(10) DEFAULT 'Activo' NOT NULL,
    idusuario                NUMBER NOT NULL,

    CONSTRAINT pk_profesionalsalud
        PRIMARY KEY (idprofesionalsalud),

    CONSTRAINT fk_profesionalsalud_user
        FOREIGN KEY (idusuario)
        REFERENCES users (id),

    CONSTRAINT uq_profesionalsalud
        UNIQUE (idusuario),

    CONSTRAINT uq_prof_numdoc
        UNIQUE (numdoc),

    CONSTRAINT chk_prof_tipodoc
        CHECK (
            tipodoc IN (
                'Cedula Ciudadania',
                'Tarjeta Identidad',
                'Cedula Extranjeria',
                'Pasaporte',
                'Registro Civil',
                'Permiso Proteccion Temporal',
                'Otro'
            )
        ),

    CONSTRAINT chk_prof_estado
        CHECK (
            estadoprofesionalsalud IN ('Activo', 'Inactivo')
        )
);


-- =========================================================
-- TABLA: SEDE
-- Sedes de atención de los laboratorios
-- =========================================================

CREATE TABLE sede (
    idsede       NUMBER GENERATED ALWAYS AS IDENTITY,
    nombre       VARCHAR2(80) NOT NULL,
    ciudad       VARCHAR2(100) NOT NULL,
    direccion    VARCHAR2(60) NOT NULL,
    detalles     VARCHAR2(150) NOT NULL,
    estadosede   VARCHAR2(10) DEFAULT 'Activo' NOT NULL,

    CONSTRAINT pk_sede
        PRIMARY KEY (idsede),

    CONSTRAINT chk_sede_estado
        CHECK (estadosede IN ('Activo', 'Inactivo'))
);


-- =========================================================
-- TABLA: TIPOSERVICIO
-- Categorías o tipos de servicios ofrecidos
-- =========================================================

CREATE TABLE tiposervicio (
    idtiposervicio       NUMBER GENERATED ALWAYS AS IDENTITY,
    nombre               VARCHAR2(80) NOT NULL,
    descripcion          CLOB NOT NULL,
    estadotiposervicio   VARCHAR2(10) DEFAULT 'Activo' NOT NULL,

    CONSTRAINT pk_tiposervicio
        PRIMARY KEY (idtiposervicio),

    CONSTRAINT chk_tiposerv_estado
        CHECK (
            estadotiposervicio IN ('Activo', 'Inactivo')
        )
);


-- =========================================================
-- TABLA: SERVICIO
-- Servicios/exámenes ofrecidos por el laboratorio
-- =========================================================

CREATE TABLE servicio (
    idservicio       NUMBER GENERATED ALWAYS AS IDENTITY,
    idtiposervicio   NUMBER NOT NULL,
    nombre           VARCHAR2(80) NOT NULL,
    precio           NUMBER(10,2) NOT NULL,
    prerequisitos    CLOB,
    estadoservicio   VARCHAR2(10) DEFAULT 'Activo' NOT NULL,

    CONSTRAINT pk_servicio
        PRIMARY KEY (idservicio),

    CONSTRAINT fk_serv_tiposerv
        FOREIGN KEY (idtiposervicio)
        REFERENCES tiposervicio (idtiposervicio),

    CONSTRAINT chk_serv_estado
        CHECK (
            estadoservicio IN ('Activo', 'Inactivo')
        )
);


-- =========================================================
-- TABLA: AGENDA
-- Horarios disponibles de los profesionales
-- =========================================================

CREATE TABLE agenda (
    idhorariodispo       NUMBER GENERATED ALWAYS AS IDENTITY,
    idprofesionalsalud   NUMBER NOT NULL,
    idsede               NUMBER NOT NULL,
    fecha                DATE NOT NULL,
    horainicio           INTERVAL DAY(0) TO SECOND(0) NOT NULL,
    consultorio         VARCHAR2(45) NOT NULL,

    CONSTRAINT pk_agenda
        PRIMARY KEY (idhorariodispo),

    CONSTRAINT fk_agenda_prof
        FOREIGN KEY (idprofesionalsalud)
        REFERENCES profesionalsalud (idprofesionalsalud),

    CONSTRAINT fk_agenda_sede
        FOREIGN KEY (idsede)
        REFERENCES sede (idsede)
);


-- =========================================================
-- TABLA: PERFILSERVICIO
-- Servicios para los cuales está capacitado cada profesional
-- =========================================================

CREATE TABLE perfilservicio (
    idperfilservicio      NUMBER GENERATED ALWAYS AS IDENTITY,
    idprofesionalsalud    NUMBER NOT NULL,
    idservicio            NUMBER NOT NULL,
    idtiposervicio        NUMBER NOT NULL,
    fechaasignacion       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estadoperfil          VARCHAR2(10) DEFAULT 'Activo' NOT NULL,

    CONSTRAINT pk_perfilservicio
        PRIMARY KEY (idperfilservicio),

    CONSTRAINT fk_perfilservicio_prof
        FOREIGN KEY (idprofesionalsalud)
        REFERENCES profesionalsalud (idprofesionalsalud),

    CONSTRAINT fk_servicio_perfilservicio
        FOREIGN KEY (idservicio)
        REFERENCES servicio (idservicio),

    CONSTRAINT fk_perfilservicio_tiposervicio
        FOREIGN KEY (idtiposervicio)
        REFERENCES tiposervicio (idtiposervicio),

    CONSTRAINT uq_perfilservicio
        UNIQUE (idprofesionalsalud, idservicio),

    CONSTRAINT chk_perfilservicio_estado
        CHECK (
            estadoperfil IN ('Activo', 'Inactivo')
        )
);


-- =========================================================
-- TABLA: CITA
-- Citas agendadas por los pacientes
-- =========================================================

CREATE TABLE cita (
    idcita           NUMBER GENERATED ALWAYS AS IDENTITY,
    idpaciente       NUMBER NOT NULL,
    idtiposervicio   NUMBER NOT NULL,
    idhorariodispo   NUMBER NOT NULL,
    idservicio       NUMBER NOT NULL,
    fechacita        TIMESTAMP NOT NULL,
    detalle          VARCHAR2(100) NOT NULL,
    estadocita       VARCHAR2(10) DEFAULT 'Pendiente' NOT NULL,

    CONSTRAINT pk_cita
        PRIMARY KEY (idcita),

    CONSTRAINT fk_cita_paciente
        FOREIGN KEY (idpaciente)
        REFERENCES paciente (idpaciente),

    CONSTRAINT fk_cita_agenda
        FOREIGN KEY (idhorariodispo)
        REFERENCES agenda (idhorariodispo),

    CONSTRAINT fk_cita_tipo
        FOREIGN KEY (idtiposervicio)
        REFERENCES tiposervicio (idtiposervicio),

    CONSTRAINT fk_cita_servicio
        FOREIGN KEY (idservicio)
        REFERENCES servicio (idservicio),

    CONSTRAINT chk_cita_estado
        CHECK (
            estadocita IN (
                'Pendiente',
                'Realizada',
                'Cancelada'
            )
        )
);


-- =========================================================
-- TABLA: ORDENLABORATORIO
-- Órdenes generadas a partir de una cita
-- =========================================================

CREATE TABLE ordenlaboratorio (
    idordenlaboratorio   NUMBER GENERATED ALWAYS AS IDENTITY,
    idcita               NUMBER NOT NULL,
    idsede               NUMBER NOT NULL,
    fechaorden           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estadoorden          VARCHAR2(10) DEFAULT 'Activo' NOT NULL,

    CONSTRAINT pk_ordenlaboratorio
        PRIMARY KEY (idordenlaboratorio),

    CONSTRAINT fk_orden_cita
        FOREIGN KEY (idcita)
        REFERENCES cita (idcita),

    CONSTRAINT fk_orden_sede
        FOREIGN KEY (idsede)
        REFERENCES sede (idsede),

    CONSTRAINT chk_orden_estado
        CHECK (
            estadoorden IN (
                'Activo',
                'Inactivo',
                'Cancelada'
            )
        )
);


-- =========================================================
-- TABLA: ORDENLABORATORIOSERVICIO
-- Servicios incluidos en cada orden de laboratorio
-- =========================================================

CREATE TABLE ordenlaboratorioservicio (
    idordenlaboratorio   NUMBER NOT NULL,
    idservicio           NUMBER NOT NULL,

    CONSTRAINT pk_ordenserv
        PRIMARY KEY (
            idordenlaboratorio,
            idservicio
        ),

    CONSTRAINT fk_ordenserv_orden
        FOREIGN KEY (idordenlaboratorio)
        REFERENCES ordenlaboratorio (idordenlaboratorio)
        ON DELETE CASCADE,

    CONSTRAINT fk_ordenserv_serv
        FOREIGN KEY (idservicio)
        REFERENCES servicio (idservicio)
);


-- =========================================================
-- VISTA: VW_PROFESIONAL_SERVICIOS
-- Relación legible entre profesionales y servicios asignados
-- =========================================================

CREATE OR REPLACE VIEW vw_profesional_servicios AS
SELECT
    ps.idperfilservicio,
    ps.idprofesionalsalud,
    u.name || ' ' || u.last_name AS profesional,
    ps.idservicio,
    s.nombre AS servicio,
    ps.idtiposervicio,
    ts.nombre AS tipo_servicio,
    ps.fechaasignacion,
    ps.estadoperfil
FROM perfilservicio ps
INNER JOIN profesionalsalud p
    ON p.idprofesionalsalud = ps.idprofesionalsalud
INNER JOIN users u
    ON u.id = p.idusuario
INNER JOIN servicio s
    ON s.idservicio = ps.idservicio
INNER JOIN tiposervicio ts
    ON ts.idtiposervicio = ps.idtiposervicio;



DESC AGENDA;


desc servicio;


SELECT
    constraint_name,
    constraint_type
FROM user_constraints
WHERE table_name = 'AGENDA'
ORDER BY constraint_name;
