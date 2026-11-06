# Asha Spa — Documentación técnica

> **Base de verificación.** Todo lo descrito en esta documentación se contrastó con el contenido de `spaa_codigo.zip` revisado el 5 de noviembre de 2026. Las rutas de archivo son relativas a la raíz del repositorio y los números de línea (`L`) corresponden a esa versión. Si la rama de trabajo (`feat/dolly2`) o el código cambian, las referencias deben volver a verificarse antes de usarse.

## Qué es Asha Spa

Asha Spa es un sistema web para la gestión de un spa y salón de belleza. En el código revisado administra usuarios y roles, clientes, empleados, servicios, citas, facturas con su detalle, pagos, proveedores, productos y movimientos de inventario.

El sistema ofrece dos interfaces sobre la misma aplicación Laravel:

| Interfaz | Para qué se usa | Autenticación | Fuente |
| --- | --- | --- | --- |
| Web (vistas Blade) | Inicio, citas, y listados de clientes, servicios y empleados | Sesión de Laravel | `backend/routes/web.php` |
| API REST (JSON, prefijo `/api`) | Operaciones CRUD de los 12 recursos y búsquedas de inventario | Token Bearer de Laravel Sanctum | `backend/routes/api.php` |

El diseño de datos inicial (`README.md`) también describe un módulo de **Realidad Aumentada** para probar cortes, peinados y colores, un módulo de **predicción de demanda con IA** y **campañas de marketing**. Ninguno de los tres tiene código en el repositorio revisado; su estado se detalla en [Estado del proyecto](estado-del-proyecto.md).

## Tecnologías

Las versiones exactas son las que fija el archivo de bloqueo de cada gestor de paquetes.

| Componente | Versión | Fuente |
| --- | --- | --- |
| PHP (imagen de contenedor) | 8.3 (`php:8.3-cli`); Composer exige `^8.2` | `Dockerfile` L1; `backend/composer.json` |
| Laravel | 12.67.0 | `backend/composer.lock` |
| Laravel Sanctum (tokens de API) | 4.3.3 | `backend/composer.lock` |
| Scribe (documentación de API) | 5.11.0, configurado pero sin documentación generada | `backend/composer.lock`; `backend/config/scribe.php` |
| PHPUnit | 11.5.56 | `backend/composer.lock` |
| MySQL | 8.0 (imagen `mysql:8.0`) | `docker-compose.yml` |
| phpMyAdmin | imagen `phpmyadmin:latest` | `docker-compose.yml` |
| Vite | 7.3.6 | `frontend/package-lock.json` |
| Tailwind CSS | 4.3.3 (con `@tailwindcss/vite`) | `frontend/package-lock.json` |
| laravel-vite-plugin | 2.1.0 | `frontend/package-lock.json` |
| axios | 1.20.0 | `frontend/package-lock.json` |

## Organización del repositorio

| Carpeta o archivo | Contenido |
| --- | --- |
| `backend/` | Aplicación Laravel: `app/` (modelos, interfaces, repositorios, servicios, controladores, requests, policies, middleware, comando), `routes/`, `config/`, `database/` (migraciones y seeders), `tests/` |
| `frontend/` | `resources/views/` (11 vistas y 5 componentes Blade), `resources/css/`, `resources/js/`, `public/` (punto de entrada `index.php`) y configuración de Vite |
| `docker-compose.yml`, `Dockerfile`, `.dockerignore` | Entorno local con tres contenedores: aplicación, MySQL y phpMyAdmin |
| `.env.example` | Variables de entorno de ejemplo (el archivo `.env` se ubica en la raíz, no en `backend/`) |
| `README.md` | Diccionario de datos del diseño inicial y guía breve de arranque |

La separación entre `backend/` y `frontend/` no es la estructura estándar de Laravel. Se logra con tres ajustes: `backend/bootstrap/app.php` L33–34 indica que el `.env` está en la raíz y que la carpeta pública es `frontend/public`; `backend/config/view.php` L4–6 apunta las vistas a `frontend/resources/views`; y `frontend/public/index.php` L16–20 carga el autoload y el arranque desde `backend/`.

## Roles del sistema

