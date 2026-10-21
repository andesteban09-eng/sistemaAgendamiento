# AGENTS.md — SIGAC (Sistema de Gestión de Agendamiento de Citas)

Instrucciones permanentes para trabajar en este repositorio. Describe **cómo** trabajar
aquí, no el estado puntual del proyecto (ese va en `docs/diagnostico-tecnico.md`).

- **Las secciones 1 a 9 son normativas**: contienen reglas que se aplican a todo cambio.
- **La sección 10 es orientativa** y no establece requisitos: describe líneas posibles de
  evolución futura, sin aprobar ninguna funcionalidad.

---

## 1. Propósito y alcance

SIGAC es una aplicación web para la gestión del agendamiento de citas médicas en una IPS
(o laboratorio clínico). Centraliza usuarios y roles, pacientes, profesionales de la
salud, auxiliares, sedes, tipos de servicio, servicios, disponibilidad (agenda) y citas.

- Proyecto formativo del programa Tecnólogo en Análisis y Desarrollo de Software (SENA).
- Alcance: **exclusivamente** gestión de usuarios y agendamiento de citas. **No** se
  incluyen módulos de ERP hospitalario (facturación, contabilidad, nómina, inventario).
  No los implementes sin que se pidan explícitamente.
- Roles del dominio: `administrador`, `auxiliar`, `profesional`, `paciente`
  (columna `users.rol`).
- **La marca vigente del proyecto es SIGAC.** El `README.md` y el DDL de Oracle aún
  citan la sigla histórica **SIGAP**, y las vistas muestran la marca comercial
  "Laboratorios Carvajal". **No renombres automáticamente** esas referencias
  históricas: un cambio de marca requiere una decisión explícita y un commit propio,
  separado de cualquier cambio funcional.

## 2. Stack (versiones comprobadas en `composer.lock` / `package-lock.json`)

| Capa | Tecnología | Versión |
| --- | --- | --- |
| Runtime | PHP | `^8.3` |
| Framework | Laravel Framework | 13.25.0 |
| UI | Livewire | 3.8.5 |
| UI | Livewire Volt | 1.11.2 |
| Auth API | Laravel Sanctum | 4.3.3 |
| Driver BD | yajra/laravel-oci8 | 13.10.1 |
| Frontend | Vite | 8.2.1 |
| Frontend | Tailwind CSS | 3.4.19 (instalado, **no usado** en los layouts principales) |
| CSS/JS | Bootstrap 5.3.3, Bootstrap Icons 1.11.3, FullCalendar 6.1.15 | vía **CDN** |
| Tests | Pest | 5.1.1 (+ pest-plugin-laravel 5.0.1) |
| Formato | Laravel Pint | 1.30.5 |
| Salida de test | laravel/pao | 1.1.4 |

Regla de frontend: las vistas usan **Bootstrap 5** (CDN) + CSS propio en `public/css/`.
No introduzcas utilidades Tailwind salvo que estén disponibles en
`resources/css/app.css` y el build de Vite esté en uso.

## 3. Arquitectura

```
app/
├── Http/Controllers/
│   ├── Controller.php                  ← clase base
│   ├── dashboardController.php         ← dashboard por rol (match sobre users.rol)
│   ├── Admin/                          ← Paciente, Profesional, Auxiliar, Servicio, TipoServicio
│   ├── Auxiliar/                       ← AgendaController
│   ├── Paciente/                       ← CitaController
│   ├── Profesional/                    ← ProfesionalDashboardController
│   └── Auth/                           ← VerifyEmailController
├── Http/Middleware/RoleMiddleware.php  ← alias de middleware "role"
├── Models/                             ← 9 modelos Eloquent
├── Livewire/Forms/LoginForm.php        ← login + rate limiting
├── Livewire/Actions/Logout.php
├── Providers/                          ← AppServiceProvider (URL base), VoltServiceProvider
└── View/Components/                    ← AppLayout, GuestLayout
routes/web.php                           ← CRUD de la aplicación + agrupación por rol
routes/auth.php                         ← rutas Volt (register, login, reset, verify)
routes/api.php                          ← archivo presente; NO registrado (ver §7)
resources/views/                        ← layouts/, admin/, auxiliar/, Paciente/, profesional/,
                                          components/, partials/, livewire/
database/sistemaagendamiento.sql         ← DDL de Oracle: fuente de verdad del modelo de datos
database/migrations/                    ← solo tablas Laravel por defecto (+ sanctum)
public/css/                             ← principal.css, dashboard.css, footer.css, formularios.css
```

