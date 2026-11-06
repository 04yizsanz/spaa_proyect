# 4. Estrategia de solución

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Volver a verificar si cambia la rama `feat/dolly2`.

Este capítulo resume las decisiones fundamentales que dan forma a Asha Spa. Cada decisión se documentará en detalle como ADR en `decisiones/`. Al final se indica dónde el código actual no sigue la estrategia, porque esa diferencia es necesaria para entender el sistema tal como funciona.

## 4.1 Decisiones fundamentales

| N.º | Decisión | En qué consiste | Fuente |
| --- | --- | --- | --- |
| E1 | Monolito Laravel con dos interfaces | Una sola aplicación sirve vistas Blade (con sesión) y una API REST JSON (con token). Ambas comparten modelos, base de datos y la definición de permisos. | `backend/routes/web.php`; `backend/routes/api.php` |
| E2 | Arquitectura por capas en la API | La API se organiza en siete capas: ruta, request, controlador, servicio, interfaz, repositorio y modelo. No todas las operaciones pasan por todas: las de lectura y eliminación no usan request, y las ediciones de citas, facturas y pagos guardan sobre el modelo en lugar de hacerlo por el repositorio (ver `backend/capas.md`, sección 1.1). Las dependencias se resuelven por inyección y los enlaces interfaz→repositorio se registran en un solo lugar. | `backend/app/Providers/AppServiceProvider.php` L44–111; [backend/capas.md](../backend/capas.md) |
| E3 | Autorización por permisos, no por nombre de rol | Los permisos de cada rol se declaran en un archivo de configuración; las policies y el middleware consultan `tienePermiso()`. Todo lo no declarado se deniega. | `backend/config/permisos.php`; `backend/app/Models/Usuario.php` L81–110; `backend/app/Policies/PermisoPolicy.php` |
| E4 | Verificación del estado de la cuenta en cada petición | Un middleware revisa que el usuario y su rol sigan activos antes de atender cualquier ruta protegida, también para tokens ya emitidos. | `backend/app/Http/Middleware/EnsureActiveAccount.php` |
| E5 | Visibilidad por registro en el modelo | Los modelos con datos por cliente (Cita, Cliente, Factura, FacturaServicio, Pago) definen un alcance `visiblePara($usuario)`; un registro que no se puede ver se trata como inexistente (404). | `backend/app/Models/Cita.php` L74–87 y equivalentes |
| E6 | Valores calculados en el servidor | El subtotal, el total y el precio unitario del detalle los fija siempre el servidor: las peticiones que los envían se rechazan (`prohibited`). El código de cita lo asigna el servidor cuando no se envía; si quien agenda es un Cliente, el valor que envíe se descarta; quien tiene `citas.gestionar` puede indicarlo. | `backend/app/Services/CitaService.php` L177–194; `backend/app/Http/Requests/Factura/StoreFacturaRequest.php`; `FacturaServicio/StoreFacturaServicioRequest.php` |
| E7 | Borrado lógico, salvo registros financieros | Clientes, empleados, servicios, citas, proveedores, productos y movimientos usan `SoftDeletes`. Pagos, facturas y su detalle no tienen borrado lógico y sus policies impiden eliminarlos. Usuarios y roles se eliminan físicamente. | Modelos en `backend/app/Models/`; `PagoPolicy.php` L64–69; `FacturaPolicy.php` L47–52 |
| E8 | Separación física de backend y frontend | El código PHP vive en `backend/`; vistas, estilos y punto de entrada público en `frontend/`. | `backend/bootstrap/app.php` L33–34; `backend/config/view.php` L4–6; `frontend/public/index.php` |
| E9 | Entorno reproducible con Docker | La aplicación, MySQL y phpMyAdmin están definidos como servicios de Docker Compose. La imagen ejecuta `composer install` al construirse y un volumen con nombre (`vendor`) conserva esas dependencias aunque la carpeta del proyecto se monte sobre el contenedor. No se ejecutó. | `docker-compose.yml` L9–11; `Dockerfile` L24, L29–32 |
| E10 | Administrador único asignado por consola | Solo una cuenta puede tener el rol Administrador; la regla se aplica en el comando, en la validación de usuarios y con un índice único en base de datos. | `MakeAdministrator.php`; `Http/Requests/Usuario/StoreUsuarioRequest.php` L20–25; `Usuario.php` L48–58 |

## 4.2 Relación entre objetivos de calidad y estrategia

| Objetivo de calidad (capítulo 1) | Decisiones que lo sostienen |
| --- | --- |
| Confidencialidad | E3, E4, E5 |
| Integridad financiera | E6, E7 |
| Trazabilidad | Log de auditoría en los servicios de pagos y facturación (`backend/config/logging.php` L78–84) |
| Control de acceso inmediato | E4 |
| Mantenibilidad | E2, E8 |
| Facilidad de instalación | E9 |

## 4.3 Dónde el código no sigue la estrategia

La estrategia descrita se cumple en la mayor parte de la API. La interfaz web se aparta de ella casi por completo, y algunos componentes de la API también lo hacen en puntos concretos. Estas diferencias no se corrigieron y se documentan como observaciones técnicas.

| Decisión | Desviación observada | Fuente |
| --- | --- | --- |
| E2 (capas) | Los seis controladores web consultan y guardan con los modelos directamente; no usan requests, servicios, interfaces ni repositorios (salvo `UsuarioService` en el login y el registro). El registro crea el rol y la ficha de cliente con el modelo. En la API, `FacturaServicioService` consulta `Factura` y `Servicio` sin repositorio, y las ediciones de citas, facturas y pagos guardan sobre el modelo. | `backend/app/Http/Controllers/Web/`; `Web/AuthController.php` L123, L141; `FacturaServicioService.php` L57–58; `CitaService.php` L131, L138; `FacturaService.php` L81–82; `PagoService.php` L78 |
| E3 (permisos) | La web decide con `esCliente()`, que compara el nombre del rol. | `Usuario.php` L70–73; `Web/CitaWebController.php` L38, L76, L115, L167, L196, L240; `Web/EmpleadoWebController.php` L20 |
| E3 y E5 (autorización por registro) | Las rutas web de edición de citas no exigen permiso ni llaman a `CitaPolicy`; la página de inicio no aplica `visiblePara`. | `routes/web.php` L52–53; `Web/HomeController.php` L26–37 |
| E6 (cálculo en servidor) | En la web, el código de cita lo escribe el usuario y es obligatorio. | `Web/CitaWebController.php` L128 |
| E3 (roles y usuarios) | `RolPolicy` y `UsuarioPolicy` existen pero no se invocan; esas rutas se protegen con el middleware `EnsureAdministrator`. | `routes/api.php` L103–107 |

El análisis de riesgo de cada desviación se documentará en `arquitectura/11-riesgos.md`, y la decisión que tome el equipo sobre ellas, en el ADR 0009.
