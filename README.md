# SIGAC — Sistema Integral Web de Gestión, Automatización y Agendamiento de Citas

**Sistema web de gestión administrativa y agendamiento de citas, desarrollado con Laravel y Oracle Database.**

**Estado:** En desarrollo — consolidación funcional y validación  
**Versión objetivo:** SIGAC 1.0 — Diciembre de 2026  
**Origen:** Proyecto formativo del Tecnólogo en Análisis y Desarrollo de Software — SENA

---

## 1. Descripción general

SIGAC es una aplicación web orientada a centralizar y organizar la gestión administrativa de citas, mediante la administración de usuarios, profesionales de salud, pacientes, servicios, sedes, agendas y disponibilidades.

El sistema permite relacionar los servicios solicitados con profesionales habilitados y horarios disponibles, aplicando reglas de negocio para controlar la asignación, modificación y cancelación de citas.

SIGAC se desarrolla como un **proyecto de software independiente**, con una arquitectura y un modelo funcional susceptibles de adaptación a distintos entornos organizacionales que requieran gestionar citas.

### Origen del proyecto

La iniciativa surgió a partir de un ejercicio académico de investigación y diagnóstico realizado en el contexto de Laboratorios Carvajal IPS S.A.S., que permitió identificar oportunidades de mejora relacionadas con la organización de solicitudes, el registro de información y la asignación de citas.

Dicho diagnóstico constituye un **antecedente de investigación y caso de estudio**, no una relación comercial, contractual o institucional vigente.

SIGAC no representa a Laboratorios Carvajal, no es un producto oficial de dicha organización y no se desarrolla actualmente por encargo de ninguna IPS.

---

## 2. Objetivo general

Desarrollar y consolidar un sistema web para gestionar los procesos administrativos de agendamiento de citas mediante la administración de usuarios, roles, pacientes, profesionales, sedes, servicios y disponibilidades, aplicando reglas de negocio, controles de acceso y mecanismos de integridad de la información.

### Objetivos específicos

- Organizar la información de usuarios, profesionales, pacientes, sedes y servicios mediante un modelo de datos relacional.
- Administrar las disponibilidades de los profesionales según los servicios que tienen asignados.
- Permitir la creación, consulta, modificación y cancelación de citas conforme a reglas de negocio.
- Establecer permisos diferenciados según los roles del sistema.
- Verificar el funcionamiento mediante pruebas funcionales y de integración.
- Documentar los componentes, requisitos, decisiones técnicas y procedimientos de operación del sistema.

---

## 3. Alcance funcional

La versión 1.0 se concentra en la gestión administrativa de citas y sus entidades relacionadas.

Comprende:

- Autenticación y control de acceso.
- Administración de usuarios y perfiles.
- Gestión de pacientes y profesionales de salud.
- Gestión de auxiliares.
- Administración de sedes.
- Gestión de tipos de servicio y servicios.
- Asignación de servicios a profesionales.
- Administración de agendas y disponibilidades.
- Agendamiento, consulta, reprogramación y cancelación de citas.
- Gestión operativa de citas según los permisos de cada rol.

SIGAC no pretende sustituir sistemas de historia clínica electrónica, gestión de resultados diagnósticos, contabilidad, nómina o inventario.

La facturación electrónica, la asistencia conversacional y el cliente móvil se contemplan como ampliaciones posteriores, no como funcionalidades terminadas de SIGAC 1.0.

---

## 4. Roles del sistema

| Rol | Responsabilidades |
|---|---|
| Administrador | Administración de usuarios, perfiles, profesionales, pacientes, auxiliares, servicios y otras entidades autorizadas. |
| Auxiliar | Gestión administrativa de agendas, disponibilidades y operaciones de citas habilitadas para su perfil. |
| Profesional | Consulta y gestión de información relacionada con su agenda y las citas asignadas, según los permisos implementados. |
| Paciente | Consulta de servicios, agendamiento y administración de sus propias citas. |

Las operaciones disponibles para cada perfil están sujetas a los permisos y validaciones definidos en la aplicación.

---

## 5. Funcionalidades principales

### 5.1. Autenticación y usuarios

El sistema contempla autenticación mediante correo electrónico y contraseña, manejo de sesiones y acceso diferenciado según el rol.

La administración de usuarios permite gestionar los perfiles correspondientes a pacientes, profesionales y auxiliares.

### 5.2. Gestión de servicios

Los servicios se organizan mediante dos niveles:

```text
TIPOSERVICIO
    |
    └── SERVICIO
```

Esta estructura permite clasificar los servicios y administrar su información y estado.

### 5.3. Asignación de servicios a profesionales

La entidad `PERFILSERVICIO` representa la relación entre profesionales de salud y servicios habilitados.

```text
PROFESIONALSALUD
        |
        v
  PERFILSERVICIO
        |
        v
     SERVICIO
```

Esta relación permite verificar qué profesionales pueden prestar un servicio determinado.

### 5.4. Gestión de agenda

Las disponibilidades se registran en `AGENDA` y relacionan:

- Profesional de salud.
- Sede.
- Fecha.
- Hora de inicio.
- Consultorio.

Las disponibilidades constituyen la base para asignar citas.

### 5.5. Agendamiento de citas

El paciente selecciona el servicio requerido y posteriormente una disponibilidad válida.

```text
Tipo de servicio
       |
       v
    Servicio
       |
       v
Disponibilidades válidas
       |
       v
Selección de horario
       |
       v
Detalle de la solicitud
       |
       v
Confirmación de la cita
```

La relación con el profesional y la sede se obtiene a partir de la agenda seleccionada.

### 5.6. Gestión de citas del paciente

El módulo del paciente permite:

- Consultar sus citas registradas.
- Visualizar el detalle de una cita.
- Modificar observaciones.
- Reprogramar citas pendientes.
- Cancelar citas pendientes.

Las cancelaciones se realizan mediante cambios de estado, conservando los registros para consulta e historial.

### 5.7. Reglas de reprogramación y cancelación

La implementación actual del módulo del paciente contempla las siguientes reglas:

1. Solo se permite modificar o cancelar citas en estado `Pendiente`.
2. La modificación o cancelación debe solicitarse con al menos 24 horas de anticipación al inicio de la cita.
3. Una cita puede reprogramarse como máximo tres veces.
4. El contador `NUMREPROGRAMACIONES` aumenta únicamente cuando cambia la disponibilidad asignada.
5. La modificación exclusiva del detalle no consume una reprogramación.
6. La nueva disponibilidad debe cumplir las condiciones de fecha, servicio, profesional habilitado y ocupación.
7. Las operaciones críticas utilizan transacciones y bloqueo de registros cuando corresponde.

El cumplimiento integral de estas reglas forma parte de las pruebas funcionales del proyecto.

---

## 6. Modelo funcional

El núcleo del sistema se fundamenta en las relaciones entre las siguientes entidades:

```text
SERVICIO <── PERFILSERVICIO ──> PROFESIONALSALUD
                                     |
                                     v
                                   AGENDA
                                     |
                                     v
                                    CITA
                                     |
                                     v
                                  PACIENTE
```

- `SERVICIO` representa la prestación solicitada.
- `PERFILSERVICIO` determina las asignaciones de servicios a profesionales.
- `PROFESIONALSALUD` identifica al profesional responsable.
- `AGENDA` representa la disponibilidad.
- `CITA` registra la reserva.
- `PACIENTE` identifica al usuario que solicita la cita.

El sistema utiliza reglas de negocio y mecanismos de integridad para controlar las relaciones entre estas entidades.

---

## 7. Base de datos

SIGAC utiliza **Oracle Database**, integrado con Laravel mediante Yajra OCI8.

Entre las entidades principales del modelo se encuentran:

```text
USERS
PACIENTE
PROFESIONALSALUD
SEDE
TIPOSERVICIO
SERVICIO
PERFILSERVICIO
AGENDA
CITA
ORDENLABORATORIO
ORDENLABORATORIOSERVICIO
```

La existencia de entidades relacionadas con órdenes de laboratorio no implica que SIGAC implemente un sistema de gestión clínica o de resultados diagnósticos.

El diseño utiliza relaciones, claves y restricciones de integridad, complementadas por validaciones en la lógica de aplicación.

---

## 8. Tecnologías

| Componente | Tecnología |
|---|---|
| Lenguaje backend | PHP |
| Framework backend | Laravel 13 |
| ORM | Eloquent |
| Componentes reactivos | Livewire y Livewire Volt |
| Motor de base de datos | Oracle Database |
| Integración Oracle | Yajra Laravel OCI8 |
| Interfaz | HTML5, CSS3, JavaScript |
| Framework CSS | Bootstrap 5 |
| Iconografía | Bootstrap Icons |
| Gestión de dependencias | Composer, npm |
| Control de versiones | Git y GitHub |
| Entorno local de desarrollo | Laragon |

