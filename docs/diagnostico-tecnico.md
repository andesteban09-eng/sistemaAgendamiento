# Diagnóstico técnico — SIGAC

**Fecha de revisión:** 2026-10-21
**Revisado por:** sesión de análisis asistida por IA (Pi)
**Repositorio:** `sistemaAgendamiento` — rama de trabajo `desarrollo-admin`
(último commit observado: `5b37d63`, 2026-08-28)
**Alcance:** revisión estática del código, ejecución de la suite de tests y de comandos
de diagnóstico en la máquina local.

> **Naturaleza de este documento:** es una **foto puntual** del 2026-10-21. Describe lo
> que se comprobó en esa fecha, separando **hechos observados**, **hipótesis** y
> **pendientes por verificar**. Los datos de ejecuciones anteriores **no implican** que
> el estado siga siendo el mismo hoy. Para las reglas permanentes de trabajo ver
> `AGENTS.md`.

---

## 1. Resumen ejecutivo

- La capa de aplicación está implementada: el CRUD de administración, la agenda del
  auxiliar y el flujo completo de citas del paciente se revisaron línea a línea.
- **La suite de tests no estaba en verde** en la ejecución registrada: 26 tests, 4
  pasan, 2 fallan, 20 dan error. La causa raíz de los 20 errores es una sola y está
  localizada. **Estos resultados corresponden a la ejecución del 2026-10-21 y deben
  re-verificarse antes de confiar en ellos.**
- **No fue posible verificar la base de datos**: los servicios Oracle estaban apagados en
  el entorno de revisión (confirmado por el equipo), lo que produce `ORA-12514` en
  cualquier comando que necesite la conexión.
- **`.env` está versionado en Git**: es el hallazgo de mayor riesgo del repositorio.
- Hay funcionalidades y pantallas que el `README.md` presenta como implementadas pero
  que en el código no existen o están vacías.
- La **base actual no está preparada para exponer una API** (§2.10). Es un hecho
  verificado, no un plan de trabajo: la decisión de construirla es de producto.

---

## 2. Hechos observados

### 2.1 Entorno local (observado el 2026-10-21)

| Elemento | Valor observado |
| --- | --- |
| PHP | 8.5.5 (ZTS, Laragon, `C:\laragon\bin\php\php-8.5.5-Win32-vs17-x64`) |
| Composer | 2.10.1 |
| Node.js | v24.14.0 |
| npm | 11.9.0 |
| Laravel | 13.25.0 (`php artisan --version`) |
| `APP_ENV` / `APP_DEBUG` | `local` / `false` |
| Conexión efectiva | `Database .. oracle` (`php artisan about --only=drivers`) |
| Sesión / caché / cola | `database` / `database` / `database` |
| Zona horaria / locale de la app | `UTC` / `en` |
| Extensión PHP | `oci8` cargada (`oci_connect` disponible) |
| Shell del proyecto | Windows PowerShell (`C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe`); `pwsh` no disponible |

### 2.2 Suite de tests — ejecución registrada el 2026-10-21

Comando: `php artisan test` (Pest 5.1.1). **Estos tests corren contra SQLite en
memoria** (`phpunit.xml` fija `DB_CONNECTION=sqlite` y `DB_DATABASE=:memory:`), por lo
que **son independientes del estado de Oracle**.

```
tests 26 | passed 4 | failed 2 | errors 20
```

**Errores (20) — causa raíz única y comprobada.** `database/factories/UserFactory.php`
no define `last_name`, pero la migración `0001_01_01_000000_create_users_table.php`
declara `last_name` como `string` sin `nullable` (es decir, `NOT NULL`). Todos los tests
que crean usuarios fallan con:

```
SQLSTATE[23000]: Integrity constraint violation: 19 NOT NULL constraint failed: users.last_name
```

