# Estado del proyecto

> **Base de verificación.** Estado determinado por lectura del código de `spaa_codigo.zip` (5 de noviembre de 2026). No se ejecutó la aplicación ni las pruebas. Si la rama `feat/dolly2` cambia, este estado debe volver a verificarse.

## Resumen

La gestión central del spa (citas, clientes, empleados, servicios, facturación, pagos e inventario) está implementada en la API con autorización por permisos. La interfaz web cubre cuatro módulos y aplica reglas de negocio y de autorización distintas a las de la API. Los módulos diferenciadores del diseño inicial (Realidad Aumentada e IA) y las campañas de marketing son propuestas sin código.

| Estado | Cantidad de funcionalidades |
| --- | --- |
| Implementado | 13 |
| Parcialmente implementado | 11 |
| No encontrado | 5 |
| Propuesto | 4 |
| Pendiente de verificar | 12 |

Los estados se definen en [index.md](index.md#convenciones-de-esta-documentación).

## Implementado

| Funcionalidad | Descripción del comportamiento real | Fuente |
| --- | --- | --- |
| Inicio de sesión web | Valida correo y contraseña, busca solo usuarios activos, exige cuenta habilitada, inicia sesión y regenera la sesión. | `backend/app/Http/Controllers/Web/AuthController.php` L36–72; `backend/app/Repositories/UsuarioRepository.php` L59–62 |
| Inicio de sesión API | Devuelve un token Bearer de Sanctum; `logout` borra el token actual y `me` devuelve los datos básicos del usuario. | `backend/app/Http/Controllers/ApiAuthController.php` L25–78 |
| Registro público de clientes | Crea siempre una cuenta con rol Cliente y su ficha en `clientes`, dentro de una transacción. El visitante no elige el rol. | `Web/AuthController.php` L89–165 |
| Administrador único | El comando `app:make-administrator` asigna el rol Administrador a una cuenta existente si no hay otro administrador; la columna `administrador_unico` con índice único lo refuerza en base de datos. | `backend/app/Console/Commands/MakeAdministrator.php`; `backend/app/Models/Usuario.php` L48–58; migración `2026_11_03_210000_add_unique_administrator_guard_to_usuarios_table.php` |
| Permisos por rol con denegación por defecto | Un rol solo tiene los permisos listados; un rol desconocido no tiene ninguno. | `backend/config/permisos.php` L51–88; `Usuario.php` L81–110 |
| Bloqueo inmediato de cuentas inactivas | En cada petición web o API se comprueba que el usuario, su rol y la definición del rol estén activos. | `backend/app/Http/Middleware/EnsureActiveAccount.php`; `backend/routes/api.php` L50; `backend/routes/web.php` L42 |
| Límites de intentos | Login: 5 por minuto por correo e IP y 20 por IP; registro: 5 por minuto por IP; API: 120 por minuto por usuario o IP. | `backend/app/Providers/AppServiceProvider.php` L129–146 |
| Política de contraseñas | Mínimo 8 caracteres con letras, mayúsculas, minúsculas y números; en producción además se rechazan contraseñas filtradas. | `AppServiceProvider.php` L149–153 |
| CRUD de la API | 12 recursos con `apiResource` o rutas explícitas, más búsquedas de proveedores, productos y movimientos. | `backend/routes/api.php` L50–163 |
| Reglas de citas en la API | El Cliente solo agenda a su nombre; el código lo asigna el servidor si no se envía (el de un Cliente siempre); un Cliente solo puede crear citas en estado pendiente; se rechaza un empleado con otra cita a la misma fecha y hora. Al **editar**, el request no exige fecha futura, empleado disponible ni servicio activo (observación CAP-10 de `backend/capas.md`). | `backend/app/Services/CitaService.php` L58–86, L177–203; `backend/app/Policies/CitaPolicy.php` |
| Facturación | Subtotal y total los calcula el servidor a partir del detalle; el precio unitario del detalle se copia del servicio al crearlo y no se modifica después. | `backend/app/Services/FacturaService.php` L53–107; `backend/app/Services/FacturaServicioService.php` |
| Pagos con aprobación y auditoría | Registrar un pago con estado distinto de pendiente exige el permiso de aprobar; las operaciones se escriben en el canal de log `auditoria` (90 días). | `backend/app/Services/PagoService.php` L52–109; `backend/config/logging.php` L78–84 |
| Ocultamiento de datos del personal en la API | Documento, correo y teléfono se ocultan sin `empleados.ver_contacto`; el salario, sin `empleados.ver_salario`. En la web solo se ocultan al rol Cliente (ver CAP-15 en `backend/capas.md`). | `backend/app/Http/Controllers/EmpleadoController.php` L24–60; `Web/EmpleadoWebController.php` L20–32 |

## Parcialmente implementado

| Funcionalidad | Qué existe | Qué falta o difiere | Fuente |
| --- | --- | --- | --- |
| Módulo web de citas | Listado con filtros, creación y edición. | No usa `CitaService`: no verifica disponibilidad, exige escribir el código, no impide fechas pasadas y no aplica `CitaPolicy` al editar. | `Web/CitaWebController.php` L110–230; `routes/web.php` L48–54 |
| Restricción del rol Cliente en la web | Menú reducido y módulo de citas limitado a sus citas. | La página de inicio lista todas las citas del día con el nombre de cada cliente. | `Web/HomeController.php` L26–37; `frontend/resources/views/home.blade.php` L151–164 |
| Interfaz web | Inicio, citas, clientes, servicios y empleados. | Pagos, facturas, detalle de facturas, proveedores, productos e inventario solo existen en la API. | `routes/web.php` L42–65 |
| Inventario | CRUD de proveedores, productos y movimientos. | Registrar un movimiento no modifica `producto.cantidad`. | `backend/app/Services/MovimientoInventarioService.php`; `backend/app/Repositories/MovimientoInventarioRepository.php` |
| Documentación de la API | Scribe instalado y configurado para `api/*`. | La documentación no está generada. | `backend/config/scribe.php` L36, L71 |
| Pruebas automatizadas | 3 pruebas propias y 2 de plantilla. | No se ejecutaron; una de plantilla probablemente falla (ver pendientes). | `backend/tests/` |
| Recuperación de contraseña | Configuración base de Laravel (broker de contraseñas) y modelo con `Notifiable`. | No hay rutas, vistas, controlador ni migración de `password_reset_tokens`; el correo está en modo `log`. | `backend/config/auth.php` L95–98; `.env.example` L51 |
| Búsquedas y cambios de estado adicionales | 19 métodos escritos en seis controladores API. | No tienen ruta, por lo que no son accesibles. | `CitaController.php` L96–130; `ClienteController.php` L85–104; `EmpleadoController.php` L91–113; `RolController.php` L62–87; `ServicioController.php` L61–78; `UsuarioController.php` L61–74 |
| Vínculo cuenta de usuario y ficha de empleado | Método `Usuario::empleado()`. | La tabla `empleados` no tiene columna `usuario_id`; ningún código llama al método. | `Usuario.php` L112–115; migración `04_empleado_table.php` |
| Relaciones de cliente con reservas y ventas | Métodos `Cliente::reserva()` y `Cliente::venta()`. | Apuntan a las clases `Reserva` y `Venta`, que no existen en `app/Models`; ningún código los llama. | `backend/app/Models/Cliente.php` L36–44 |
| Ocultamiento de datos del personal en la web | El listado web oculta documento, correo y teléfono al rol Cliente. | No consulta `empleados.ver_contacto`: por lectura de código, Estilista y Esteticista verían esos datos, que la API les oculta (observación CAP-15 de `backend/capas.md`). | `backend/app/Http/Controllers/Web/EmpleadoWebController.php` L20–32; `frontend/resources/views/staff/index.blade.php` L66–78 |

## No encontrado

Se buscó en rutas, controladores, servicios, repositorios, modelos, vistas, componentes, JavaScript, CSS y configuración.

| Funcionalidad | Única referencia encontrada |
| --- | --- |
| Realidad Aumentada (prueba virtual de cortes, peinados y colores) | `README.md` L17, L180–190 |
| Predicción de demanda con IA | `README.md` L17, L170–178 |
| Campañas de marketing | `README.md` L158–168 |
| Generación del PDF de la factura | `facturas.pdf_url` es un texto opcional que se guarda tal como llega; no hay código que genere un PDF |
| Envío de correos o notificaciones | No hay uso de `Mail` ni de `notify()` en `backend/app` |

## Propuesto

| Funcionalidad | Origen |
| --- | --- |
| Módulo de Realidad Aumentada (tabla TVisualizacionAR) | Diseño de datos en `README.md` |
| Módulo de predicción con IA (tabla TPrediccionIA) | Diseño de datos en `README.md` |
| Campañas de marketing (tabla TCampanaMarketing) | Diseño de datos en `README.md` |
| Recuperación de contraseña por correo desde el login | Plan del equipo; debe respetar la arquitectura por capas |

## Pendiente de verificar

| Asunto | Cómo verificarlo |
| --- | --- |
| Resultado de las pruebas automatizadas. `tests/Feature/ExampleTest.php` L15–17 espera estado 200 en `/`, ruta que exige sesión. | Ejecutar `docker compose exec app php artisan test` y registrar el resultado |
| Si un Estilista o Esteticista puede editar citas desde la web | Prueba manual con `andres.estilista@example.com` en `/citas/{id}/editar` |
| Si un Cliente ve citas de otros clientes en la página de inicio | Prueba manual con una cuenta registrada como Cliente |
| Si Estilista o Esteticista ven documento, correo y teléfono del personal en la web | Prueba manual con `andres.estilista@example.com` en `/empleados` |
| Efecto de eliminar un rol que tiene usuarios (`DELETE /api/roles/{id}`), dado que `usuarios.rol_id` tiene `onDelete('restrict')` | Prueba en un entorno de pruebas con datos sembrados |
| Efecto de eliminar un usuario (`DELETE /api/usuarios/{id}`) sobre su ficha de cliente, sus citas y sus pagos, según las llaves foráneas en cascada y `restrictOnDelete` | Prueba en un entorno de pruebas |
| Si `app:make-administrator` aparece en `php artisan list` sin registro manual | Ejecutar `docker compose exec app php artisan list` |
| Si una instalación nueva ejecuta todas las migraciones sin error | `php artisan migrate:fresh --seed` en un entorno limpio |
| Efecto de los espacios al final de `DB_PASSWORD` en `.env.example` L28 | Copiar el ejemplo sin cambios y levantar los contenedores |
| Efecto de la zona horaria UTC (`backend/config/app.php` L68) en la regla `after_or_equal:today` de las citas, dado que el spa opera en hora de Colombia (UTC−5) | Crear una cita entre las 19:00 y las 23:59 hora local para la fecha del día |
| Si los archivos generados y residuales están versionados en Git | Revisar el historial del repositorio |
| Si existen ramas con código de AR, IA o campañas | Revisar las demás ramas del repositorio |

## Observaciones técnicas

Estas observaciones describen el comportamiento actual del código, según su lectura. No se corrigieron ni se verificaron en ejecución. Su análisis de riesgo se ampliará en `arquitectura/11-riesgos.md`. Las observaciones propias de la arquitectura por capas (CAP-01 a CAP-20) están en [backend/capas.md](backend/capas.md#16-observaciones-técnicas); varias amplían las de esta tabla.

| Código | Observación | Fuente |
| --- | --- | --- |
| OBS-01 | La web y la API aplican reglas distintas al crear y editar citas. | `Web/CitaWebController.php` L110–230; `CitaService.php` |
| OBS-02 | Las rutas web de edición de citas no exigen permiso y ningún controlador web llama a `Gate` ni `authorize`. Por lectura de código, los roles de solo lectura podrían editar citas. | `routes/web.php` L52–53 |
| OBS-03 | La página de inicio no filtra las citas por usuario. | `Web/HomeController.php` L26–37 |
| OBS-04 | La web decide por el nombre del rol (`esCliente()`); la API, por permisos (`tienePermiso()`). | `Usuario.php` L70–73, L105–110 |
| OBS-05 | Los movimientos de inventario no afectan el stock. | `MovimientoInventarioService.php` |
| OBS-06 | Los tokens de la API no expiran. | `backend/config/sanctum.php` L53 |
| OBS-07 | La verificación de disponibilidad compara solo fecha y hora exactas; no tiene en cuenta la duración del servicio. | `backend/app/Repositories/CitaRepository.php` (`existeCitaEnHorario`) |
| OBS-08 | `empleados.rol` (cargo, con el valor `Estetisista`) y la tabla `roles` (acceso, con `Esteticista`) son conceptos distintos sin vínculo. | migración `04_empleado_table.php` L26–31; `RolSeeder.php` |
| OBS-09 | Las cuentas sembradas son de personal y también tienen ficha de cliente; no se siembra ninguna cuenta Cliente ni Administrador. | `backend/database/seeders/DatabaseSeeder.php` L37–105 |
| OBS-10 | phpMyAdmin inicia sesión automáticamente con las credenciales de la aplicación (limitado a `127.0.0.1`). | `docker-compose.yml` L46–51 |