---

## 9. Arquitectura y organización

SIGAC utiliza una aplicación web basada en Laravel, organizada en capas de presentación, lógica de aplicación y persistencia de datos.

La estructura general comprende:

```text
app/
├── Http/
│   └── Controllers/
└── Models/

resources/
└── views/

routes/
└── web.php

database/

public/

docs/
```

Los controladores se organizan por contexto funcional:

```text
Controllers/
├── Admin/
├── Auxiliar/
├── Paciente/
└── Profesional/
```

Esta distribución permite separar las responsabilidades de cada perfil sin atribuir al proyecto una arquitectura de microservicios que no ha sido implementada.

---

## 10. Instalación local

### Requisitos previos

- PHP compatible con Laravel 13.
- Composer.
- Node.js y npm.
- Oracle Database.
- Extensión OCI8 para PHP.
- Git.
- Un entorno de desarrollo web compatible.

### Clonar el repositorio

```bash
git clone https://github.com/andesteban09-eng/sistemaAgendamiento.git
cd sistemaAgendamiento
```

### Instalar dependencias

```bash
composer install
npm install
```

### Configurar el entorno

En PowerShell:

```powershell
Copy-Item .env.example .env
```

En sistemas compatibles con `cp`:

```bash
cp .env.example .env
```

Generar la clave de aplicación:

```bash
php artisan key:generate
```

### Configurar Oracle Database

Establecer los parámetros de conexión correspondientes al entorno local.

Ejemplo orientativo:

```dotenv
DB_CONNECTION=oracle
DB_HOST=127.0.0.1
DB_PORT=1521
DB_DATABASE=XEPDB1
DB_USERNAME=usuario_local
DB_PASSWORD=contraseña_local
```

La configuración exacta debe corresponder al controlador Oracle, servicio y variables reconocidas por la versión del proyecto.

**Importante:** la instalación requiere disponer del esquema y los objetos de base de datos necesarios. Los procedimientos de creación o importación del esquema deberán documentarse según los scripts disponibles en el repositorio.

No deben publicarse contraseñas reales ni archivos `.env`.

### Ejecutar la aplicación

```bash
php artisan serve
```

Para compilar los recursos frontend:

```bash
npm run build
```

También puede utilizarse Laragon como entorno de ejecución local.

---

## 11. Estado de desarrollo

### Implementado en el núcleo web

- [x] Autenticación y sesiones.
- [x] Gestión administrativa básica de usuarios.
- [x] Gestión de pacientes.
- [x] Gestión de profesionales.
- [x] Gestión de auxiliares.
- [x] Tipos de servicio y servicios.
- [x] Asignación de servicios a profesionales.
- [x] Gestión de agendas.
- [x] Agendamiento de citas del paciente.
- [x] Consulta y detalle de citas del paciente.
- [x] Reprogramación de citas del paciente.
- [x] Cancelación de citas del paciente.
- [x] Validaciones de disponibilidad.
- [x] Límite de tres reprogramaciones.
- [x] Restricción de 24 horas para modificación y cancelación.

### Pendiente de consolidación y validación

- [ ] Completar y verificar la gestión operativa de citas del auxiliar.
- [ ] Completar y verificar las operaciones del profesional.
- [ ] Revisar los flujos de estados y reasignación.
- [ ] Ejecutar pruebas funcionales e integrales.
- [ ] Revisar autorización y seguridad.
- [ ] Mejorar el tratamiento de errores y las validaciones.
- [ ] Revisar la experiencia de usuario.
- [ ] Completar documentación técnica y funcional.
- [ ] Preparar una demostración estable de SIGAC 1.0.

Las funcionalidades señaladas como implementadas corresponden al avance de desarrollo registrado; la validación integral de la versión continúa pendiente.

---

## 12. Hoja de ruta

### Diciembre de 2026 — SIGAC 1.0

**Objetivo:** consolidar el sistema web de gestión y agendamiento de citas.

Prioridades:

- Finalizar las funcionalidades pendientes del núcleo.
- Corregir errores detectados.
- Verificar las reglas de negocio.
- Ejecutar pruebas.
- Revisar controles de acceso.
- Actualizar la documentación.
- Preparar las evidencias académicas.
- Disponer de una demostración funcional.

**Hito académico:** presentación de evidencias de avance prevista para el 3 o 4 de diciembre de 2026.

### 2027 — Evolución del producto