Afecta a `AuthenticationTest`, `EmailVerificationTest`, `PasswordConfirmationTest`,
`PasswordResetTest`, `PasswordUpdateTest` y `ProfileTest`.

**Fallos (2)**

1. `PasswordResetTest::reset password link screen can be rendered` — la respuesta es un
   500 y el test no encuentra el componente Volt `pages.auth.forgot-password`.
   Causa registrada en `storage/logs/laravel.log` (2026-10-21 18:17:47):
   `Vite manifest not found at: ...\public\build/manifest.json`, lanzada al renderizar
   `resources/views/layouts/guest.blade.php`, que contiene `@vite([...])`.
   El directorio `public/build/` no existe en el repositorio (está en `.gitignore`).
   → El test fallaría también con la factory corregida mientras no exista el manifest.
2. `RegistrationTest::new users can register` — `Component did not perform a redirect`.

**Tests que pasaban** (4): los que no dependen de la factory ni del manifest, entre
ellos el renderizado del login, la autenticación con credenciales inválidas sobre un
usuario existente y el test de ejemplo de `tests/Unit`.

> Los conteos y mensajes anteriores corresponden a la ejecución registrada. Vuelve a
> ejecutar `php artisan test` para conocer el estado actual.

### 2.3 Conexión con Oracle: indisponibilidad confirmada en el entorno

- **Los servicios Oracle estaban apagados** en el entorno de revisión (confirmado por
  el equipo). En consecuencia, `php artisan db:show` falla con
  `ORA-12514: TNS:el listener no conoce actualmente el servicio solicitado en el
  descriptor de conexión`, con DSN
  `(HOST = localhost)(PORT = 1521)(SERVICE_NAME = XE)`.
- **Origen de la conexión durante el arranque (comprobado con `php artisan route:list -v`)**:
  la traza muestra que el comando no consulta Oracle por diseño de Laravel, sino que
  resuelve el driver de sesión al construir el contenedor:

  ```
  SessionManager::createDatabaseDriver()      ← SESSION_DRIVER=database
  DatabaseManager->connection("oracle")
  ConnectionFactory->createSingleConnection()
  Oci8ServiceProvider::register() -> OracleConnector::connect()
  ...
  SessionServiceProvider::registerSessionDriver()
  RoutingServiceProvider::registerRedirector() → resolve("redirect")
  RouteListCommand::getRouteInformation()      ← gatherMiddleware() -> getController()
  ```

  Es decir: **la conexión se dispara porque `SESSION_DRIVER=database`**, y no porque
  listar rutas exija la base de datos. Consecuencia práctica: con Oracle apagado,
  cualquier comando de Artisan que resuelva la sesión falla de la misma forma.
- **Contorno verificado**: `SESSION_DRIVER=file php artisan route:list` (o `array`)
  funciona y **no requiere modificar la configuración del proyecto** (es una variable de
  entorno para una sola invocación). Con esa variante se listaron **71 rutas**.
  - En el entorno real del proyecto (**Windows PowerShell**) la forma Unix
    `SESSION_DRIVER=file php artisan route:list` **no es ejecutable**; la variante
    válida es:

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

    Verificada en esta revisión: devuelve el mismo listado de 71 rutas.
  - **Comprobación necesaria antes de concluir que la variable surtió efecto**: si
    existe `bootstrap/cache/config.php`, Laravel usa la configuración cacheada e
    **ignora** la variable temporal. En esta revisión **no existía** caché de
    configuración (`bootstrap/cache/` solo contenía `packages.php` y `services.php`),
    por lo que la variable sí surtió efecto.
