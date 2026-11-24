Asistente conversacional transversal de SIGAC
Contexto, principios preliminares de seguridad y decisiones pendientes
Naturaleza del documento: intención preliminar. Este texto describe una posible evolución de SIGAC, no una funcionalidad existente o aprobada. No demuestra que se hayan implementado controles de seguridad, no garantiza seguridad ni autoriza instalar dependencias, modificar código, exponer datos o habilitar funciones. Antes de cualquier implementación deben aprobarse el alcance, los requisitos y un análisis formal de amenazas.

Campo	Descripción
Creación	2026-10-21
Revisión documental	2026-11-19
Estado	No iniciada: intención y diseño preliminar
Producto	SIGAC, plataforma web independiente de gestión y agendamiento de citas médicas
Sector	Salud; sin pertenencia, representación ni vinculación institucional implícita
Referencias	AGENTS.md (instrucciones de trabajo vigentes, sujetas a revisión), docs/diagnostico-tecnico.md (observaciones técnicas fechadas)


1. Contexto e identidad del producto
SIGAC es un producto de software independiente orientado a la gestión y el agendamiento de citas médicas. Su dominio funcional incluye usuarios, pacientes, profesionales de la salud, auxiliares, administradores, sedes, servicios, agendas, disponibilidades y citas. La aplicación no representa ni pertenece por defecto a una institución de salud concreta.

La independencia de la marca no implica que el producto ya admita múltiples organizaciones, separación de datos por institución o personalización de marca por cliente. Esas capacidades solo existirán si se especifican, implementan y prueban explícitamente. Los términos sede, institución usuaria o IPS se emplean únicamente como conceptos generales del dominio sanitario.

Se considera como posible evolución futura un asistente conversacional que ayude a los cuatro roles actuales de la plataforma:
Rol	Orientación tentativa; no constituye un requisito aprobado
Paciente	Comprender el proceso de agendamiento y consultar sus propias citas o las disponibilidades que tenga permitido ver.
Auxiliar	Consultar agendas y disponibilidades autorizadas, y resolver dudas operativas.
Profesional de la salud	Consultar su propia agenda y las citas que tenga asignadas.
Administrador	Recibir orientación sobre gestión de usuarios, servicios, sedes y disponibilidades dentro de sus atribuciones.


Inicialmente, el asistente podría limitarse a explicar el uso de SIGAC. Solo después de una aprobación específica podría acceder a información autenticada o ejecutar operaciones en nombre del usuario, siempre a través de funcionalidades autorizadas del backend.
1.1 Alcance de esta iniciativa
- Sí es: una dirección preliminar de diseño, documentada para abordar la seguridad desde el inicio.
- No es: aprobación de desarrollo, ampliación automática del alcance del proyecto, elección de tecnología o compromiso de entrega.
- No presupone: proveedor o modelo de IA, acceso a Oracle, API funcional, infraestructura de agentes, integración conversacional ni herramientas operativas.
- No altera: reglas existentes de autenticación, autorización, reserva de citas, modelos ni contratos de datos.
- No habilita: asesoría clínica, diagnóstico médico, tratamiento automatizado de datos sensibles ni acciones autónomas.
2. Estado y condiciones previas a cualquier implementación
Aspecto	Estado documental
Aprobación de producto	Pendiente de autorización explícita.
Especificación de alcance y operaciones	No aprobada.
Análisis formal de amenazas	No documentado como realizado.
Requisitos funcionales y no funcionales	Pendientes para esta iniciativa.
Proveedor, modelo y arquitectura de IA	Sin decisión aprobada.
Integración conversacional y herramientas	No acreditadas por este documento.
Controles específicos del asistente	No acreditados como implementados. Los principios de §3 son objetivos por concretar y verificar.