Puntos estructurales que debes respetar:

- **El enrutado real no tiene un grupo `role:profesional`.** El profesional accede a
  `/dashboard`, y `DashboardController::index()` resuelve por rol. Si añades rutas de
  profesional, crea el grupo explícitamente.
- **Las rutas `Paciente.citas.*` llevan `P` mayúscula** (el resto de nombres de ruta van
  en minúsculas). No las renombres sin actualizar todas las vistas.
- Los layouts usan `@section('contenido')`.
- El esquema de negocio **no se crea con migraciones**: se aplica
  `database/sistemaagendamiento.sql` sobre la instancia Oracle.
- **No hay** `app/Http/Resources`, `app/Http/Requests` ni `config/cors.php`: la
  validación vive en los controladores y las respuestas son datos planos. Tenlo en
  cuenta al diseñar cualquier evolución hacia una API (ver §10).
- **`bootstrap/app.php` configura `trustProxies(at: '*')`**: es una configuración ya
  existente que **requiere evaluación según el entorno de despliegue**. Confiar en
  todos los proxies hace que la aplicación acepte como confiables las cabeceras
  `X-Forwarded-*` de cualquier origen, lo que solo es adecuado detrás de un proxy
  inverso conocido (balanceador, túnel, CDN). **No lo presentes ni lo trates como
  seguridad resuelta ni como una ventaja confirmada**: antes de cambiarlo, averigua
  cuántos niveles de proxy hay realmente en el entorno de destino y qué cabeceras
  reenvía legítimamente el proxy; ajustar mal esta opción produce URLs de redirección
  incorrectas o permite suplantación de `X-Forwarded-For`.

## 4. Convenciones de código

- **Formato**: PSR-12, 4 espacios, LF, UTF-8, newline final (`.editorconfig`).
  Formatea con `vendor/bin/pint` antes de dar por terminado un cambio.
- **Idioma**: comentarios, mensajes de validación y de interfaz en **español**; los
  identificadores técnicos oficiales se mantienen en su forma original (`App\Models\Cita`,
  `DB::transaction`, `NUMTODSINTERVAL`, `Livewire`, `Pest`, `Vite`, `Eloquent`…).
- **Capa Laravel en minúsculas**: variables, propiedades de Eloquent (`$fillable`,
  `$casts`, `$table`, `$primaryKey`), atributos de formulario (`name=`, `wire:model`),
  claves de validación y campos enviados al backend van en **minúsculas**
  (`idtiposervicio`, `idservicio`, `idhorariodispo`, `numdoc`, `tipodoc`, `name`,
  `last_name`, `email`, `password`…).
- **Excepción obligatoria**: respeta los **nombres físicos existentes de Oracle**
  (`IDPROFESIONALSALUD`, `IDSEDE`, `FECHA`, `HORAINICIO`, `CONSULTORIO`, `IDHORARIODISPO`,
  `ESTADOSEDE`). En `Auxiliar\AgendaController` y en `resources/views/auxiliar/agenda/*`
  esos campos se escriben en mayúsculas porque son nombres de columna de Oracle; no los
  "normalices" sin unificar antes controlador + vistas.
- **Casing de modelos**: es inconsistente en el repositorio y **no se debe alterar** al
  añadir código:
  - PascalCase: `Agenda`, `PerfilServicio`, `Servicio`, `TipoServicio`, `User`.
  - minúsculas: `cita.php` (`Cita`), `paciente.php` (`Paciente`),
    `profesionalSalud.php` (`ProfesionalSalud`), `sede.php` (`Sede`).
- **Tablas**: los modelos declaran `protected $table` explícito; la mayoría en minúsculas
  (`agenda`, `cita`, `paciente`, `profesionalsalud`, `sede`, `perfilservicio`) y dos en
  mayúsculas (`SERVICIO`, `TIPOSERVICIO`). Oracle no distingue mayúsculas en
  identificadores sin comillas, por lo que ambas formas son equivalentes.
- **Modelos**: todo modelo de negocio declara `$table`, `$primaryKey`,
  `public $timestamps = false`, `$fillable` y `$casts`. No se usa `$guarded`.
