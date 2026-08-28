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

create table users (
   id                number generated always as identity,
   name              varchar2(255) not null,
   last_name         varchar2(255) not null,
   email             varchar2(255) not null,
   email_verified_at timestamp,
   password          varchar2(255) not null,
   rol               varchar2(255) not null,
   estado            varchar2(255) not null,
   remember_token    varchar2(100),
   created_at        timestamp,
   updated_at        timestamp,
   constraint users_id_pk primary key ( id ),
   constraint users_email_uk unique ( email )
);


-- =========================================================
-- TABLA: PACIENTE
-- Información complementaria de los usuarios con rol paciente
-- =========================================================

create table paciente (
   idpaciente     number generated always as identity,
   tipodoc        varchar2(40) not null,
   numdoc         varchar2(45) not null,
   telefono       varchar2(20) not null,
   direccion      varchar2(150) not null,
   ciudad         varchar2(60) not null,
   fecharegistro  timestamp default current_timestamp,
   estadopaciente varchar2(10) default 'Activo' not null,
   idusuario      number not null,
   constraint pk_paciente primary key ( idpaciente ),
   constraint fk_paciente_user foreign key ( idusuario )
      references users ( id ),
   constraint uq_paciente unique ( idusuario ),
   constraint uq_paciente_numdoc unique ( numdoc ),
   constraint chk_paciente_tipodoc
      check ( tipodoc in ( 'Cedula Ciudadania',
                           'Tarjeta Identidad',
                           'Cedula Extranjeria',
                           'Pasaporte',
                           'Registro Civil',
                           'Permiso Proteccion Temporal',
                           'Otro' ) ),
   constraint chk_paciente_estado check ( estadopaciente in ( 'Activo',
                                                              'Inactivo' ) )
);


-- =========================================================
-- TABLA: PROFESIONALSALUD
-- Información complementaria de los usuarios profesionales
-- =========================================================

create table profesionalsalud (
   idprofesionalsalud     number generated always as identity,
   tipodoc                varchar2(40) not null,
   numdoc                 varchar2(45) not null,
   telefono               varchar2(20) not null,
   estadoprofesionalsalud varchar2(10) default 'Activo' not null,
   idusuario              number not null,
   constraint pk_profesionalsalud primary key ( idprofesionalsalud ),
   constraint fk_profesionalsalud_user foreign key ( idusuario )
      references users ( id ),
   constraint uq_profesionalsalud unique ( idusuario ),
   constraint uq_prof_numdoc unique ( numdoc ),
   constraint chk_prof_tipodoc
      check ( tipodoc in ( 'Cedula Ciudadania',
                           'Tarjeta Identidad',
                           'Cedula Extranjeria',
                           'Pasaporte',
                           'Registro Civil',
                           'Permiso Proteccion Temporal',
                           'Otro' ) ),
   constraint chk_prof_estado check ( estadoprofesionalsalud in ( 'Activo',
                                                                  'Inactivo' ) )
);


-- =========================================================
-- TABLA: SEDE
-- Sedes de atención de los laboratorios
-- =========================================================

create table sede (
   idsede     number generated always as identity,
   nombre     varchar2(80) not null,
   ciudad     varchar2(100) not null,
   direccion  varchar2(60) not null,
   detalles   varchar2(150) not null,
   estadosede varchar2(10) default 'Activo' not null,
   constraint pk_sede primary key ( idsede ),
   constraint chk_sede_estado check ( estadosede in ( 'Activo',
                                                      'Inactivo' ) )
);


-- =========================================================
-- TABLA: TIPOSERVICIO
-- Categorías o tipos de servicios ofrecidos
-- =========================================================

create table tiposervicio (
   idtiposervicio     number generated always as identity,
   nombre             varchar2(80) not null,
   descripcion        clob not null,
   estadotiposervicio varchar2(10) default 'Activo' not null,
   constraint pk_tiposervicio primary key ( idtiposervicio ),
   constraint chk_tiposerv_estado check ( estadotiposervicio in ( 'Activo',
                                                                  'Inactivo' ) )
);


-- =========================================================
-- TABLA: SERVICIO
-- Servicios/exámenes ofrecidos por el laboratorio
-- =========================================================

create table servicio (
   idservicio     number generated always as identity,
   idtiposervicio number not null,
   nombre         varchar2(80) not null,
   precio         number(10,2) not null,
   prerequisitos  clob,
   estadoservicio varchar2(10) default 'Activo' not null,
   constraint pk_servicio primary key ( idservicio ),
   constraint fk_serv_tiposerv foreign key ( idtiposervicio )
      references tiposervicio ( idtiposervicio ),
   constraint chk_serv_estado check ( estadoservicio in ( 'Activo',
                                                          'Inactivo' ) )
);


-- =========================================================
-- TABLA: AGENDA
-- Horarios disponibles de los profesionales
-- =========================================================

create table agenda (
   idhorariodispo     number generated always as identity,
   idprofesionalsalud number not null,
   idsede             number not null,
   fecha              date not null,
   horainicio         interval day(0) to second(0) not null,
   consultorio        varchar2(45) not null,
   constraint pk_agenda primary key ( idhorariodispo ),
   constraint fk_agenda_prof foreign key ( idprofesionalsalud )
      references profesionalsalud ( idprofesionalsalud ),
   constraint fk_agenda_sede foreign key ( idsede )
      references sede ( idsede )
);