- **Estado de `routes/api.php` en la ejecución registrada del 2026-10-21** — cuatro
  dimensiones que conviene no confundir, separando lo observado **por inspección del
  código** de lo observado **por ejecución**:

  | Dimensión | Cómo se obtuvo | Estado observado |
  | --- | --- | --- |
  | **1. Archivo existente** | Inspección del árbol de trabajo | **Sí**: `routes/api.php` está presente. |
  | **2. Seguimiento en Git** | Inspección del historial | **Sin seguimiento**: figura como `??` en `git status`. `git log --all --diff-filter=A -- routes/api.php` no devolvió resultados y `git ls-tree` en `main`, `desarrollo-admin`, `desarrollo-profesional`, `fusion-desarrollo` y `paciente` dio 0 coincidencias → **no se encontró en el historial ni en las ramas inspeccionados**. La búsqueda no es exhaustiva (no cubre *reflog*, objetos sueltos, *forks* remotos ni repositorios externos), por lo que no se afirma que el archivo nunca haya existido en otra copia. Lo mismo se observó para `config/sanctum.php` y la migración `create_personal_access_tokens_table`. |
  | **3. Rutas efectivamente registradas** | Inspección de la configuración + ejecución del listado de rutas | **No, según la configuración examinada**: en el listado de 71 rutas **no aparece** `api/user`, ni ninguna ruta con prefijo `api` (la única de Sanctum es `sanctum/csrf-cookie`). `grep` no encuentra ninguna referencia a `routes/api.php` en `app/`, `bootstrap/`, `config/` o `routes/`. En `bootstrap/app.php`, `withRouting()` declara `web:`, `commands:` y `health:` pero **no** el parámetro `api:`; en Laravel 13 ese parámetro es el que registra el archivo con prefijo `api`. |
  | **4. API funcional y probada** | Pruebas de ejecución | **No verificado**: **no se ejecutó ninguna petición autenticada** contra un endpoint de API y no existe test que lo cubra. Por inspección del código, `app/Models/User.php` **no utiliza el trait `HasApiTokens`**, que el guard de Sanctum exige en `supportsTokens()`; con la configuración examinada, `auth:sanctum` no podría autenticar por token. |

  → Lo único probado por ejecución es el punto 3: en esa ejecución el archivo **no
  estaba registrado**. Eso **no permite concluir** que la API nunca existió ni que haya
  funcionado, y **tampoco** que la ausencia de registro sea un descuido: es una
  **decisión de producto pendiente** de confirmar con el equipo. Lo que sí puede
  afirmarse es que el paquete `laravel/sanctum` instalado, el archivo presente y la
  migración creada **no equivalen a una API operativa**.
- **Rutas registradas observadas** (con `SESSION_DRIVER=file`, 71 en total):
  `admin` 36 · `Paciente` 6 · `auxiliar` 6 · `livewire` 6 · `paciente` 2 ·
  `storage` 2 · `verify-email` 2 · y 15 rutas individuales (`/`, `dashboard`, `login`,
  `logout`, `register`, `forgot-password`, `reset-password/{token}`,
  `confirm-password`, `profile`, `up`, `sanctum/csrf-cookie` y las de
  Livewire/archivos).
- **Aclaración**: el fallo observado es de **conexión**, no de rutas ausentes ni mal
  registradas. Las rutas definidas en `routes/web.php` y `routes/auth.php` existen en el
  código y se registran con normalidad cuando la aplicación puede arrancar.
- **No fue posible comprobar** (requiere Oracle operativo): si el esquema de
  `database/sistemaagendamiento.sql` está aplicado, qué datos existen, si el usuario
  Oracle configurado tiene los permisos esperados, ni el estado real de la columna
  `users.estado`.

### 2.4 `.env` versionado en Git

- `git ls-files` incluye `.env`.
- `git log --oneline -- .env` muestra: `2f53329 guardar .env en esta rama`,
  `9456469 agregar .env`, `7acf183 Delete .env`, `99bce73 config .env`.
  La entrada en `.gitignore` se añadió después y **no eliminó** el archivo del índice.
- **No se copiaron ni reproducen aquí los valores del archivo.** La revisión se limitó a
  nombres de variables.