La iniciativa requiere esta secuencia, antes de escribir código:
1. Autorización explícita para iniciar el trabajo y definición del responsable de aprobar cambios.
2. Especificación previa: alcance, roles, casos de uso, datos necesarios, operaciones permitidas y prohibidas, confirmaciones, privacidad y criterios de aceptación.
3. Revisión de seguridad y privacidad: amenazas, límites de confianza, controles técnicos, posibles proveedores y cumplimiento aplicable.
4. Diseño e implementación incremental: empezar por un caso de uso pequeño, con controles en el backend y pruebas automatizadas.
5. Validación previa a habilitación: pruebas funcionales, de seguridad, privacidad y recuperación ante errores, y aprobación explícita de despliegue.
Ninguno de estos pasos debe darse por completado por la sola existencia de este documento.
3. Diez principios preliminares de seguridad
Los diez principios siguientes constituyen criterios de diseño propuestos. Si se aprueba la iniciativa, deberán convertirse en requisitos verificables, revisados frente a amenazas concretas; no describen controles ya desplegados.
3.1 La autorización corresponde al backend
El modelo de IA no es una autoridad de seguridad. Laravel debe autenticar a la persona usuaria y autorizar cada lectura y operación con su identidad y permisos efectivos. La información escrita en un mensaje, el rol declarado por el usuario o la respuesta del modelo no son prueba de autorización.

El middleware role: que comprueba users.rol no basta, por sí solo, para verificar la propiedad del recurso. Se deben aplicar controles de acceso por registro y contexto incluso cuando el rol parezca suficiente. Consultar AGENTS.md y verificar las políticas y middleware realmente presentes en el código antes de reutilizarlos.
3.2 Mínimo privilegio y herramientas delimitadas
El asistente solo podrá invocar herramientas previamente definidas y aprobadas, con parámetros tipados, validación y permisos acotados. No deberá contar con:
- Acceso directo o indiscriminado a Oracle.
- Ejecución libre de SQL.
- Ejecución de comandos del sistema o código arbitrario.
- Lectura general de archivos, variables de entorno o secretos.
- Operaciones administrativas globales concedidas solo porque el modelo las solicite.
La lista de herramientas debe ser explícita, revisable y no ampliarse silenciosamente durante el desarrollo.
3.3 Resistencia a prompt injection
Mensajes de usuarios, documentos, resultados de búsquedas, campos de base de datos y contenidos devueltos por herramientas son datos no confiables. Ninguna instrucción contenida en esos datos puede invalidar las reglas del servidor, ampliar privilegios, revelar secretos ni activar operaciones fuera de alcance.

