# SIGAP - Sistema de Gestión de Agendamiento

Sistema web para la gestión de usuarios, servicios, profesionales, disponibilidad y agendamiento de citas médicas para Instituciones Prestadoras de Servicios de Salud (IPS).

SIGAP nace como proyecto formativo del programa **Tecnólogo en Análisis y Desarrollo de Software del SENA** y está orientado a centralizar y facilitar el proceso de agendamiento de citas, respetando las responsabilidades y permisos definidos para cada rol de usuario.

> **Estado del proyecto:** 🚧 En desarrollo
> **Objetivo de versión:** SIGAP 1.0 - Diciembre de 2026

---

## 📋 Descripción

SIGAP permite gestionar el proceso de agendamiento de citas médicas desde diferentes roles dentro de una IPS.

El sistema centraliza la información relacionada con:

* Usuarios y roles.
* Pacientes.
* Profesionales de la salud.
* Auxiliares.
* Sedes.
* Tipos de servicio.
* Servicios médicos.
* Disponibilidad y agendas.
* Citas médicas.

El diseño del sistema busca separar las responsabilidades de cada actor y garantizar que las citas sean programadas únicamente sobre disponibilidades válidas.

---

## 🎯 Objetivo

Desarrollar un sistema web que permita a una IPS gestionar de forma organizada el proceso de agendamiento de citas médicas, desde la administración de usuarios y servicios hasta la creación, consulta, modificación y cancelación de citas.

El proyecto se enfoca exclusivamente en la **gestión de usuarios y el agendamiento de citas médicas**, sin incluir módulos propios de un ERP hospitalario como facturación, contabilidad, nómina o inventario.

---

## 👥 Roles del sistema

SIGAP contempla cuatro roles principales:

| Rol               | Responsabilidad                                                                                      |
| ----------------- | ---------------------------------------------------------------------------------------------------- |
| **Administrador** | Gestión de usuarios, profesionales, pacientes, auxiliares, servicios y configuración administrativa. |
| **Auxiliar**      | Gestión de la disponibilidad y agenda de los profesionales.                                          |
| **Profesional**   | Consulta y gestión de las citas asociadas a su actividad profesional.                                |
| **Paciente**      | Consulta de servicios, selección de disponibilidad, agendamiento y gestión de sus citas.             |

---

## ⚙️ Funcionalidades

### 🔐 Autenticación

* Inicio de sesión mediante correo y contraseña.
* Manejo de sesiones.
* Cierre de sesión.
* Verificación de correo.
* Acceso condicionado por autenticación.

### 👤 Gestión de usuarios

El administrador puede gestionar los principales perfiles del sistema:

* Pacientes.
* Profesionales de la salud.
* Auxiliares.

Las funcionalidades administrativas incluyen creación, consulta, actualización y manejo de estados según corresponda.

### 🏥 Servicios

SIGAP maneja dos niveles para los servicios:

```text
Tipo de servicio
       │
       └── Servicio
```

Esto permite organizar los servicios ofrecidos por la IPS y mantener su estado activo o inactivo.

### 👨‍⚕️ Asignación de servicios a profesionales

La tabla `PERFILSERVICIO` determina los servicios para los cuales un profesional se encuentra autorizado.

La relación principal es:

```text
Profesional
     │
     ▼
PERFILSERVICIO
     │
     ▼
Servicio
```

De esta manera, el sistema puede determinar qué profesionales están habilitados para atender un servicio determinado.

### 📅 Gestión de agenda

El auxiliar puede administrar las disponibilidades de los profesionales.

Cada disponibilidad contiene información como:

* Profesional.
* Sede.
* Fecha.
* Hora.
* Consultorio.

La agenda constituye la fuente de disponibilidad sobre la cual los pacientes pueden solicitar una cita.

### 🗓️ Agendamiento de citas

El paciente realiza el proceso de forma progresiva:

```text
Tipo de servicio
        ↓
Servicio
        ↓
Disponibilidades
        ↓
Selección de horario
        ↓
Detalle
        ↓
Confirmación
        ↓
Cita registrada
```

El paciente no necesita seleccionar manualmente al profesional. El profesional y la sede se determinan a partir de la disponibilidad seleccionada en la agenda.

### 📌 Gestión de citas del paciente

El paciente puede:

* Consultar sus citas.
* Ver el detalle de una cita.
* Reprogramar una cita pendiente.
* Modificar el detalle.
* Cancelar una cita pendiente.

Las citas canceladas no se eliminan físicamente de la base de datos, sino que cambian su estado a `Cancelada`.

### ⏱️ Control de disponibilidad

El sistema evita mostrar horarios:

* Que ya hayan pasado.
* Que estén ocupados por una cita activa.
* Que no correspondan a un profesional autorizado para el servicio.

Cuando una cita es cancelada, el horario puede volver a estar disponible.

---

## 🧠 Modelo funcional del agendamiento

El núcleo del agendamiento se basa en tres entidades principales:

```text
PERFILSERVICIO
      │
      │ determina qué servicios puede prestar
      ▼
PROFESIONALSALUD
      │
      │ genera disponibilidades
      ▼
AGENDA
      │
      │ representa fecha, hora, sede y consultorio
      ▼
CITA
      │
      │ relaciona al paciente con la disponibilidad
      ▼
PACIENTE
```

De esta forma:

* `PERFILSERVICIO` determina la capacidad del profesional.
* `AGENDA` determina cuándo y dónde está disponible.
* `CITA` registra quién reservó esa disponibilidad.

---

## 🗃️ Base de datos

SIGAP utiliza **Oracle Database** como sistema gestor de base de datos.

Entre las principales tablas se encuentran:

```text
USERS
PACIENTE
PROFESIONALSALUD
SEDE
TIPOSERVICIO
SERVICIO
AGENDA
PERFILSERVICIO
CITA
ORDENLABORATORIO
ORDENLABORATORIOSERVICIO
```

También se utiliza la vista:

```text
VW_PROFESIONAL_SERVICIOS
```

para consultar de forma legible la relación entre profesionales y servicios asignados.

---

## 🛠️ Tecnologías

### Backend

* PHP
* Laravel 13
* Laravel Eloquent
* Livewire
* Livewire Volt

### Base de datos

* Oracle Database
* Yajra Laravel OCI8

### Frontend

* HTML5
* CSS3
* Bootstrap 5
* Bootstrap Icons
* JavaScript

### Herramientas

* Git
* GitHub
* Composer
* Node.js
* npm
* Laragon
* Visual Studio Code

El proyecto utiliza Yajra OCI8 para la integración entre Laravel y Oracle.

---

## 🏗️ Arquitectura

El proyecto utiliza la estructura de Laravel basada en:

```text
app/
├── Http/
│   └── Controllers/
├── Models/
└── ...

resources/
└── views/

routes/
└── web.php

public/
└── ...

database/
└── ...
```

Los controladores están organizados según el contexto funcional:

```text
Controllers/
├── Admin/
├── Auxiliar/
├── Paciente/
└── Profesional/
```

Esto permite mantener separadas las responsabilidades correspondientes a cada rol.

---

## 🚀 Instalación

### Requisitos

Antes de instalar SIGAP se recomienda contar con:

* PHP 8.3 o superior.
* Composer.
* Node.js y npm.
* Oracle Database.
* Extensión OCI8 para PHP.
* Git.

### Clonar el proyecto

```bash
git clone https://github.com/andesteban09-eng/sistemaAgendamiento.git
cd sistemaAgendamiento
```

### Instalar dependencias PHP

```bash
composer install
```

### Instalar dependencias frontend

```bash
npm install
```

### Configurar entorno

Copiar el archivo de ejemplo:

```bash
cp .env.example .env
```

En Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generar la clave de la aplicación:

```bash
php artisan key:generate
```

### Configuración de Oracle

Configurar en `.env` los parámetros correspondientes a la conexión Oracle utilizada por el proyecto.

Ejemplo:

```env
DB_CONNECTION=oracle
DB_HOST=127.0.0.1
DB_PORT=1521
DB_DATABASE=XE
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

> No subir nunca el archivo `.env` al repositorio.

---

## ▶️ Ejecución

Para ejecutar el proyecto localmente:

```bash
php artisan serve
```

También puede ejecutarse mediante un entorno local como Laragon.

Para compilar los recursos frontend:

```bash
npm run build
```

---

## 📊 Estado actual del proyecto

### ✅ Implementado

* Autenticación y manejo de sesiones.
* Gestión administrativa de pacientes.
* Gestión administrativa de profesionales.
* Gestión administrativa de auxiliares.
* Gestión de tipos de servicio.
* Gestión de servicios.
* Gestión de agendas.
* Dashboard del paciente.
* Dashboard del profesional.
* Agendamiento de citas por parte del paciente.
* Consulta de citas del paciente.
* Visualización del detalle de una cita.
* Edición y reprogramación de citas.
* Cancelación de citas.
* Validación de disponibilidad.
* Asociación automática del profesional mediante la agenda.

### 🚧 En desarrollo

* Gestión de citas desde el rol auxiliar.
* Gestión de citas desde el rol profesional.
* Reglas completas de reasignación y atención de citas.
* Pruebas integrales del sistema.
* Mejoras de seguridad y validaciones.
* Documentación funcional y técnica.

### 📌 Objetivo de SIGAP 1.0

La meta es disponer de una versión funcional, probada y documentada del sistema durante **diciembre de 2026**, con capacidad para ser presentada como solución tecnológica para una IPS o laboratorio clínico.

---

## 📋 Requerimientos funcionales

El proyecto contempla como alcance principal:

1. Inicio de sesión mediante correo y contraseña.
2. Gestión de usuarios por parte del administrador.
3. Actualización de información de usuarios.
4. Agendamiento de citas.
5. Reasignación y cancelación de citas según las reglas definidas.
6. Gestión de estados.
7. Prevención de duplicidad de horarios.
8. Asignación de servicios a profesionales autorizados.
9. Consulta de citas registradas.
10. Gestión de la disponibilidad y agenda por parte del auxiliar.

El estado de cada requerimiento se irá actualizando conforme avance el desarrollo.

---

## 🔄 Flujo general de una cita

```text
Administrador
     │
     ├── registra profesionales
     ├── registra servicios
     └── administra usuarios
             │
             ▼
        PERFILSERVICIO
             │
             ▼
          Profesional
             │
             ▼
        Auxiliar crea
          AGENDA
             │
             ▼
          Paciente
             │
             ▼
    selecciona servicio
             │
             ▼
    selecciona disponibilidad
             │
             ▼
          crea CITA
             │
             ▼
      Profesional / Auxiliar
             │
             ▼
       gestión de la cita
```

---

## 📚 Documentación

La documentación del proyecto se complementará progresivamente con:

* Requerimientos funcionales.
* Casos de uso.
* Modelo de datos.
* Diagramas UML.
* Prototipos de interfaz.
* Casos de prueba.
* Manual de usuario.
* Manual técnico.
* Guía de instalación y despliegue.

---

## 🔒 Seguridad

Las credenciales y configuraciones sensibles del entorno local deben mantenerse fuera del repositorio.

El archivo `.env` se utiliza para la configuración local de la aplicación y debe permanecer fuera del control de versiones.

El repositorio mantiene un archivo `.env.example` como referencia para la configuración inicial del proyecto.

---

## 📅 Hoja de ruta

### Fase 1 - Núcleo del sistema

* [x] Autenticación.
* [x] Gestión básica de usuarios.
* [x] Servicios y tipos de servicio.
* [x] Profesionales y perfiles de servicio.
* [x] Agenda.
* [x] Agendamiento de citas del paciente.

### Fase 2 - Gestión de citas

* [x] Consulta de citas.
* [x] Detalle de citas.
* [x] Reprogramación.
* [x] Cancelación.
* [ ] Gestión por auxiliar.
* [ ] Gestión por profesional.
* [ ] Reglas finales de reasignación.

### Fase 3 - Calidad

* [ ] Pruebas funcionales.
* [ ] Pruebas de integración.
* [ ] Validaciones adicionales.
* [ ] Revisión de seguridad.
* [ ] Optimización.

### Fase 4 - SIGAP 1.0

* [ ] Documentación completa.
* [ ] Manual de usuario.
* [ ] Manual técnico.
* [ ] Preparación para despliegue.
* [ ] Demo funcional.
* [ ] Presentación a una IPS.

---

## 👨‍💻 Autores

Proyecto desarrollado como parte del programa:

**Tecnólogo en Análisis y Desarrollo de Software - SENA**

Repositorio:

**andesteban09-eng/sistemaAgendamiento**

---

## 📄 Licencia

Este proyecto se encuentra actualmente en desarrollo como proyecto formativo. La licencia definitiva será definida según la evolución y propósito de distribución de SIGAP.