- `git status` muestra `.env` como modificado respecto al último commit.
- En la última revisión el riesgo seguía vigente; **vuelve a comprobarlo** (`git
  ls-files | grep -E '^\.env$'`) antes de darlo por resuelto.
- **Secretos expuestos y su tratamiento**:
  - **Credenciales de la conexión Oracle y contraseñas de cuentas de servicio**: su
    rotación es la medida habitual, pero debe **planificarse** y ejecutarse de forma
    **coordinada con sus consumidores** (`.env`, `config/`, cadena de conexión,
    cuentas y Roles del usuario Oracle, configuraciones de despliegue y cualquier
    servicio que use esas credenciales). Rotar sin actualizar a los consumidores deja
    el sistema roto.
  - **`APP_KEY` requiere un tratamiento distinto**: es la clave simétrica de cifrado
    (`config/app.php`, `AES-256-CBC`). Rotarla invalida cookies de sesión cifradas,
    valores cifrados con `Crypt`, remember-me, colas cifradas y cualquier dato cifrado
    almacenado en Oracle. En la revisión del 2026-10-21 **no se encontraron usos de
    `Crypt::` en `app/`** y `SESSION_ENCRYPT` no estaba activo, lo que **reduce** el
    riesgo pero **no lo descarta**: pueden existir datos cifrados en la base de datos, en
    la configuración, en colas o en componentes externos. Antes de proponer la rotación
    hay que verificar el estado real de `SESSION_ENCRYPT` y el contenido de la base.
- En la última revisión el riesgo seguía vigente; **vuelve a comprobarlo** antes de
  darlo por resuelto.

### 2.5 Marca del proyecto

- `README.md` (título y 11 apariciones más) y el DDL (`database/sistemaagendamiento.sql`,
  cabecera) usan la sigla **SIGAP**.
- La marca vigente es **SIGAC** (indicada por el equipo). No hay ninguna aparición de
  "SIGAC" en el código, en el historial de Git ni en las vistas.
- Las vistas y el login muestran la marca comercial **"Laboratorios Carvajal"** (textos,
  logo `public/img/LOGO-CARVAJAL-LABORATORIOS-IPS-3-1.webp`, teléfono PBX en
  `resources/views/partials/menu.blade.php`).
  → **Pendiente de decidir**: si la interfaz debe mostrar SIGAC, "Laboratorios Carvajal",
  o ambos. Es una decisión de producto, no técnica.

### 2.6 Funcionalidades verificadas como implementadas

- **Autenticación**: login/logout (Volt + `LoginForm`), verificación de correo
  (`Auth\VerifyEmailController`), reset y confirmación de contraseña. Rate limiting de
  5 intentos.
- **Administración**: CRUD de pacientes, profesionales, auxiliares, tipos de servicio y
  servicios; asignación de servicios por profesional; activación/desactivación.
- **Auxiliar**: CRUD de disponibilidades (`agenda`), con validación de profesional y sede
  activos, profesional con al menos un servicio activo, y no duplicidad de
  profesional + fecha + hora.
- **Paciente**: listado, detalle, creación, reprogramación y cancelación de citas, con
  validación de disponibilidad y asignación automática de profesional y sede desde la
  agenda.
- **Dashboards**: existe una vista por rol. El del paciente y el del profesional calculan
  datos desde la base de datos.

### 2.7 Discrepancias entre documentación y código

| Afirmación en `README.md` | Estado real comprobado |
| --- | --- |
| "Gestión de citas desde el rol auxiliar" (en desarrollo) | **Coincide**: no hay rutas ni controlador. |
| "Gestión de citas desde el rol profesional" (en desarrollo) | **Coincide**: no hay rutas ni controlador. |
| "Gestión de órdenes de laboratorio" | Las tablas `ordenlaboratorio` y `ordenlaboratorioservicio` existen en el DDL, pero **no hay modelos, controladores ni consultas** que las usen. |
| "Documentación funcional y técnica" pendiente | No existe directorio `docs/` en el repositorio. |
| "Pruebas integrales del sistema" pendiente | Coincide: la suite no estaba en verde (§2.2). |