- **Vistas**: Blade con clases Bootstrap 5 (`form-control`, `btn btn-primary`,
  `form-check-input`). Escapa siempre con `{{ }}`.
- **Mensajes**: `->with('success', '…')`, `->with('error', '…')`,
  `->withErrors(['campo' => '…'])->withInput()`.
- **Controladores**: patrón `index / create / store / show / edit / update / destroy` más
  `toggleEstado` para activar/desactivar. La baja de **pacientes y profesionales es
  lógica** (estado → `Inactivo`); los **auxiliares se borran físicamente**.
- **Nombres de vistas** por rol: `admin/…`, `auxiliar/…`, `Paciente/…` (P mayúscula),
  `profesional/…`.
- **Evita refactorizaciones masivas** de nombres, rutas, modelos o tablas. Si un cambio de
  nomenclatura parece conveniente, requiere **necesidad justificada y autorización
  explícita**; realízalo en commits separados y verificables, nunca mezclado con un
  cambio funcional.

## 5. Autorización: rol ≠ acceso a cualquier registro

- Todo endpoint sensible va tras el middleware `role:` (alias registrado en
  `bootstrap/app.php`). Cualquier ruta nueva debe ir dentro del grupo de rol
  correspondiente.
- **Un middleware de rol NO demuestra que el usuario pueda acceder a un registro
  concreto.** El middleware comprueba `users.rol`; no comprueba la propiedad del
  registro.
- Antes de dar por buena una autorización, verifica **por recurso**:
  - Que el usuario tenga el rol necesario (**rol**).
  - Que el registro pertenezca al usuario o al ámbito que le corresponde
    (**propiedad del registro**).
  En `Paciente\CitaController` el patrón es comparar `cita.idpaciente` con el paciente
  del usuario autenticado y hacer `abort(403)` si no coincide. Replica ese patrón en
  todo endpoint de paciente y aplícalo, mutatis mutandis, a auxiliar y profesional
  (p. ej. un auxiliar solo debe operar sobre las disponibilidades de los profesionales
  que tiene asignados, si esa es la regla del dominio).
- El login exige `users.estado = 'activo'` (`App\Livewire\Forms\LoginForm`) y aplica
  rate limiting de 5 intentos. `RoleMiddleware` **no** comprueba el estado: no asumas
  que un usuario inactivo queda bloqueado fuera de su grupo de rol.
- Contraseñas: siempre `Hash::make()` al crear y al actualizar. `User` ya castea
  `password` a `hashed`, pero el código usa `Hash::make` explícito: **mantenlo**.
- No incluyas datos personales reales (cédulas, teléfonos, direcciones) en código, tests,
  commits ni documentación.

## 6. Seguridad y datos sensibles

- **`.env` aparece versionado en Git** (commit `2f53329`, "guardar .env en esta rama";
  `git ls-files` lo confirma). En la última revisión el riesgo seguía vigente.
  Consecuencias y acciones:
  - **No leas, copies, imprimas ni menciones valores de `.env`** en commits, chats,
    documentación ni en archivos de contexto. Trabaja solo con nombres de variables.
  - **Planifica la rotación de los secretos expuestos** (credenciales de la conexión
    Oracle, contraseñas de cuentas de servicio y contraseñas de la aplicación) y
    **actualiza de forma coordinada todos los componentes dependientes** de cada
    secreto (`.env`, `config/`, cadena de conexión, configuraciones de despliegue,
    cuentas del usuario Oracle y cualquier servicio que consuma esas credenciales).
    La coordinación es la parte importante: rotar un secreto sin actualizar a sus
    consumidores deja el sistema roto.
  - **Evalúa además la exclusión definitiva de Git** (`.gitignore` ya lo lista, pero eso
    no basta si el archivo ya está en el historial; requiere `git rm --cached` y, si
    corresponde, reescritura de historia).
  - **`APP_KEY` es un caso aparte: no la rotes automáticamente.** A diferencia de una
    contraseña, `APP_KEY` es la clave simétrica de cifrado de Laravel
    (`config/app.php`, `AES-256-CBC`): cambiarla invalida todo lo que dependa de ella —
    cookies de sesión cifradas, valores cifrados con `Crypt`, remember-me, colas
    cifradas y cualquier dato cifrado almacenado en la base de datos. Antes de
    proponer su rotación, comprueba si el proyecto guarda datos cifrados y, si los hay,
    planifica su re-cifrado o reseteo y la invalidación de las sesiones activas.
    **No encontrar usos de `Crypt::` en `app/` no descarta todos los posibles datos
    cifrados**: pueden existir valores cifrados en la base de datos, en la configuración,
    en colas, en cookies de sesión (`SESSION_ENCRYPT`) o en componentes externos.
    Evalúa el estado real de `SESSION_ENCRYPT` y de la base antes de concluir.
  - Esa evaluación **no está autorizada por defecto**: requiere decisión explícita y
    cuidado.