Las siguientes iniciativas forman parte de la hoja de ruta futura y no de las funcionalidades implementadas en SIGAC 1.0.

#### Aplicación móvil

Desarrollo de un cliente móvil mediante **Flutter**, con comunicación con el backend de Laravel a través de una API.

Se contempla utilizar **Laravel Sanctum** para la autenticación de solicitudes y el control de acceso a los recursos autorizados.

La implementación deberá definir los casos de uso móviles, los endpoints, las políticas de autorización y los mecanismos de gestión de sesiones o tokens correspondientes.

#### Facturación electrónica

Evaluación e integración de la **API de Factus** para incorporar funcionalidades de facturación electrónica asociadas a los procesos administrativos que se definan.

Esta ampliación requerirá analizar:

- Requisitos funcionales y normativos aplicables.
- Autenticación con el proveedor.
- Información de clientes, servicios y documentos.
- Manejo de respuestas y errores.
- Persistencia y trazabilidad de las operaciones.
- Pruebas en el entorno habilitado por el proveedor.

La integración no implica desarrollar un sistema contable o ERP completo.

#### Asistente conversacional con inteligencia artificial

Diseño de un asistente conversacional para orientar a los usuarios en las operaciones autorizadas del sistema.

Se priorizará la evaluación de modelos de lenguaje ejecutados localmente, considerando requisitos de rendimiento, privacidad, seguridad y recursos disponibles.

Antes de su implementación deberán definirse:

- Alcance funcional y permisos.
- Fuentes de información autorizadas.
- Protección de información personal.
- Límites de acceso a datos.
- Validación de respuestas.
- Tratamiento de errores.
- Evaluación de riesgos y pruebas.

El asistente no se considera implementado ni se presupone que tenga autorización para ejecutar operaciones sobre datos del sistema.

---

## 13. Calidad y seguridad

El proyecto considera la seguridad y la calidad del software como actividades que deben verificarse durante su desarrollo.

Entre los aspectos prioritarios se encuentran:

- Autenticación y autorización por rol.
- Validación de datos recibidos.
- Protección de información personal.
- Control de acceso a recursos propios de cada usuario.
- Integridad de las relaciones entre entidades.
- Uso de transacciones en operaciones críticas.
- Prevención de reservas incompatibles.
- Manejo de errores.
- Pruebas funcionales y de integración.
- Mantenibilidad y documentación.

Como referencias conceptuales para la evolución del proyecto se consideran la ingeniería de requisitos, los modelos de calidad de software y las recomendaciones de seguridad para aplicaciones web.

La adopción de estos criterios no constituye por sí sola una certificación de seguridad o calidad.

---

## 14. Documentación

La documentación académica, funcional y técnica del proyecto comprende o contempla:

- Marco referencial.
- Especificación de requisitos.
- Casos de uso.
- Modelo de datos.
- Diagramas UML.
- Documentación de reglas de negocio.
- Casos y resultados de prueba.
- Manual de usuario.
- Manual técnico.
- Guía de instalación y despliegue.
- Documentación de futuras integraciones.

Los documentos deben mantener correspondencia con el estado real del código y distinguir las funcionalidades implementadas de las propuestas.

---

## 15. Control de versiones

El proyecto utiliza Git para el seguimiento de cambios y GitHub como plataforma de alojamiento del repositorio.

Se recomienda mantener cambios coherentes y verificables, evitando incluir archivos de configuración sensibles, credenciales, dependencias generadas o artefactos locales innecesarios.

Antes de registrar una modificación deben revisarse los archivos afectados y comprobarse, cuando corresponda, el funcionamiento de los componentes modificados.

---

## 16. Contexto académico y autoría

SIGAC se desarrolla como proyecto formativo del programa **Tecnólogo en Análisis y Desarrollo de Software**, del Servicio Nacional de Aprendizaje (SENA), Centro de Gestión Administrativa y Fortalecimiento Empresarial — CEGAFE, Regional Boyacá.

**Desarrollador:** Andersson Esteban Peña Romero  
**Ficha:** 3230746

**Repositorio:** https://github.com/andesteban09-eng/sistemaAgendamiento

El origen académico del proyecto no implica que el SENA o la organización utilizada como caso de estudio sean propietarios, clientes o responsables de las funcionalidades del producto.

---

## 17. Licencia

La licencia de distribución del proyecto está pendiente de definición.

Hasta que se incorpore un archivo de licencia explícito, no debe asumirse que el código dispone de una licencia de software libre u otra autorización general de reutilización.