### 2.8 Pantallas con datos hardcodeados o sin acción

- `resources/views/admin/dashboard-admin.blade.php`: los 4 contadores de tarjetas están
  en `0` literal.
- `resources/views/auxiliar/dashboard-auxiliar.blade.php`: los contadores están en `0`
  literal.
- `resources/views/profesional/dashboardProfesional.blade.php`: los botones "Editar cita"
  y "Cancelar cita" **fueron diseñados pero nunca cableados** (no hay ninguna acción
  asociada).
- El dashboard del profesional **sí** arma el calendario desde `Cita` (colores por
  estado: `Pendiente`, `Realizada`/`Atendida`, `Cancelada`).
  → Nota: `Realizada` y `Atendida` aparecen en el `match` de color, pero el `CHECK` de
  `cita.estadocita` solo admite `Pendiente`, `Realizada` y `Cancelada`. `Atendida` no
  existe en el dominio: **valor huérfano**.

### 2.9 Otras verificaciones menores

- `resources/views/layouts/app.blade.php` y `layouts/dashboard.blade.php` **no** usan
  `@vite`; cargan Bootstrap/FullCalendar por CDN y el CSS propio desde `public/css/`.
  `layouts/guest.blade.php` sí usa `@vite` (y es el que provoca el 500 del §2.2).
- `tailwind.config.js` y `postcss.config.js` existen, pero `resources/css/app.css` solo
  contiene las tres directivas `@tailwind` y **ninguna vista de la aplicación** usa
  utilidades Tailwind: Tailwind está instalado pero prácticamente inactivo.
- `public/build/` no existe (ignorado por Git): un `npm run build` es necesario para
  las vistas que usan `@vite`.
- No existe `.github/` → **no hay CI, ni lint automático, ni cobertura**.
- `composer.json` declara `laravel/pao` 1.1.4: su `ServiceProvider` detecta si el proceso
  es un agente y reformatea la salida de los comandos (sin ANSI) para consumo de IA.
- `app/Models/User.php` tiene comentado `MustVerifyEmail`, pero `routes/web.php` aplica
  el middleware `verified`. El middleware `EnsureEmailIsVerified` de Laravel solo exige
  verificación si el modelo implementa `MustVerifyEmail`, por lo que **en el estado
  observado la verificación de correo no bloquea el acceso** (la columna
  `email_verified_at` existe y `update-profile-information-form.blade.php` la pone a
  `null` al cambiar el correo, sin reenviar verificación).
- `app/Providers/AppServiceProvider.php` fuerza `URL::forceRootUrl(config('app.url'))` y
  deriva de `APP_URL` el path base de las rutas de Livewire (`/livewire/livewire.js`,
  `/livewire/update`). El `APP_URL` vigente apunta a un túnel `*.trycloudflare.com`:
  en otro host hay que revisar `APP_URL` y el subdirectorio de instalación.
- **Riesgo de `NULL` en Oracle no cubierto por la validación**: `tiposervicio.descripcion`
  es `clob NOT NULL` en el DDL, pero `Admin\TipoServicioController` la valida como
  `nullable|string`; crear un tipo de servicio sin descripción puede fallar con
  `ORA-01400` (falta la columna obligatoria). El campo `servicio.prerequisitos` sí es
  nullable en el DDL.
- `storage/logs/laravel.log` acumula errores de conexión Oracle de forma histórica
  (`ORA-28000` cuenta bloqueada, `ORA-12514` servicio desconocido, `ORA-12541` sin
  listener, `ORA-01017` credenciales). Son registros **acumulados en el tiempo**: no
  describen por sí solos el estado actual y no deben interpretarse como diagnóstico
  vigente sin una ejecución reciente.