- No alteres datos de la base de datos ni **configuraciones sensibles** (`.env`,
  `config/`, `.gitignore`, credenciales, `APP_KEY`, URLs de despliegue) sin
  **autorización explícita y comprobaciones previas**.
- `APP_DEBUG` debe permanecer desactivado en entornos compartidos.

## 7. Oracle: particularidades verificadas

Motor real: **Oracle Database** vía `yajra/laravel-oci8` (`config/database.php`
→ conexión `oracle`; `config/oracle.php` replica la misma configuración).

- **El DDL de negocio está en `database/sistemaagendamiento.sql`**, no en migraciones.
  Las migraciones solo crean `users`, `password_reset_tokens`, `sessions`, `cache`,
  `jobs` y `personal_access_tokens`. Si cambias el modelo de datos, actualiza **el
  `.sql` y el modelo**; una migración no basta.
- **Claves primarias** con `number generated always as identity` y tablas de negocio sin
  `created_at`/`updated_at` → todos esos modelos usan `public $timestamps = false`.
- **`agenda.horainicio` es `interval day(0) to second(0)`**, no `date`/`time`:
  - Insertar: se envía el literal `'0 HH:MM:SS'` (ver `Auxiliar\AgendaController`).
  - Comparar: `whereRaw("horainicio > NUMTODSINTERVAL(?, 'SECOND')", [segundos])` y
    `NUMTODSINTERVAL(?, 'MINUTE')` para detectar duplicados. El cálculo de "segundos
    desde medianoche" está duplicado en `CitaController` y `AgendaController`: si lo
    tocas, revisa ambos sitios.
  - Leer la hora: `Agenda::getRawOriginal('horainicio')` + `preg_match` de `HH:MM:SS`.
    No confíes en el cast de Carbon para este campo.
- **`cita.detalle` es `varchar2(100) NOT NULL`** y Oracle trata `''` como `NULL`: el
  código sustituye el vacío por `'Sin observaciones'`. Mantén ese fallback.
- **Estados con `CHECK` y valores exactos** (comparación sensible a mayúsculas):
  - `'Activo'` / `'Inactivo'`: `estadopaciente`, `estadoprofesionalsalud`, `estadosede`,
    `estadotiposervicio`, `estadoservicio`, `estadoperfil`.
  - `'Pendiente'` / `'Realizada'` / `'Cancelada'`: `estadocita`.
  - `'Activo'` / `'Inactivo'` / `'Cancelada'`: `estadoorden`.
- `users.estado` (autenticación) usa **minúsculas** `'activo'` / `'inactivo'`, distinto
  del dominio y del default de la migración (`'Activo'`). Trátalo como un valor
  propio; no lo unifiques sin comprobar antes el DDL real (ver diagnóstico).
- `tipodoc` está restringido por `CHECK` a: `Cedula Ciudadania`, `Tarjeta Identidad`,
  `Cedula Extranjeria`, `Pasaporte`, `Registro Civil`, `Permiso Proteccion Temporal`,
  `Otro`.
- `perfilservicio` es único por `(idprofesionalsalud, idservicio)`. La reasignación de
  servicios **reactiva** (`updateOrCreate`) y **desactiva** (`estadoperfil = 'Inactivo'`),
  nunca borra.
- `cita` referencia `agenda` por `idhorariodispo`. Un horario está ocupado si existe una
  cita con `estadocita != 'Cancelada'`; al cancelar, el horario vuelve a quedar libre.
- **SQL específico de Oracle** en el código (`SYSDATE`, `NUMTODSINTERVAL`): no migres a
  `sqlite`/`mysql` sin adaptar esas consultas.
