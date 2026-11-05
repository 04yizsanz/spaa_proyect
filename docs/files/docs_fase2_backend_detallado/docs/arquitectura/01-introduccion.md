# 1. Introducción y objetivos

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Volver a verificar si cambia la rama `feat/dolly2`.

Este capítulo sigue la plantilla arc42. Describe para qué existe Asha Spa, qué hace hoy el código, qué cualidades busca proteger y quiénes intervienen en el sistema.

## 1.1 Propósito

Asha Spa centraliza la operación diaria de un spa y salón de belleza: quién es cliente, qué personal atiende, qué servicios se ofrecen, cuándo se agenda cada cita, cómo se factura y se cobra, y qué productos entran y salen del inventario. El diseño inicial (`README.md` L1–19) agrega como diferenciador un módulo de Realidad Aumentada y un módulo de predicción con IA, que todavía no tienen código (ver [Estado del proyecto](../estado-del-proyecto.md#no-encontrado)).

## 1.2 Requisitos funcionales implementados

La tabla resume lo que el código hace hoy. El detalle de cada flujo se documentará en `backend/flujos-por-operacion.md`.

| Área | Qué permite el sistema | Interfaz | Fuente principal |
| --- | --- | --- | --- |
| Acceso | Registrarse como Cliente, iniciar y cerrar sesión, obtener un token de API | Web y API | `backend/app/Http/Controllers/Web/AuthController.php`; `backend/app/Http/Controllers/ApiAuthController.php` |
| Usuarios y roles | Crear, consultar, editar y eliminar usuarios y roles (solo Administrador) | API | `backend/app/Http/Controllers/UsuarioController.php`; `RolController.php` |
| Clientes | Consultar el directorio o el propio perfil; crear, editar y eliminar fichas | Web (listado) y API | `ClienteController.php`; `Web/ClienteWebController.php` |
| Empleados | Consultar el personal con ocultamiento de datos sensibles; gestionar fichas | Web (listado) y API | `EmpleadoController.php`; `Web/EmpleadoWebController.php` |
| Servicios | Consultar y gestionar el catálogo | Web (listado) y API | `ServicioController.php`; `Web/ServicioWebController.php` |
| Citas | Agendar, consultar, editar y eliminar citas; filtrar por fecha y estado en la web | Web y API (con reglas distintas) | `CitaController.php`; `Web/CitaWebController.php`; `backend/app/Services/CitaService.php` |
| Facturación | Emitir facturas, agregar servicios al detalle, recalcular totales | API | `FacturaController.php`; `FacturaServicioController.php` |
| Pagos | Registrar pagos de una cita y aprobarlos o rechazarlos | API | `PagoController.php`; `backend/app/Services/PagoService.php` |
| Inventario | Gestionar proveedores, productos y movimientos de entrada y salida; buscar por varios criterios | API | `ProveedorController.php`; `ProductoController.php`; `MovimientoInventarioController.php` |

## 1.3 Objetivos de calidad

Los objetivos de calidad no están escritos como requisitos formales en el repositorio. Los siguientes se deducen de mecanismos que el código implementa de forma explícita; cada uno remite a la evidencia.

| Prioridad | Objetivo | Cómo se refleja en el código | Fuente |
| --- | --- | --- | --- |
| 1 | **Confidencialidad**: cada usuario ve solo lo que su rol permite | Permisos con denegación por defecto; registros ajenos responden 404 en la API; ocultamiento de salario y contacto del personal | `backend/config/permisos.php`; `backend/app/Policies/CitaPolicy.php` L26–31; `EmpleadoController.php` L47–60 |
| 2 | **Integridad financiera**: los valores de cobro no se manipulan desde el cliente | Subtotal, total y precio unitario prohibidos en las peticiones y calculados en el servidor; pagos y facturas no se eliminan | `backend/app/Http/Requests/Factura/StoreFacturaRequest.php`; `FacturaService.php` L94–107; `PagoPolicy.php` L64–69 |
| 3 | **Trazabilidad**: queda registro de operaciones sensibles | Log de auditoría de pagos y facturas; log de inicios de sesión sin el correo del usuario | `backend/config/logging.php` L78–84; `PagoService.php` L100–109; `Web/AuthController.php` L67–68; `ApiAuthController.php` L46 |
| 4 | **Control de acceso inmediato**: desactivar una cuenta corta su acceso | Middleware que revisa el estado del usuario y del rol en cada petición | `backend/app/Http/Middleware/EnsureActiveAccount.php` |
| 5 | **Mantenibilidad**: separar responsabilidades | Arquitectura por capas en la API, con operaciones que omiten algunas capas | [backend/capas.md](../backend/capas.md), secciones 1.1 y 15 |

El cumplimiento de estos objetivos no es uniforme: la interfaz web no aplica los mismos controles que la API en el módulo de citas, en la página de inicio y en el listado de personal (observaciones OBS-01 a OBS-04 de [Estado del proyecto](../estado-del-proyecto.md#observaciones-técnicas) y CAP-15 de [backend/capas.md](../backend/capas.md#16-observaciones-técnicas)). Ninguno de estos controles se verificó en ejecución.

## 1.4 Partes interesadas

| Parte interesada | Interés en el sistema | Cómo interactúa |
| --- | --- | --- |
| Administrador del spa | Control total: personal, roles, finanzas e inventario | Web y API; su cuenta se asigna por consola |
| Recepcionista | Atender clientes y organizar la agenda | Web y API |
| Estilista y Esteticista | Consultar la agenda y el catálogo | Web y API (solo lectura según `config/permisos.php`) |
| Cliente | Agendar y consultar sus citas, pagos y facturas | Registro público, web y API |
| Equipo de desarrollo | Mantener y extender el código | Repositorio, Docker, consola de Artisan |
| Instructores y jurados del SENA | Evaluar el diseño, la implementación y la documentación | Esta documentación y la demostración |

## 1.5 Fuera del alcance del código revisado

No forman parte del sistema actual: la Realidad Aumentada, la predicción con IA, las campañas de marketing, la generación de PDF de facturas, el envío de correos y la recuperación de contraseña. Su estado exacto (propuesto, parcial o no encontrado) está en [Estado del proyecto](../estado-del-proyecto.md).