### 2.10 Estado de la base para una futura API (verificado el 2026-10-21)

Hechos comprobados, **sin proponer ni ejecutar ninguna implementación**. Cada punto
indica si proviene de **inspección del código** o de **pruebas de ejecución**:

- **`app/Models/User.php` no usa el trait `HasApiTokens`** de Sanctum *(inspección)*. El
  guard de Sanctum (`vendor/laravel/sanctum/src/Guard.php`, método `supportsTokens()`)
  exige ese trait en el modelo tokenizable: **sin él, `auth:sanctum` no puede autenticar
  por token**, ni siquiera si la ruta se registrara.
- **No existe `app/Http/Resources`** *(inspección)*: no hay capa de serialización de
  respuestas; los controladores devuelven datos planos o `redirect()`.
- **No existe `app/Http/Requests`** *(inspección)*: la validación vive dentro de los
  controladores, lo que dificulta reutilizarla entre una interfaz web y un endpoint.
- **No existe `config/cors.php`** en el proyecto *(inspección)*; se aplica la
  configuración por defecto del framework (`paths: api/*, sanctum/csrf-cookie`,
  `allowed_origins: *`, `supports_credentials: false`). `HandleCors` sí está en el
  middleware global, pero **con `allowed_origins: *` no es una política adecuada para
  exponer datos personales** a clientes externos.
- **No hay versionado de API** (`/v1/…`) ni estructura de rutas `api` registrada
  *(inspección de `bootstrap/app.php` y del listado de rutas)*.
- **No hay throttling de API configurado** *(inspección)*: en `bootstrap/app.php` no se
  invoca `throttleApi()`; el grupo `api` de Laravel quedaría sin limitador.
- **`bootstrap/app.php` sí tiene `shouldRenderJsonWhen()`** para `api/*` y
  `expectsJson()` *(inspección)*: es un ajuste útil para la futura API.
- **`bootstrap/app.php` configura `trustProxies(at: '*')`** *(inspección)*: es una
  configuración **existente que requiere evaluación según el entorno de despliegue**,
  no una seguridad resuelta. Confiar en todos los proxies hace que la aplicación acepte
  como confiables las cabeceras `X-Forwarded-*` de cualquier origen, lo que solo es
  adecuado detrás de un proxy inverso conocido. No se pudo verificar cuántos niveles de
  proxy hay en el destino real, de modo que **no puede calificarse ni de riesgo ni de
  ventaja**.
- **No existe ningún test** que cubra endpoints de API, ni `tests/Feature/Api/`
  *(inspección)*.
- **No se ejecutó ninguna petición autenticada** contra un endpoint de API
  *(pruebas de ejecución)*: la API **no fue probada** y, con la configuración
  inspeccionada, no puede considerarse funcional.

**Conclusión de estado (no es un plan):** la aplicación **no está preparada** para
exponer una API a clientes externos en su estado actual. Convertirla requiere decisiones
de producto y de seguridad explícitas (autenticación por token, autorización por rol y
propiedad del registro, formato consistente de respuestas, versionado, CORS y protección
de datos personales). La orientación de diseño correspondiente está en `AGENTS.md` §10.

### 2.11 Orientación de extensibilidad (referencia, no alcance aprobado)

| Línea futura considerada | Estado comprobado hoy | Observación de diseño |
| --- | --- | --- |
| Integración con un sistema de facturación mediante API | **No existe** ninguna integración, cliente HTTP ni configuración de servicios externos relacionados. SIGAC no implementa facturación. | El núcleo de reservas debe permanecer usable si la integración no está o falla; aislar el cliente en un componente propio. |
| API REST para clientes móviles (por ejemplo Flutter) u otros consumidores | **No operativa y no probada**: ver §2.10 (ruta no registrada según la configuración examinada, `HasApiTokens` ausente, sin resources, sin versionado, sin CORS propio, sin throttling, sin tests). | Requiere decisiones explícitas de seguridad y un contrato de respuestas estable antes de exponer datos personales. |
| Chatbot de asistencia | **No existe** ninguna integración conversacional, ni dependencia de IA, ni cola o worker dedicado más allá de la cola por defecto de Laravel. | Debe invocar los casos de uso existentes, nunca duplicar reglas ni eludir el middleware `role` y la verificación de propiedad del registro. |

