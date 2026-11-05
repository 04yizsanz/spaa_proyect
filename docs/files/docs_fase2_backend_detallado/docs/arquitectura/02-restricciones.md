# 2. Restricciones

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Las versiones provienen de `backend/composer.lock` y `frontend/package-lock.json`; si se actualizan las dependencias, esta tabla debe revisarse.

Las restricciones son condiciones que el equipo no eligió libremente en cada decisión de diseño: vienen de la tecnología adoptada, del contexto formativo o de la configuración vigente. Se agrupan en técnicas, de configuración, organizacionales y de convenciones.

## 2.1 Restricciones técnicas

| Restricción | Detalle | Fuente |
| --- | --- | --- |
| Lenguaje y framework del servidor | PHP con Laravel 12 (12.67.0). Composer exige PHP `^8.2`; el contenedor usa PHP 8.3. | `backend/composer.json`; `backend/composer.lock`; `Dockerfile` L1 |
| Base de datos | MySQL 8.0. Las pruebas automatizadas usan SQLite en memoria. | `docker-compose.yml`; `backend/phpunit.xml` L26–27 |
| Autenticación de la API | Tokens personales de Laravel Sanctum 4.3.3, tabla `personal_access_tokens`. | `backend/composer.lock`; migración `2026_11_05_000000_create_personal_access_tokens_table.php` |
| Interfaz web | Vistas Blade renderizadas en el servidor; estilos con Tailwind CSS 4 compilados con Vite 7. No hay framework de JavaScript en el cliente; `resources/js` solo configura axios. | `frontend/package.json`; `frontend/resources/js/bootstrap.js` |
| Dependencia externa en el navegador | Las fuentes Playfair Display e Inter se cargan desde Google Fonts, lo que requiere conexión a Internet para verse como fueron diseñadas. | `frontend/resources/css/app.css` L6 |
| Ejecución local | Docker Compose con tres servicios: `app` (contenedor `c_laravel`, `php artisan serve` en el puerto 8000), `db` (`c_mysql`, puerto 3308 solo en `127.0.0.1`) y `phpmyadmin` (`c_phpmyadmin`, puerto 8881 solo en `127.0.0.1`). | `docker-compose.yml` |
| Servidor de aplicación | `php artisan serve` es el servidor de desarrollo de Laravel; el repositorio no incluye configuración de un servidor de producción (Nginx o Apache con PHP-FPM). `frontend/public/.htaccess` solo aplica si se sirve con Apache. | `Dockerfile`; `docker-compose.yml` |
| Variables obligatorias | `docker-compose.yml` declara `DB_ROOT_PASSWORD` y `DB_PASSWORD` con la sintaxis `${VAR:?mensaje}`, que indica a Docker Compose detenerse con ese mensaje si la variable falta en `.env`. No se ejecutó. | `docker-compose.yml` L28, L30, L51 |
| Estructura no estándar | El backend lee el `.env` de la raíz y publica desde `frontend/public`; las vistas viven en `frontend/resources/views`. Cualquier herramienta que asuma la estructura estándar de Laravel debe ajustarse. | `backend/bootstrap/app.php` L33–34; `backend/config/view.php` L4–6 |

## 2.2 Restricciones de configuración vigente

Estos valores son los de `.env.example` y de los archivos de `backend/config/`. Determinan comportamientos que la documentación funcional debe tener en cuenta.

| Configuración | Valor actual | Consecuencia | Fuente |
| --- | --- | --- | --- |
| Zona horaria | `UTC` (fija en el archivo, no se lee del `.env`) | Las fechas y horas se calculan en UTC, aunque el spa opera en Colombia (UTC−5). Su efecto sobre la validación de fechas de citas está pendiente de verificar. | `backend/config/app.php` L68 |
| Idioma | `APP_LOCALE=en`, `APP_FAKER_LOCALE=en_US` | Los mensajes de validación propios están en español porque se escriben en cada request o controlador; los mensajes por defecto de Laravel saldrían en inglés. | `.env.example` L7–9; `backend/config/app.php` L81 |
| Nombre de la aplicación | `APP_NAME=Laravel` | El título por defecto de las páginas y del remitente de correo es “Laravel”, no “Asha Spa”. | `.env.example` L1 |
| Sesiones | Archivo, 120 minutos, sin cifrado | Las sesiones web se guardan en `backend/storage/framework/sessions`. | `.env.example` L31–35; `backend/config/session.php` L21, L35, L50 |
| Caché y colas | Caché en archivo; colas síncronas | No hay procesos en segundo plano: todo se ejecuta dentro de la petición. | `.env.example` L39–41 |
| Correo | `MAIL_MAILER=log` | Ningún correo sale del sistema; se escribiría en el log. | `.env.example` L51 |
| Tokens de API | Sin expiración | Un token sigue siendo válido hasta que se cierra sesión o se borra. | `backend/config/sanctum.php` L53 |
| Modo de depuración | `APP_DEBUG=true` | Adecuado solo para desarrollo. | `.env.example` L4 |

## 2.3 Restricciones organizacionales

| Restricción | Detalle |
| --- | --- |
| Contexto formativo | Proyecto del programa Análisis y Desarrollo de Software (SENA, Centro de Diseño y Metrología), evaluado por instructores y jurados. |
| Entorno de desarrollo | El equipo trabaja en Windows con Docker; el código se monta en el contenedor `c_laravel` desde la carpeta del proyecto (`docker-compose.yml`, volumen `.:/var/www/html`). |
| Trabajo por ramas | El trabajo actual está en la rama `feat/dolly2`, creada desde `main`. Esta documentación corresponde al estado del ZIP entregado de esa rama. |
| Asignación del administrador | Solo puede existir un Administrador. La primera asignación se hace por consola (`app:make-administrator`); el registro público nunca asigna ese rol. |

## 2.4 Convenciones del código

Estas convenciones existen en el código y la documentación las respeta al nombrar archivos, tablas y campos.

| Convención | Ejemplos | Excepciones encontradas |
| --- | --- | --- |
| Nombres de dominio en español | `Cita`, `Empleado`, `MovimientoInventario`, `tienePermiso()` | Algunos métodos en inglés: `getByName`, `getByLastname` en `ClienteInterface.php` L11–13 |
| Llaves primarias con nombre propio | `cliente_id`, `servicio_id`, `citas_id` | `factura_servicio` usa llave compuesta (`factura_id`, `servicio_id`) |
| Tablas en plural | `usuarios`, `citas`, `pagos` | En singular: `proveedor`, `producto`, `movimientoinventario`, `factura_servicio` |
| Migraciones de negocio numeradas | `01_rol_table.php` a `12_MoivimientoInventario.php` | Las posteriores usan fecha (`2026_…`) y cinco no tienen prefijo (`add_…`, `create_…`); el orden de ejecución se documentará en `datos/migraciones.md` |
| Una carpeta de requests por entidad | `Http/Requests/Cita/StoreCitaRequest.php` | Ninguna |
| Nombres de métodos de servicio | `getAll`, `getById`, `create`, `update`, `delete` (Cliente, Empleado, Rol, Servicio, Usuario) | Proveedor, Producto y MovimientoInventario usan `list`, `show`, `store`, `update`, `destroy`; Factura, Pago y FacturaServicio usan `list`/`listarPara`, `show`, `store`, `update`; Cita usa `listarPara`, `crear`, `actualizar`, `eliminar` |
| Formato de respuesta JSON | El recurso o la colección directamente | Proveedor, Producto y MovimientoInventario envuelven la respuesta en `{ "success": …, "data": … }` |