- `SESSION_DRIVER`, `CACHE_STORE` y `QUEUE_CONNECTION` usan la base de datos: las tablas
  `sessions`, `cache` y `jobs` deben existir en Oracle.
- Límite de longitud de identificadores: `ORA_MAX_NAME_LEN = 30`.
- **Distingue cuatro estados distintos al hablar de la API** (o de cualquier módulo), y
  separa siempre lo que se obtiene **por inspección del código** de lo que se obtiene
  **por pruebas de ejecución**:
  1. **Dependencia instalada** *(inspección)*: el paquete `laravel/sanctum` está en
     `composer.json`.
  2. **Archivo existente** *(inspección)*: existe `routes/api.php` en el árbol de
     trabajo.
  3. **Ruta registrada** *(inspección de la configuración + ejecución del listado de
     rutas)*: en la ejecución registrada del 2026-10-21 la ruta `api/user` **no**
     aparecía en el listado de 71 rutas, y en `bootstrap/app.php` faltaba el
     parámetro `api:` en `withRouting()`. El archivo figuraba **sin seguimiento en Git**
     y **no se encontró en el historial ni en las ramas inspeccionados** (búsqueda no
     exhaustiva).
  4. **API funcional y probada** *(ejecución)*: **no verificado**. No se ejecutó ninguna
     petición autenticada contra un endpoint de API y no existe test que lo cubra. Por
     inspección del código, además, `app/Models/User.php` **no utiliza el trait
     `HasApiTokens`**, que el guard de Sanctum exige en `supportsTokens()`; con la
     configuración inspeccionada, `auth:sanctum` **no podría autenticar por token**.
  → **No afirmes que la API funciona** por el hecho de que el paquete esté instalado o
    el archivo exista. Y **tampoco afirmes lo contrario**: la ausencia de registro
    **no prueba** que la API nunca existió ni que su estado sea un descuido. Es una
    **decisión de producto pendiente** de confirmar con el equipo.

## 8. Integridad de las reservas (regla crítica)

Cualquier código que reserve, libere o reprograme una disponibilidad **debe** replicar el
patrón ya implementado en `Paciente\CitaController::store()` y `::update()`:

1. Abrir una transacción (`DB::transaction`).
2. Seleccionar la fila de `agenda` con `->lockForUpdate()` y validar, en la misma
   consulta: profesional habilitado para el servicio (`perfilesServicio.estadoperfil =
   'Activo'`), profesional/servicio activos, y fecha/hora no vencida.
3. **Revalidar la ocupación dentro de la transacción** (`Cita::where('idhorariodispo', …)
   ->where('estadocita', '!=', 'Cancelada')->exists()`), excluyendo la cita propia al
   reprogramar.
4. Crear/actualizar la cita con `estadocita = 'Pendiente'` y `fechacita` derivada de
   `agenda.fecha` + la hora leída de `horainicio`.
5. Solo un `Cancelada` libera el horario. La cancelación es un **cambio de estado**, no
   un borrado.

El bloqueo pesimista (`lockForUpdate`) es lo que evita la doble reserva: no lo
replaces por una comprobación previa fuera de la transacción.

## 9. Verificación de cambios

- **Antes de dar por terminado**: `vendor/bin/pint` + `php artisan test`.
- **No ejecutes migraciones destructivas ni `php artisan migrate --force` por inercia.**
  Antes de cualquier `migrate` comprueba: (a) a qué conexión apunta `DB_CONNECTION`
  (Oracle real, no el `sqlite` de pruebas), (b) si la base es la de desarrollo o una con
  datos reales, (c) el alcance de la migración. `--force` desactiva la confirmación
  interactiva: úsalo solo con esa comprobación hecha y con copia previa. **No alteres
  datos ni ejecutes migraciones sin autorización explícita.**
- **Suite de tests**: antes de usarla como criterio de aceptación, confirma que está en
  verde. En la última revisión no lo estaba (ver `docs/diagnostico-tecnico.md`): si un
  test falla, distingue si es **preexistente** o lo introdujo tu cambio, y dilo
  explícitamente en tu informe.
- **Diagnóstico de errores**: `storage/logs/laravel.log` es la fuente principal.
  `php artisan about` y `php artisan about --only=drivers` funcionan sin base de datos.