Estas tres líneas son **direcciones de evolución**, no funcionalidades aprobadas ni
requisitos: **ninguna autoriza una implementación por sí sola** y ninguna debe
implementarse sin petición explícita. Tampoco justifican introducir microservicios,
capas nuevas o patrones complejos: el criterio es un diseño **modular, mantenible y
compatible con la arquitectura actual**.

---

## 3. Hipótesis (no verificadas)

| # | Hipótesis | Cómo comprobarla |
| --- | --- | --- |
| H1 | El test de registro falla porque no envía `last_name` y la validación lo rechaza antes del redirect | Reproducir con `Volt::test('pages.auth.register')->set('last_name', '…')` |
| H2 | `users.estado` en la base real contiene `'activo'` (minúscula, como escribe la app) o `'Activo'` (como el DDL y la migración) | `select distinct estado from users` en la instancia Oracle |
| H3 | El DDL de `sistemaagendamiento.sql` está realmente aplicado y coincide con la base | `php artisan db:show` + comparar con `user_constraints` / `user_tab_columns` |
| H4 | El fallo de `pages.auth.forgot-password` desaparece por completo con `npm run build` | Ejecutar `npm run build` y repetir el test |
| H5 | El servicio Oracle real no es `XE` (podría ser `XEPDB1` u otro), lo que explicaría `ORA-12514` una vez encendidos los servicios | Revisar la configuración del listener en Laragon y el servicio configurado |
| H6 | Los botones "Editar cita" / "Cancelar cita" del dashboard profesional se pensaron para cablear con rutas de gestión de citas que nunca se crearon | Confirmar con el equipo; en el estado observado no hay ninguna ruta que los respalde |
| H7 | El enrutado de la API es una decisión de producto pendiente, no un descuido | Confirmar con el equipo; el estado observado es que la ruta no se registra, y no hay evidencia en el repositorio de que la API llegara a estar operativa |
| H8 | La cuenta Oracle bloqueada (`ORA-28000`) es un residuo de intentos fallidos previos | Revisar el estado de la cuenta desde una sesión con permisos |
| H9 | El proyecto se despliega bajo un subdirectorio (Apache) y de ahí el `APP_URL` con path | Revisar la configuración del servidor web de destino |
| H10 | `Atendida` es un estado previsto para `cita.estadocita` que aún no está en el `CHECK` de Oracle | Revisar el modelo de dominio con el equipo |
| H11 | La verificación de correo debe ser obligatoria (por eso `MustVerifyEmail` está comentado) | Confirmar con el equipo; en el estado observado no bloquea el acceso |

---

## 4. Pendientes de verificación

1. **Decisión de producto sobre la API y, si procede, su registro efectivo.** En la
   ejecución registrada la ruta no aparecía en el listado y `routes/api.php` no se
   encontró en el historial ni en las ramas inspeccionados. Debe confirmarse si la API
   forma parte del alcance: solo entonces corresponde registrar el archivo en
   `withRouting()` y verificar un endpoint con `auth:sanctum`. **Pendiente, no
   registrado como error ni como descuido.**
2. **Que el servicio Oracle sea el esperado** (H5) y que la cuenta configurada esté
   desbloqueada (H8).
3. **Aplicación real del esquema** y contenido real de las tablas (H2, H3).
4. **Estado actual de la suite de tests**: re-ejecutar `php artisan test` (los números
   de §2.2 son de la ejecución del 2026-10-21).