Los roles se crean con `backend/database/seeders/RolSeeder.php` y sus permisos se definen en `backend/config/permisos.php` L51–90. Un rol que no figure en ese archivo no tiene ningún permiso.

| Rol | Alcance resumido de sus permisos |
| --- | --- |
| Administrador | Todos los permisos (`*`), incluida la gestión de roles y usuarios |
| Recepcionista | Directorio y gestión de clientes, agenda completa y gestión de citas, catálogo de servicios, datos públicos y de contacto del personal |
| Estilista | Consulta de la agenda completa, del catálogo de servicios y de los datos públicos del personal |
| Esteticista | Igual que Estilista |
| Cliente | Su propio perfil, sus citas, sus pagos y sus facturas; consulta de servicios y de datos públicos del personal |

La matriz completa de 23 permisos se documentará en `seguridad/matriz-de-permisos.md`. Existen diferencias entre lo que estos permisos autorizan en la API y lo que la interfaz web permite en la práctica; se describen en [Estado del proyecto](estado-del-proyecto.md#observaciones-técnicas).

## Mapa de la documentación

| Sección | Documento | Estado de esta documentación |
| --- | --- | --- |
| Inicio | `index.md` | Escrito |
| Estado | `estado-del-proyecto.md` | Escrito |
| Arquitectura (arc42) | `arquitectura/01-introduccion.md` | Escrito |
| | `arquitectura/02-restricciones.md` | Escrito |
| | `arquitectura/03-contexto.md` | Escrito |
| | `arquitectura/04-estrategia.md` | Escrito |
| | `arquitectura/05` a `12` | Por escribir |
| Backend | `backend/capas.md` | Escrito |
| | `backend/referencia/` (controladores, requests, servicios, interfaces, repositorios, modelos, policies, consola y configuración) | Escrito |
| | `backend/flujos-por-operacion.md`, `backend/reglas-de-negocio.md` | Por escribir |
| Primeros pasos | `instalacion.md`, `configuracion-env.md`, `usuarios-prueba.md`, `administrador.md` | Por escribir |
| Frontend | `estructura.md`, `vistas.md`, `componentes.md`, `estilos-y-build.md` | Por escribir |
| Datos | `modelo-er.md`, `diccionario.md`, `migraciones.md`, `seeders.md` | Por escribir |
| Seguridad | `autenticacion.md`, `matriz-de-permisos.md`, `policies.md` | Por escribir |
| API | `index.md`, `endpoints.md` | Por escribir |
| Pruebas | `pruebas/index.md` | Por escribir |
| Decisiones | `decisiones/` (ADR 0001–0009) | Por escribir |
| Guías | `guias/` | Por escribir |
| Anexos | `anexos/observaciones.md` | Por escribir |

## Convenciones de esta documentación

Cada afirmación técnica remite a su archivo fuente con el formato `ruta/al/archivo.php` L*inicio*–*fin*. La funcionalidad se clasifica siempre con uno de estos estados:

| Estado | Significado |
| --- | --- |
| Implementado | El código existe, está enlazado a una ruta o comando y cumple la función descrita. |
| Parcialmente implementado | Existe una parte del código, o funciona en una interfaz pero no en otra, o le falta una pieza para operar. |
| No encontrado | Se buscó en rutas, controladores, servicios, repositorios, modelos, vistas, componentes, JS, CSS y configuración, y no aparece. |
| Propuesto | Figura en el diseño (README) o en planes del equipo, sin código. |
| Pendiente de verificar | Depende de ejecutar la aplicación o las pruebas, o de información fuera del ZIP (por ejemplo, el historial de Git). |

Ninguna prueba automatizada ni prueba manual se ejecutó durante la elaboración de esta documentación.

## Equipo y contexto académico

Proyecto formativo del programa Análisis y Desarrollo de Software, Centro de Diseño y Metrología.

| Rol | Nombre |
| --- | --- |
| Aprendiz | Dolly Juliana Aguirre Heredia |
| Integrantes adicionales | Pendiente de completar por el equipo |
| Tutor | Alex Jaime |
| Ficha | Pendiente de completar por el equipo |