Las decisiones críticas deben depender de controles deterministas en el backend, nunca exclusivamente de un system prompt o de la clasificación del modelo.
3.4 Aislamiento de usuarios, roles y, si aplica, organizaciones
Cada operación debe comprobar que el usuario tiene derecho a acceder a ese recurso concreto:
- Un paciente no puede consultar ni modificar citas de otras personas.
- Un profesional solo puede acceder a la información asignada y autorizada para su ejercicio.
- Un auxiliar está limitado al alcance operativo que se le haya concedido.
- Un administrador está sujeto a reglas de negocio y restricciones de acceso; el asistente no le ofrece un atajo.
Condición futura: si SIGAC admite varias instituciones usuarias, también deberán aislarse sus datos en todas las consultas, acciones, herramientas y registros. No se afirma que exista arquitectura multiinstitución o multitenant actualmente.
3.5 Protección de datos personales y de salud
El asistente debe utilizar la menor cantidad posible de datos. Antes de adoptar un modelo o proveedor hay que resolver:
- Finalidad y base jurídica: qué información personal o de salud es necesaria, para qué se usa y qué autorizaciones o consentimientos proceden.
- Retención: cuánto tiempo se conservan conversaciones, respuestas, trazas y auditorías, quién accede y cómo se eliminan.
- Proveedores externos: qué datos se transfieren, con qué garantías contractuales, en qué jurisdicciones se procesan y si se destinan al entrenamiento.
- Protección técnica: cifrado, manejo y rotación de credenciales, control de acceso, minimización y seudonimización cuando corresponda.
- Cumplimiento: evaluación de las obligaciones legales aplicables al tratamiento de datos personales y sensibles en el lugar de operación.
SIGAC puede tratar identificaciones, información de contacto, detalles de citas y datos vinculados con la atención en salud. Eso exige controles más rigurosos que los de un chatbot informativo genérico.
3.6 Separación entre consulta y cambio de estado
Consultar información no equivale a estar autorizado para cambiarla. Crear, reprogramar o cancelar una cita requeriría, como mínimo:
- Identidad, sesión y permisos válidos en el momento de ejecutar la operación.
- Validaciones del servidor y comprobación de titularidad y alcance.
- Confirmación expresa del usuario cuando corresponda, separada de la intención inferida por la IA.
- Resultado confirmado por el sistema antes de informar que la acción terminó.
- Registro de auditoría proporcional (§3.10).
Las reservas deben preservar las reglas transaccionales del sistema (según su implementación vigente): DB::transaction(), lockForUpdate() y revalidación de ocupación, como salvaguardas frente a concurrencia. Verificar su aplicabilidad en el caso de uso real antes de integrarlo.
3.7 Defensa en profundidad
La seguridad no puede basarse únicamente en instrucciones al modelo. Se requieren, según los riesgos identificados:
- Autenticación, autorización y reglas de negocio verificadas en Laravel.
- Validación de parámetros de entrada y de resultados de herramientas antes de mostrarlos o ejecutarlos.
- Límites de tamaño de mensajes, frecuencia, coste, tiempo y recursos.
- Tratamiento seguro de excepciones y respuestas del proveedor, sin revelar rutas internas, datos personales o secretos.
- Restricción de herramientas en código y supervisión de operaciones sensibles.
- Respuestas degradadas de forma segura cuando falten permisos, datos o servicios externos.
3.8 Pruebas obligatorias antes del despliegue
Prueba	Comportamiento exigido
Acceso entre roles	Un rol no ejecuta funciones reservadas a otro.
Propiedad y alcance	Un usuario no consulta ni altera citas o registros ajenos.
Prompt injection	Instrucciones maliciosas en mensajes y datos recuperados no amplían permisos.
Herramientas no autorizadas	La invocación se rechaza en el backend.
Cambio de identificadores	Modificar un id no permite acceder a otro recurso.
Fuga de información	Respuestas, errores, trazas y logs no exponen secretos ni datos innecesarios.
Abuso automatizado	Cuotas y límites resisten peticiones repetidas o maliciosas.
Fallo de proveedor	Timeouts y respuestas inválidas no crean citas ni estados inconsistentes.
Concurrencia	Solicitudes simultáneas no producen reservas incompatibles.
Separación institucional, si existe	Una institución usuaria no accede a datos de otra.


Cada prueba deberá tener criterio de aceptación y evidencia. La tabla no declara pruebas ejecutadas.
3.9 Transparencia, exactitud y límites clínicos
El asistente debe indicar que es automatizado, explicar sus límites y ofrecer una alternativa de atención humana cuando corresponda. No debe inventar horarios, citas ni resultados operativos, ni anunciar éxito sin confirmación del backend.

No sustituye al personal clínico, no diagnostica, no prescribe y no debe proporcionar decisiones clínicas individualizadas como funcionalidad de SIGAC.
3.10 Trazabilidad proporcional
Evaluar registros de auditoría que permitan conocer quién solicitó una operación, qué herramienta se utilizó, a qué recurso se intentó acceder y cuál fue el resultado, sin convertir el log en una copia indiscriminada de datos médicos ni registrar secretos, contraseñas o conversaciones completas por defecto.

