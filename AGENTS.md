AGENTS.md — SIGAC
Producto: SIGAC — sistema web independiente de gestión y agendamiento de citas médicas.
Tipo de documento: instrucciones permanentes para agentes de IA y colaboradores del repositorio.
Última revisión documental: 2026-11-19.
Repositorio: sistemaAgendamiento.
Este documento prescribe cómo trabajar en SIGAC. No constituye una auditoría del estado actual del código. Las observaciones técnicas históricas, con sus fechas y limitaciones, están en docs/diagnostico-tecnico.md. Antes de modificar un archivo, examina su versión existente y confirma las rutas, dependencias y configuraciones que realmente están en uso.

- Secciones 1 a 9: normativas. Se aplican a cualquier modificación del proyecto.
- Sección 10: orientativa. Expone posibilidades de evolución que no son requisitos aprobados. El apartado final «Requisito de proceso (normativo)» es una excepción: sus condiciones son obligatorias si se propone desarrollar el asistente conversacional.
- Prioridad de las decisiones: las instrucciones expresas del responsable del proyecto pueden actualizar el alcance, pero un agente no debe inferir autorizaciones de documentos de intención, comentarios o ejemplos. Señala los conflictos antes de cambiar reglas de negocio o configuraciones sensibles.
1. Propósito, alcance e identidad institucional
SIGAC es un producto de software independiente, orientado a la gestión y al agendamiento de citas médicas en el sector salud. Centraliza usuarios, roles, pacientes, profesionales de la salud, auxiliares, administradores, sedes, tipos de servicio, servicios, agendas, disponibilidades y citas.
- Origen académico: proyecto formativo del programa Tecnólogo en Análisis y Desarrollo de Software (SENA).
- Alcance funcional vigente: gestión de usuarios y agendamiento de citas médicas. No incorpora un ERP hospitalario completo ni módulos propios de facturación, contabilidad, nómina o inventario. No implementes funcionalidades fuera de alcance sin petición y autorización explícitas.
- Roles existentes: administrador, auxiliar, profesional, paciente (columna users.rol).
- Marca del producto: SIGAC. El nombre y la identidad visual del software no deben presentarlo como perteneciente, patrocinado, autorizado o representativo de una IPS, clínica, laboratorio u otra organización particular.
- Neutralidad institucional obligatoria: en nuevas vistas, documentación vigente, pruebas, datos ficticios, correos, plantillas, reportes, metadatos, imágenes y mensajes de interfaz, emplea la identidad SIGAC y una terminología genérica del sector salud. No reutilices nombres, teléfonos, direcciones, dominios, correos, logotipos ni otras señas de identidad de organizaciones que hayan servido como antecedentes del proyecto.
- Datos institucionales configurables: es válido hablar conceptualmente de una institución usuaria o de una sede, pero eso no significa que el producto sea multiinstitución, multitenant o permita personalizar la marca por cliente. No afirmes ni implementes esas capacidades sin especificación, diseño, autorización y pruebas.
- Referencias heredadas: pueden existir siglas, rótulos, nombres de archivos o datos de prueba de versiones anteriores (por ejemplo, SIGAP en documentación y DDL histórico). Identifícalos y propón su tratamiento contextual. Corrige la identidad visible y vigente cuando se autorice, pero no renombres automáticamente tablas, clases, rutas, columnas, archivos SQL ni el historial de Git: esos cambios requieren análisis de impacto y un cambio verificable separado.
- Documentación histórica: respeta el carácter fechado de docs/diagnostico-tecnico.md. No reescribas los resultados de una auditoría pasada como si se hubieran obtenido hoy. Las decisiones nuevas se registran en una actualización explícita, diferenciada de los hallazgos originales.
2. Stack y convenciones de interfaz
Versiones registradas en la revisión original del repositorio (2026-10-21). Son referencias, no una verificación de las versiones instaladas actualmente. Confirma composer.lock, package-lock.json y el entorno antes de recomendar actualizaciones o usar APIs específicas.
Capa	Tecnología	Versión registrada
Runtime	PHP	^8.3 (restricción declarada)
Framework	Laravel Framework	13.25.0
Interactividad	Livewire	3.8.5
Componentes	Livewire Volt	1.11.2
Autenticación futura de API	Laravel Sanctum	4.3.3
Driver de Oracle	yajra/laravel-oci8	13.10.1
Herramientas frontend	Vite	8.2.1
CSS	Tailwind CSS	3.4.19, instalado; uso no confirmado en vistas principales
UI principal	Bootstrap, Bootstrap Icons, FullCalendar	5.3.3 / 1.11.3 / 6.1.15, vía CDN en la revisión
Pruebas	Pest	5.1.1 (pest-plugin-laravel 5.0.1)
Formato PHP	Laravel Pint	1.30.5
Salida asistida para agentes	laravel/pao	1.1.4


