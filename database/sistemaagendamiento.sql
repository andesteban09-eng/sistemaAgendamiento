-- 02_Crear_Base_Datos.sql
-- Ejecutar conectado como SISTEMAAGENDAMIENTO

create table paciente (
   idpaciente     number generated always as identity,
   tipodoc        varchar2(40) not null,
   numdoc         varchar2(45) not null,
   nombre         varchar2(80) not null,
   apellido       varchar2(80) not null,
   telefono       varchar2(20) not null,
   direccion      varchar2(150) not null,
   ciudad         varchar2(60) not null,
   contrasena     varchar2(250) not null,
   fecharegistro  timestamp default current_timestamp,
   estadopaciente varchar2(10) default 'Activo' not null,
   constraint pk_paciente primary key ( idpaciente ),
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

create table profesionalsalud (
   idprofesionalsalud     number generated always as identity,
   tipodoc                varchar2(40) not null,
   numdoc                 varchar2(45) not null,
   nombre                 varchar2(80) not null,
   apellido               varchar2(80) not null,
   telefono               varchar2(20) not null,
   correo                 varchar2(300) not null,
   estadoprofesionalsalud varchar2(10) default 'Activo' not null,
   contrasena             varchar2(250),
   constraint pk_profesionalsalud primary key ( idprofesionalsalud ),
   constraint uq_prof_numdoc unique ( numdoc ),
   constraint uq_prof_correo unique ( correo ),
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

create table tiposervicio (
   idtiposervicio     number generated always as identity,
   nombre             varchar2(80) not null,
   descripcion        clob not null,
   estadotiposervicio varchar2(10) default 'Activo' not null,
   constraint pk_tiposervicio primary key ( idtiposervicio ),
   constraint chk_tiposerv_estado check ( estadotiposervicio in ( 'Activo',
                                                                  'Inactivo' ) )
);

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
   constraint chk_cita_estado
      check ( estadocita in ( 'Pendiente',
                              'Realizada',
                              'Cancelada' ) )
);

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


alter table cita
   add constraint fk_cita_servicio foreign key ( idservicio )
      references servicio ( idservicio );

alter table cita add idhorariodispo number not null;
alter table cita
   add constraint fk_cita_agenda foreign key ( idhorariodispo )
      references agenda ( idhorariodispo );
describe cita;

delete from cita;

delete from ordenlaboratorio;

select p.idprofesionalsalud,
       p.nombre,
       p.apellido
  from profesionalsalud p
  join perfilservicio ps
on ps.idprofesionalsalud = p.idprofesionalsalud
 where ps.idservicio = :idservicio
   and ps.estadoperfil = 'Activo'
   and p.estadoprofesionalsalud = 'Activo';

alter table perfilservicio add idtiposervicio number not null;
alter table perfilservicio
   add constraint fk_perfilservicio_tiposervicio foreign key ( idtiposervicio )
      references tiposervicio ( idtiposervicio );

alter table cita add idservicio number not null;
alter table cita
   add constraint fk_cita_servicio foreign key ( idservicio )
      references servicio ( idservicio );
commit;

  -- =========================================================
-- USERS
-- =========================================================


-- =========================================================
-- PACIENTE
-- =========================================================

alter table paciente drop column contrasena;

alter table paciente add idusuario number not null;

alter table paciente
   add constraint fk_paciente_user foreign key ( idusuario )
      references users ( id );
      alter table paciente
      add constraint uq_paciente unique (idusuario);


-- =========================================================
-- PROFESIONALSALUD
-- =========================================================

alter table profesionalsalud drop column contrasena;

alter table profesionalsalud add idusuario number not null;

alter table profesionalsalud
   add constraint fk_profesionalsalud_user foreign key ( idusuario )
      references users ( id );

      alter table profesionalsalud
      add constraint uq_profesionalsalud unique (idusuario);

alter table paciente drop column nombre;

alter table paciente drop column apellido;

alter table profesionalsalud drop column nombre;

alter table profesionalsalud drop column apellido;
commit;