5. **Riesgo de `.env` versionado**: re-verificar con `git ls-files` y, si sigue
   vigente, decidir exclusión del historial y **planificar la rotación de los secretos
   expuestos** (credenciales Oracle y contraseñas), evaluando **por separado** el caso de
   `APP_KEY` por su dependencia con el cifrado (§2.4).
6. **Marca visible en la interfaz** (SIGAC / "Laboratorios Carvajal" / ambas) (§2.5).
7. **Alcance de la gestión de citas** para auxiliar y profesional: definir rutas,
   autorización por recurso y estados válidos (§2.7, §2.8).
8. **`tiposervicio.descripcion`**: decidir si la validación pasa a `required` o el DDL
   cambia a nullable (§2.9).
9. **`Atendida`**: decidir si se añade al `CHECK` de Oracle o se elimina del `match` de
   colores (§2.8).
10. **Preparación para una eventual API**, si se decide abordarla (§2.10): trait
    `HasApiTokens` en el modelo de usuario, capa de serialización de respuestas,
    reutilización de validaciones, CORS propio, versionado, throttling y tests.
    **No implementar sin decisión de producto y autorización explícita.**

---

## 5. Limitaciones de esta revisión

- **No se accedió a la base de datos.** Los servicios Oracle estaban apagados en el
  entorno; ninguna comprobación sobre datos reales fue posible.
- **No se ejecutó `npm run build`**, por lo que el manifest de Vite no existe y las
  vistas con `@vite` fallan.
- **No se ejecutó `php artisan dev` ni `laravel/pao`**: requieren TTY/multiplex.
- **No se ejecutaron migraciones** contra ningún entorno (no había autorización ni
  conexión).
- **No se modificó ningún archivo de código, ruta, modelo, controlador, migración,
  configuración, dependencia o `.env`** durante esta revisión. Los únicos archivos
  creados fueron `AGENTS.md` y `docs/diagnostico-tecnico.md`.
- **Sí se ejecutó `php artisan config:clear` en una revisión previa**: elimina la
  caché de configuración de la aplicación (archivo `bootstrap/cache/config.php`). En el
  estado observado, ese archivo **no existía** antes del comando, por lo que el
  comando no llegó a eliminar configuración cacheada alguna. **No se reportaron cambios
  de código ni de datos de la base de datos atribuibles a ese comando**, y no se
  ejecutó ninguna migración ni se alteró dato alguno de Oracle.
- Se ejecutó `php artisan route:list` en variantes de solo lectura y se comprobó
  `bootstrap/cache/`; ninguna de esas acciones modificó el repositorio.
- No se inspeccionaron en profundidad las vistas de `resources/views/livewire/profile/*`
  ni `welcome/*`, ni la documentación funcional de negocio.

---

## 6. Registro de cambios de este documento

| Fecha | Cambio |
| --- | --- |
| 2026-10-21 | Creación inicial a partir de la revisión del repositorio `sistemaAgendamiento` en la rama `desarrollo-admin`. Identificado el origen de la conexión Oracle durante el arranque (`SESSION_DRIVER=database`) y verificado el listado de 71 rutas con variable de entorno temporal. Confirmado que `routes/api.php` no está registrado según la configuración examinada y que no se encontró en el historial ni en las ramas inspeccionados. Separado el caso de `APP_KEY` del resto de secretos. Corregidas erratas de texto y alineada la numeración de secciones (10 en `AGENTS.md`, 6 en este documento). Añadido §2.10 (estado comprobado de la base para una futura API) y §2.11 (orientación de extensibilidad, sin alcance aprobado). Ajustes finales: §1 y §10 de `AGENTS.md` separados en normativas (1–9) y orientativa (10); `trustProxies(at: '*')` redescrito como configuración a evaluar; hallazgos de la API separados entre inspección y ejecución; imprecisión del historial de Git atenuada; precisión sobre `php artisan config:clear`. |