- **Enumerar rutas**: `php artisan route:list` **falla en este proyecto cuando Oracle no
  está disponible**, porque `SESSION_DRIVER=database` hace que el arranque resuelva el
  driver de sesión (y con él la conexión `oracle`) antes de listar rutas. Es un
  comportamiento de **esta** configuración, no una regla general de Laravel. Mientras
  Oracle no esté disponible:
  - inspecciona las rutas **leyendo `routes/web.php` y `routes/auth.php` directamente**;
  - o ejecuta el comando con una **variable de entorno temporal**, sin tocar `.env` ni
    `config/`.

  El entorno del proyecto es **Windows PowerShell**, donde la sintaxis
  `VAR=valor comando` de los shells Unix **no es ejecutable**. Usa este bloque, que
  restaura la variable incluso si el comando falla:

  ```powershell
  $old = $env:SESSION_DRIVER
  try {
      $env:SESSION_DRIVER = 'file'
      php artisan route:list
  }
  finally {
      $env:SESSION_DRIVER = $old
  }
  ```

  - **Comprueba antes de concluir que la variable surtió efecto**: si existe
    `bootstrap/cache/config.php` (configuración cacheada), Laravel usa la caché e
    **ignora** la variable temporal, y el comando seguirá fallando. Verifica con
    `Test-Path 'bootstrap/cache/config.php'`. En la revisión del 2026-10-21 **no
    había caché de configuración**, por lo que la variable sí surtió efecto
    (listado de 71 rutas). Si hubiera caché, habría que ejecutar
    `php artisan config:clear` (limpia caché; no altera código ni datos, pero sí
    cambia el estado del repositorio).
  - recuerda que el **registro efectivo** de las rutas solo queda verificado si el
    comando llegó a ejecutarse.
- **Ámbito de un cambio**: revisa el flujo completo ruta → middleware → controlador →
  modelo → vista antes y después de tocarlo.

## 10. Extensibilidad: orientación (no normativa)

Esta sección es **orientativa y no normativa**. SIGAC podría evolucionar hacia una
plataforma más amplia; las siguientes son **direcciones de diseño a considerar**, **no
funcionalidades aprobadas, no requisitos actuales y nada que debas implementar** sin que
se pidan explícitamente. En particular, la posibilidad de una API REST para clientes
móviles (por ejemplo Flutter), una integración con un sistema de facturación externo o
un chatbot de asistencia **no son requisitos aprobados** y **no autorizan ninguna
implementación por sí solas**.

- **Principio rector: no acoples el agendamiento a las integraciones futuras.** El
  núcleo del dominio (agenda, disponibilidad y reservas) debe seguir siendo usable y
  coherente aunque una integración externa no esté disponible o falle. Nada de lo que
  se añada para esas integraciones debe cambiar el comportamiento actual de las citas.
- **Facturación mediante API**: SIGAC **no implementa facturación** ni es responsable de
  procesos contables. Si algún día se integra con un sistema de facturación externo,
  aísla el cliente en un componente propio con un contrato claro, y define qué ocurre
  cuando el servicio externo no responde (degradación explícita, sin dejar reservas en
  estado inconsistente).
- **API para clientes móviles y otros consumidores**: si se decide exponer una API
  REST, la base actual **no está preparada** (ver `docs/diagnostico-tecnico.md` §2.10).
  Cuando se aborde, verifica explícitamente: autenticación por token, **autorización
  por rol y por propiedad del registro** (§5, no solo el rol), validación, formato
  **consistente** de respuestas (en la práctica: recursos o una capa de serialización
  estable, y versiónado de la API), y **protección de datos personales** (no exponer
  documentos, teléfonos o correos sin enmascarar, y registrar accesos).
- **Chatbot de asistencia**: si algún día se incorpora, la lógica conversacional debe
  **invocar los mismos casos de uso** del sistema, nunca duplicar reglas de negocio ni
  eludir los permisos existentes: el chatbot **no puede saltarse** el middleware `role`
  ni la verificación de propiedad del registro. No asumas proveedor, modelo de IA ni
  tecnología concreta.
- **No sobrearquitectes.** No introduzcas microservicios, capas adicionales ni patrones
  complejos sin una necesidad demostrada. Prioriza un diseño **modular, mantenible y
  compatible con la arquitectura actual** (Laravel monolítico + Livewire + Oracle).