- La interfaz principal utiliza Bootstrap 5 y CSS propio en public/css/ según la revisión técnica. Respeta el estilo existente: no introduzcas Tailwind por inercia ni mezcles sistemas de estilos sin comprobar que las utilidades están compiladas y cargadas.
- Los archivos estáticos (CSS, JS, logotipos, favicon e imágenes) deben referenciarse según el directorio público real de Laravel. En Blade, utiliza asset() u otros mecanismos existentes apropiados en vez de codificar un dominio temporal.
- La carga de Livewire, sus rutas dinámicas y los recursos de Vite deben comprobarse en el entorno de ejecución de destino, no solo con php artisan serve.
3. Arquitectura y rutas
Estructura observada en la revisión original, sujeta a comprobación antes de cambios:
app/
├── Http/Controllers/
│   ├── Controller.php
│   ├── dashboardController.php             # dashboard por rol
│   ├── Admin/                              # pacientes, profesionales, auxiliares, servicios
│   ├── Auxiliar/                           # AgendaController
│   ├── Paciente/                           # CitaController
│   ├── Profesional/                        # ProfesionalDashboardController
│   └── Auth/                               # VerifyEmailController
├── Http/Middleware/RoleMiddleware.php     # middleware «role»
├── Models/                                 # modelos Eloquent de negocio
├── Livewire/Forms/LoginForm.php           # autenticación y límites de intentos
├── Livewire/Actions/Logout.php
├── Providers/                              # proveedores de Laravel y Volt
└── View/Components/                        # layouts de aplicación e invitado
routes/web.php                              # rutas web y agrupación por rol
routes/auth.php                             # autenticación y rutas Volt
routes/api.php                              # existencia/registro deben verificarse
resources/views/                            # layouts, vistas por rol, componentes, partials
public/css/                                 # estilos de la aplicación
database/sistemaagendamiento.sql            # DDL histórico de negocio para Oracle
Reglas estructurales:
- En el estado examinado, el profesional accedía mediante /dashboard y el controlador resolvía la vista según users.rol; no había un grupo de rutas role:profesional. Si se añaden endpoints nuevos, comprobar el enrutado real y aplicar protección de rol y recurso.
- Algunos nombres de ruta utilizan Paciente.citas.*, con P mayúscula. No los renombres sin comprobar y actualizar todas sus referencias.
- Los layouts pueden utilizar @section('contenido'); inspecciona el archivo concreto antes de cambiar la estructura de Blade.
- El modelo de negocio de Oracle se documenta en database/sistemaagendamiento.sql; no des por hecho que las migraciones de Laravel reproducen ese esquema.
- En la revisión de 2026-10-21 no existían app/Http/Resources, app/Http/Requests ni config/cors.php. Verifica su existencia actual antes de planear componentes nuevos o afirmar que faltan.
3.1 URLs, Laragon, Livewire y túneles de desarrollo
En pruebas posteriores, durante noviembre de 2026, se detectó que una configuración anterior de AppServiceProvider.php combinaba URL::forceRootUrl() con rutas de Livewire cuyo prefijo se extraía de APP_URL; al usar una URL con /sistemaAgendamiento/public, esto llegó a generar prefijos duplicados. También hubo errores 404 al servir la aplicación bajo una subcarpeta de Apache. Posteriormente se comunicó funcionamiento con php artisan serve y con Apache/Laragon usando una configuración local independiente con public/ como raíz documental.
- No vuelvas a introducir el segmento físico /public como prefijo interno de rutas de Laravel ni antepongas automáticamente el path de APP_URL a Livewire::setScriptRoute() o Livewire::setUpdateRoute().
- Si el servidor utiliza Apache, su DocumentRoot debe apuntar al directorio public/ de la aplicación; comprueba el archivo de configuración realmente cargado, no solo el nombre del dominio o del archivo de virtual host.
- Las URLs públicas y los prefijos internos de las rutas son conceptos distintos. No fuerces un origen global únicamente para hacer funcionar un túnel temporal de Cloudflare.
- Antes de ajustar APP_URL, ASSET_URL, proxies o rutas, determina si la petición llega directamente a Apache, a php artisan serve o mediante un proxy/túnel. Verifica las URLs generadas y la respuesta HTTP de CSS, imágenes, favicon, Livewire JS y livewire/update.
- En la revisión original bootstrap/app.php configuraba trustProxies(at: '*'). No se considera una política de seguridad validada: confirma su valor actual y evalúa el conjunto de proxies confiables y las cabeceras reenviadas según el despliegue antes de modificarlo.
- No conviertas un dominio de trycloudflare.com ni la dirección de un entorno local en constantes del código o documentación normativa. Las direcciones temporales pertenecen a la configuración del entorno.
- No modifiques servidores, bootstrap/app.php, AppServiceProvider.php, .env ni otros parámetros sensibles sin necesidad demostrada, revisión previa y autorización cuando corresponda.
4. Convenciones de código
- Formato: PSR-12, sangría de cuatro espacios, UTF-8, finales de línea LF y salto de línea final conforme a .editorconfig. Usa vendor/bin/pint cuando cambies PHP y sea posible ejecutarlo.
- Idioma: explicaciones, comentarios, mensajes de validación e interfaz en español. Mantén los identificadores técnicos originales de PHP, Laravel, Oracle y las bibliotecas (App\Models\Cita, DB::transaction, NUMTODSINTERVAL, Livewire, Pest, Vite).
- Nomenclatura en la capa de aplicación: utiliza nombres en minúsculas para atributos propios del dominio, claves de peticiones/validaciones, variables de negocio y campos de formularios (idtiposervicio, idservicio, idhorariodispo, numdoc, tipodoc, name, last_name, email). No conviertas nombres convencionales del framework en variantes inventadas: se respetan protected $primaryKey, protected $fillable, protected $casts, protected $table y public $timestamps exactamente como exige Eloquent.
- Columnas Oracle: conserva los nombres físicos y los contratos que ya existan (IDPROFESIONALSALUD, IDSEDE, FECHA, HORAINICIO, CONSULTORIO, IDHORARIODISPO, ESTADOSEDE). Algunas integraciones de Auxiliar/AgendaController y resources/views/auxiliar/agenda/* habían utilizado campos en mayúsculas. No los cambies aisladamente: si se normalizan nombres en Laravel, se debe verificar conjuntamente el formulario, la validación, el controlador, el modelo y las consultas.
- Archivos y clases: el repositorio ha tenido nombres de archivo con casing heterogéneo (cita.php/`Cita`, paciente.php/`Paciente`, profesionalSalud.php/`ProfesionalSalud`, sede.php/`Sede` y modelos PascalCase). Evita cambios masivos que puedan romper autoloading o despliegues con sistemas de archivos sensibles a mayúsculas.
- Modelos de negocio: respeta las declaraciones explícitas de $table, $primaryKey, $fillable, $casts y $timestamps conforme al modelo real. La mayoría de tablas se referencian en minúsculas y algunas (SERVICIO, TIPOSERVICIO) en mayúsculas; en Oracle los identificadores sin comillas no distinguen casing.
- Vistas: usa las clases de Bootstrap y estilos existentes. Escapa la salida de datos con {{ }} salvo que exista una justificación segura para HTML confiable.
- Mensajes: mantén patrones de redirección y validación como ->with('success', '…'), ->with('error', '…') y ->withErrors(['campo' => '…'])->withInput().
- Controladores: conserva el patrón CRUD (index, create, store, show, edit, update, destroy) y las acciones específicas existentes, como toggleEstado. En el estado revisado, pacientes y profesionales tenían baja lógica y auxiliares eliminación física: confirma la regla antes de modificarla.
- Vistas por rol: respeta los directorios existentes (admin/, auxiliar/, Paciente/, profesional/) y verifica el uso de mayúsculas de cada ruta y archivo.
- Cambios acotados: evita refactorizaciones masivas de nombres, rutas, modelos o tablas. Separa cambios de identidad, cambios funcionales y migraciones de datos en unidades revisables.
5. Autorización: rol no equivale a propiedad del registro
- Todo endpoint sensible debe exigir autenticación, autorización por rol y autorización por recurso. El middleware role: (alias en bootstrap/app.php según la revisión) solo comprueba users.rol; no demuestra que el usuario pueda operar sobre un registro concreto.
- Antes de acceder o modificar un recurso verifica: (1) identidad autenticada, (2) rol permitido, (3) propiedad o ámbito de acceso efectivo, (4) estado vigente y reglas del recurso.
- El patrón observado en Paciente/CitaController comparaba cita.idpaciente con el paciente del usuario autenticado y rechazaba la operación con abort(403) cuando no coincidía. Aplica el mismo principio a cada rol y recurso, adaptándolo a las reglas confirmadas del dominio.
- El login comprobaba users.estado = 'activo' y tenía un límite de cinco intentos; RoleMiddleware no comprobaba por sí mismo el estado. No asumas que una cuenta inactiva queda bloqueada en cada endpoint por la validación del login. Verifica middleware y políticas actuales.
- Las contraseñas deben almacenarse como hash, nunca en texto plano. Respeta el mecanismo existente (Hash::make() y/o el cast hashed de Eloquent) evitando dobles transformaciones no verificadas.
- Los formularios de autenticación nunca deben enviar credenciales mediante GET o parámetros de URL. Verifica el método POST, protección CSRF y comportamiento seguro si el JavaScript de Livewire falla. Una URL de login con ?password=... es un defecto de seguridad que debe corregirse; no reproduzcas valores reales en informes o pruebas.
- No incluyas cédulas, teléfonos, direcciones, contraseñas ni otros datos personales reales en código, capturas, pruebas, commits, prompts o documentación.
6. Secretos, privacidad y configuraciones sensibles
- La revisión técnica del 2026-10-21 comprobó que .env estaba versionado en Git (incluido un commit 2f53329). No se ha vuelto a auditar ese riesgo en este documento: verifica si persiste mediante git ls-files --error-unmatch .env sin mostrar el contenido.
- Nunca imprimas, copies ni compartas valores secretos del .env, ni los incluyas en chats, documentos o contexto de agentes. Trabaja con nombres de variables o estados de configuración no sensibles.
- Si se confirma exposición de credenciales, planifica su rotación y actualiza coordinadamente todos los consumidores (configuración, conexiones Oracle, servicios y despliegue); no rotes una credencial aisladamente.
- Que .gitignore incluya .env no elimina un archivo que ya está registrado. Evaluar git rm --cached, limpieza de historial y revocación de secretos requiere una decisión consciente, revisión de impactos y coordinación con el equipo.
- APP_KEY requiere un procedimiento especial. Rotarla puede invalidar cookies, sesiones, valores cifrados con Crypt, datos persistidos y otras dependencias. No la cambies automáticamente; verifica primero los usos de cifrado y prepara un plan de transición.
- No modifiques datos de Oracle ni configuraciones sensibles (.env, config/, .gitignore, credenciales, APP_KEY, orígenes de despliegue) sin autorización explícita y comprobaciones previas.
- Mantén APP_DEBUG=false en entornos accesibles a terceros. Minimiza la exposición de datos personales y de salud en logs, mensajes de error, respuestas y reportes.
- Una marca independiente no autoriza reutilizar bases de datos, datos clínicos, documentos, logotipos o información de una organización externa. Usa información ficticia o datos autorizados y desidentificados en pruebas y demostraciones.
7. Oracle: particularidades del dominio y hechos históricos
El proyecto utiliza Oracle Database mediante yajra/laravel-oci8; verifica config/database.php, config/oracle.php y la instancia efectiva antes de ejecutar comandos. Lo siguiente resume reglas y observaciones documentadas; confirma las diferencias entre DDL y esquema desplegado antes de cambiar datos.
- DDL de negocio: database/sistemaagendamiento.sql. Las migraciones de Laravel cubrían principalmente tablas de framework (users, sessions, cache, jobs, recuperación de contraseñas y tokens). Un cambio del modelo de negocio puede requerir revisar DDL, modelos, consultas y base real; no lo resuelve necesariamente una migración de Laravel.
- Identidades y timestamps: las tablas de negocio utilizan claves generated always as identity y generalmente no tienen created_at ni updated_at; los modelos correspondientes usan public $timestamps = false.
- Campo agenda.horainicio: en el DDL observado es interval day(0) to second(0), no un TIME convencional. Se usaban literales como '0 HH:MM:SS' al insertar, NUMTODSINTERVAL(?, 'SECOND') para comparar y getRawOriginal('horainicio') para leer. No sustituyas esos contratos por casts de Carbon sin comprobar la equivalencia en Oracle.
- cita.detalle: se observó varchar2(100) NOT NULL; Oracle interpreta '' como NULL. El código empleaba el valor de respaldo 'Sin observaciones'. Mantén un valor compatible con la restricción, sujeto a la validación real.
- Estados de negocio definidos por CHECK:
  - 'Activo' y 'Inactivo' en estados de paciente, profesional, sede, tipo de servicio, servicio y perfil.
  - 'Pendiente', 'Realizada' y 'Cancelada' en cita.estadocita.
  - 'Activo', 'Inactivo' y 'Cancelada' en estadoorden.
- Estado de autenticación: el login usaba users.estado en minúsculas ('activo'/`'inactivo'`), diferente de ciertos valores del DDL o migraciones. No unifiques estos valores sin inspeccionar la instancia real.
- Tipos de documento: el DDL restringía tipodoc a Cedula Ciudadania, Tarjeta Identidad, Cedula Extranjeria, Pasaporte, Registro Civil, Permiso Proteccion Temporal y Otro. Mantén los valores compatibles con la base hasta que exista un cambio aprobado.
- Perfil por servicio: perfilservicio tenía unicidad sobre (idprofesionalsalud, idservicio); el flujo observado reactivaba o desactivaba perfiles en lugar de eliminarlos.
- Ocupación de horarios: una agenda está ocupada si existe una cita vinculada con estadocita != 'Cancelada'; cancelar libera la disponibilidad según las reglas existentes.
- SQL de Oracle: hay funciones específicas como SYSDATE y NUMTODSINTERVAL. No se puede asumir portabilidad directa a SQLite/MySQL/PostgreSQL sin adaptar esas expresiones.
- Sesiones, caché y colas: en la revisión utilizaban base de datos, por lo que sessions, cache y jobs debían existir y ser accesibles. Comprueba drivers actuales antes de diagnosticar problemas de arranque.
- Longitud de identificadores: se documentó ORA_MAX_NAME_LEN = 30 en la configuración del proyecto; confirma las restricciones efectivas de la instalación antes de diseñar nuevos nombres.
7.1 Diferenciar existencia, registro y funcionalidad de una API
En el diagnóstico del 2026-10-21 se distinguieron cuatro estados. Mantén esa separación en cualquier informe:
1. Dependencia instalada: laravel/sanctum estaba declarado en el proyecto.
2. Archivo existente: se observó routes/api.php en el árbol de trabajo, sin seguimiento de Git en esa revisión.
3. Ruta registrada: en las 71 rutas de la revisión no apareció api/user; bootstrap/app.php no registraba api: en withRouting().
4. API operativa y probada: no se verificó ninguna petición autenticada; faltaban elementos necesarios para considerar funcional la autenticación por token, incluido HasApiTokens en el modelo inspeccionado.
Ninguno de esos hallazgos acredita el estado actual. No afirmes que la API está lista porque un paquete o archivo existe; tampoco afirmes que nunca existió. Inspecciona la configuración presente y ejecuta pruebas autorizadas antes de emitir conclusiones. La decisión de construir o habilitar una API requiere aprobación explícita.
8. Integridad de las reservas — regla crítica
Toda creación, reprogramación o liberación de disponibilidad debe respetar el patrón transaccional observado en Paciente/CitaController::store() y ::update(), previa verificación de su implementación actual:
1. Ejecutar la operación dentro de DB::transaction().
2. Obtener y bloquear la fila de agenda con lockForUpdate(); verificar la habilitación del profesional para el servicio, los estados activos y que la fecha/hora no hayan vencido.
3. Revalidar la ocupación dentro de la transacción, buscando citas del horario con estado distinto de Cancelada y, en reprogramaciones, excluyendo correctamente la cita propia.
4. Crear o actualizar la cita con el estado y la fecha-hora que correspondan a las reglas de negocio ('Pendiente' y derivación de agenda.fecha + horainicio en el flujo documentado).
5. Liberar el horario únicamente cuando la operación autorizada lo permita. En el flujo registrado, la cancelación cambia el estado a 'Cancelada'; no elimina físicamente la cita.
No sustituyas el bloqueo transaccional por una consulta previa sin bloqueo. La aplicación debe resistir solicitudes concurrentes y evitar reservas dobles aun cuando dos usuarios elijan el mismo horario simultáneamente.
9. Procedimiento de trabajo y verificación
- Antes de editar: inspecciona el archivo completo o el contexto suficiente; identifica ruta → middleware → controlador/componente → modelo → vista → base de datos; revisa cambios locales con git status y evita sobrescribir trabajo ajeno.
- Modificaciones mínimas: no inventes archivos, rutas, relaciones o dependencias. Explica las discrepancias detectadas y realiza cambios pequeños, coherentes y reversibles. No hagas commits, push ni despliegues si no se solicitan.
- Pruebas: para cambios PHP, utiliza Laravel Pint y php artisan test cuando sea posible. Informa con precisión de tests ejecutados, resultados y fallos preexistentes. Los 26 tests y sus errores registrados el 2026-10-21 no constituyen un resultado actual.
- Migraciones y datos: no ejecutes migraciones destructivas, migrate --force, borrados, actualizaciones de datos o comandos con efectos persistentes sin autorización. Comprueba la conexión y la base objetivo y prepara respaldo cuando corresponda.
- Diagnóstico: consulta storage/logs/laravel.log sin exponer secretos. Diferencia errores actuales de mensajes acumulados de ejecuciones anteriores.
- Rutas sin Oracle: en la revisión de 2026-10-21, php artisan route:list podía intentar conectar con Oracle por SESSION_DRIVER=database. Si el mismo problema persiste y no hay configuración cacheada, en Windows PowerShell puede utilizarse temporalmente:
  $old = $env:SESSION_DRIVER
  try {
      $env:SESSION_DRIVER = 'file'
      php artisan route:list
  }
  finally {
      $env:SESSION_DRIVER = $old
  }
  Antes comprueba Test-Path 'bootstrap/cache/config.php'; la caché puede impedir que la variable temporal tenga efecto. No presentes un listado de rutas como verificado si el comando no llegó a ejecutarse.
- Cambios visuales o de identidad: verifica al menos login, menú, layouts, dashboards, PDF, imágenes, favicon, metadatos, datos de prueba y textos visibles aplicables. Revisa si quedan identificadores heredados sin alterar las estructuras de datos por simple sustitución de texto.
- Servidor web: comprueba los recursos tanto bajo Apache/Laragon como bajo php artisan serve cuando la modificación afecte URLs, assets, Livewire, sesiones o autenticación. Si se prueba Cloudflare, comprueba adicionalmente host, esquema HTTPS, cabeceras de proxy, redirecciones y acceso autorizado. Una prueba local satisfactoria no acredita la seguridad de un despliegue público.
- Cierre de cada tarea: enumera exactamente qué archivos modificaste, qué verificaste, qué no pudiste verificar y qué decisiones quedaron pendientes. No declares resueltos problemas que no se han vuelto a probar.
10. Extensibilidad — orientación no normativa
Esta sección presenta posibles líneas futuras, no requisitos aprobados ni autorización para implementar código, instalar dependencias o modificar la arquitectura actual.
- Principio rector: el núcleo de agendamiento (servicios, agenda, disponibilidad, reservas) debe permanecer funcional y consistente aunque fallen integraciones externas.
- Facturación externa mediante API: SIGAC no es un sistema de contabilidad o facturación. Si alguna vez se solicita integrar un proveedor externo, separa su cliente, define contratos claros y plantea una degradación segura sin comprometer las citas.
- API REST para aplicaciones móviles o integraciones: si se autoriza, define autenticación, autorizaciones por rol y por recurso, validación, serialización consistente, versionado, límites de uso, CORS, privacidad y pruebas. La revisión histórica de docs/diagnostico-tecnico.md §2.10 no acreditó una API operativa; revisa el estado presente antes de diseñarla.
- Asistente conversacional transversal: se considera, sin aprobación de desarrollo, un asistente para paciente, auxiliar, profesional y administrador. Debe reutilizar casos de uso autorizados del backend, nunca duplicar o saltarse permisos. No presupongas proveedor, modelo ni arquitectura. Los principios preliminares y preguntas pendientes se encuentran en docs/chatbot/seguridad-y-principios.md.
- Capacidades para varias instituciones: la neutralidad de marca permite imaginar una oferta a distintos prestadores, pero no implica separación de datos, soporte multiinstitución, configuración de marca por cliente o arquitectura multitenant ya implementados. No añadas esas capacidades sin requisitos y análisis de aislamiento de datos.
- Arquitectura proporcionada: evita microservicios, capas adicionales y patrones complejos sin necesidad demostrada. Prioriza modularidad, mantenimiento y compatibilidad con Laravel, Livewire y Oracle, salvo decisión arquitectónica expresa.
Requisito de proceso (normativo): futuro asistente conversacional
Cualquier implementación del asistente —incluso una prueba exploratoria integrada en SIGAC— requiere en este orden:
1. Autorización explícita para iniciar la iniciativa y definir su alcance.
2. Especificación previa de roles, usuarios, operaciones permitidas, consentimiento, datos personales y de salud y requisitos funcionales y no funcionales.
3. Análisis formal de amenazas y revisión de seguridad, tomando como base los principios preliminares de docs/chatbot/seguridad-y-principios.md y definiendo sus pruebas de aceptación.
4. Diseño, implementación y pruebas por etapas, solo después de completar y aprobar los pasos anteriores.
La existencia de un documento de intención no acredita controles de seguridad implementados, no garantiza la protección de los datos y no habilita ninguna funcionalidad por sí sola.
Registro documental:
- 2026-10-21: revisión técnica inicial que sirvió de base para las reglas originales y el diagnóstico asociado.
- 2026-11-19: reescritura para fijar la identidad independiente de SIGAC, diferenciar hechos históricos de instrucciones permanentes, documentar las precauciones sobre URLs y Livewire observadas durante pruebas de Laragon/Cloudflare y alinear las instrucciones con los documentos de diagnóstico y seguridad del asistente. No constituye una inspección nueva del repositorio ni una modificación del código.