Definir acceso, retención, integridad de registros y procedimientos de revisión antes de habilitar acciones sensibles.
4. Preguntas de diseño abiertas
Las preguntas siguientes siguen abiertas hasta que haya especificación y decisión documentadas.
4.1 Producto y alcance
1. ¿El asistente será exclusivamente orientativo o también operativo?
2. Si puede operar, ¿qué acciones se autorizan por rol y cuáles se prohíben explícitamente?
3. ¿Cómo se gestionan solicitudes ambiguas, fuera de alcance o no autorizadas?
4. ¿Cómo se deriva al usuario a soporte o personal humano?
4.2 Identidad, sesión y permisos
5. ¿Cómo reutilizará la sesión web y los campos de autorización reales (users.rol, users.estado), si corresponde?
6. ¿Cómo se vinculará la identidad autenticada a cada herramienta, sin confiar en identificadores aportados por el modelo?
7. ¿Qué acciones exigen confirmación y cómo quedará evidenciada?
8. ¿Qué ocurre si cambia el rol, el estado de la cuenta o la sesión durante una conversación?
4.3 Datos y privacidad
9. ¿Qué datos personales o de salud son imprescindibles para cada caso de uso y rol?
10. ¿Qué datos, en su caso, se enviarían a proveedores externos y mediante qué salvaguardas?
11. ¿Quién determina la retención de conversaciones, auditorías y trazas y los mecanismos de eliminación?
12. ¿Cómo se anonimizarían o agregarían los datos destinados a evaluación?
13. ¿Cómo se informaría a los usuarios del tratamiento y cómo se documentarían las autorizaciones aplicables?
4.4 Arquitectura e integración
14. ¿La orquestación estará dentro de Laravel o en un servicio separado? La revisión histórica de docs/diagnostico-tecnico.md §2.10 no verificó una API operativa; se requiere comprobación actual antes de depender de ella.
15. ¿Cómo se reutilizarán los casos de uso sin duplicar validaciones o reglas de negocio?
16. ¿Qué políticas o mecanismos comprobarán la autorización en cada herramienta?
17. ¿Cómo se gestionarán costes, latencias, reintentos y disponibilidad del proveedor?
18. ¿Qué auditoría se adoptará, con qué detalle y retención?
4.5 Operación y evolución
19. ¿Qué límites de uso habrá por persona y rol, y cómo se controlarán costes y consumo?
20. ¿Qué cobertura y criterios de aceptación serán obligatorios para las pruebas de §3.8?
21. ¿Quién aprobará el despliegue y reevaluará los controles después de cambios de herramientas?
22. ¿Qué procedimiento de suspensión, reversión y respuesta a incidentes existirá?
23. Si en el futuro se habilitan varias instituciones usuarias, ¿qué modelo de separación de datos, configuración y autorización se exigirá?
5. Relación con las reglas y el diagnóstico existentes
Estas observaciones describen dependencias de decisión; no modifican ni sustituyen las instrucciones del repositorio.
1. Alcance vigente: el asistente no se incorpora automáticamente al alcance de desarrollo. Antes de implementarlo deberá verificarse la versión actual de AGENTS.md y aprobar formalmente la ampliación correspondiente.
2. Instrucciones para agentes: cualquier regla de proceso relacionada con esta iniciativa debe seguir siendo coherente con AGENTS.md. Las referencias históricas a su §10 deben comprobarse contra el archivo vigente; este documento no modifica por sí solo ese archivo.
3. Diagnóstico técnico: docs/diagnostico-tecnico.md conserva la revisión del 2026-10-21 como referencia histórica. Sus hallazgos no equivalen a una comprobación del estado actual del código.
4. API: el diagnóstico histórico no acreditó una API funcional. No se da por existente ni se autoriza su construcción sin una decisión aparte.
5. Autorización y reservas: ninguna integración conversacional puede eludir políticas, validaciones por recurso o reglas de concurrencia.
6. Identidad independiente: todas las explicaciones, mensajes de ejemplo, interfaces y referencias nuevas del asistente deben usar SIGAC y terminología sanitaria neutral. No introducir marcas, contactos o identidades de instituciones ajenas como si fueran parte del producto. Una futura configuración de marca por institución usuaria requeriría diseño propio y control de acceso.
6. Registro de cambios
Fecha	Cambio
2026-10-21	Documento inicial de intención sobre un asistente transversal para cuatro roles, principios preliminares y preguntas abiertas. Sin autorización ni implementación.
2026-11-19	Reescritura documental para expresar la independencia institucional de SIGAC; distinción entre marca del producto y una eventual organización usuaria; precisión de controles por recurso, privacidad, pruebas, preguntas abiertas y referencias históricas. Sin implementación ni validación técnica nueva.