-- =========================================================
-- TABLA: PERFILSERVICIO
-- Servicios para los cuales está capacitado cada profesional
-- =========================================================

create table perfilservicio (
   idperfilservicio   number generated always as identity,
   idprofesionalsalud number not null,
   idservicio         number not null,
   idtiposervicio     number not null,
   fechaasignacion    timestamp default current_timestamp,
   estadoperfil       varchar2(10) default 'Activo' not null,
   constraint pk_perfilservicio primary key ( idperfilservicio ),
   constraint fk_perfilservicio_prof foreign key ( idprofesionalsalud )
      references profesionalsalud ( idprofesionalsalud ),
   constraint fk_servicio_perfilservicio foreign key ( idservicio )
      references servicio ( idservicio ),
   constraint fk_perfilservicio_tiposervicio foreign key ( idtiposervicio )
      references tiposervicio ( idtiposervicio ),
   constraint uq_perfilservicio unique ( idprofesionalsalud,
                                         idservicio ),
   constraint chk_perfilservicio_estado check ( estadoperfil in ( 'Activo',
                                                                  'Inactivo' ) )
);


-- =========================================================
-- TABLA: CITA
-- Citas agendadas por los pacientes
-- =========================================================

create table cita (
   idcita         number generated always as identity,
   idpaciente     number not null,
   idtiposervicio number not null,
   idhorariodispo number not null,
   idservicio     number not null,
   fechacita      timestamp not null,
   detalle        varchar2(100) not null,
   estadocita     varchar2(10) default 'Pendiente' not null,
   constraint pk_cita primary key ( idcita ),
   constraint fk_cita_paciente foreign key ( idpaciente )
      references paciente ( idpaciente ),
   constraint fk_cita_agenda foreign key ( idhorariodispo )
      references agenda ( idhorariodispo ),
   constraint fk_cita_tipo foreign key ( idtiposervicio )
      references tiposervicio ( idtiposervicio ),
   constraint fk_cita_servicio foreign key ( idservicio )
      references servicio ( idservicio ),
   constraint chk_cita_estado
      check ( estadocita in ( 'Pendiente',
                              'Realizada',
                              'Cancelada' ) )
);


-- =========================================================
-- TABLA: ORDENLABORATORIO
-- Órdenes generadas a partir de una cita
-- =========================================================

create table ordenlaboratorio (
   idordenlaboratorio number generated always as identity,
   idcita             number not null,
   idsede             number not null,
   fechaorden         timestamp default current_timestamp,
   estadoorden        varchar2(10) default 'Activo' not null,
   constraint pk_ordenlaboratorio primary key ( idordenlaboratorio ),
   constraint fk_orden_cita foreign key ( idcita )
      references cita ( idcita ),
   constraint fk_orden_sede foreign key ( idsede )
      references sede ( idsede ),
   constraint chk_orden_estado
      check ( estadoorden in ( 'Activo',
                               'Inactivo',
                               'Cancelada' ) )
);


-- =========================================================
-- TABLA: ORDENLABORATORIOSERVICIO
-- Servicios incluidos en cada orden de laboratorio
-- =========================================================

create table ordenlaboratorioservicio (
   idordenlaboratorio number not null,
   idservicio         number not null,
   constraint pk_ordenserv primary key ( idordenlaboratorio,
                                         idservicio ),
   constraint fk_ordenserv_orden foreign key ( idordenlaboratorio )
      references ordenlaboratorio ( idordenlaboratorio )
         on delete cascade,
   constraint fk_ordenserv_serv foreign key ( idservicio )
      references servicio ( idservicio )
);


-- =========================================================
-- TABLA: HISTORIALCANCELACIONES
-- Historial de cancelaciones de citas

create table historialcancelaciones (
   idhistorial number generated always as identity primary key,
   idcita      number not null,
   fecha       timestamp default systimestamp not null,
   motivo      varchar2(200) not null
);

--TRIGGER HISTORIALCANCELACIONES
--El trigger se activa cuando se elimina una cita, solamente para el administrador del sistema
create or replace trigger tr_eliminarcita after
   delete on cita
   for each row
begin
   insert into historialcancelaciones (
      idcita,
      fecha,
      motivo
   ) values
      ( :old.idcita,
        systimestamp,
        'Cita eliminada por el administrador' );
end;
/

-- =========================================================
-- VISTA: VW_PROFESIONAL_SERVICIOS
-- Relación legible entre profesionales y servicios asignados
-- =========================================================

create or replace view vw_profesional_servicios as
   select ps.idperfilservicio,
          ps.idprofesionalsalud,
          u.name
          || ' '
          || u.last_name as profesional,
          ps.idservicio,
          s.nombre as servicio,
          ps.idtiposervicio,
          ts.nombre as tipo_servicio,
          ps.fechaasignacion,
          ps.estadoperfil
     from perfilservicio ps
    inner join profesionalsalud p
   on p.idprofesionalsalud = ps.idprofesionalsalud
    inner join users u
   on u.id = p.idusuario
    inner join servicio s
   on s.idservicio = ps.idservicio
    inner join tiposervicio ts
   on ts.idtiposervicio = ps.idtiposervicio;



DESC AGENDA;


desc servicio;


select constraint_name,
       constraint_type
  from user_constraints
 where table_name = 'AGENDA'
 order by constraint_name;
