# Referencia detallada del backend

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Volver a verificar si cambia la rama `feat/dolly2`.

[`backend/capas.md`](../capas.md) explica cómo está construido el backend, capa por capa, con el flujo de la cita como ejemplo principal. Esta carpeta documenta, componente por componente, las clases y métodos que `capas.md` solo resumía.

| Documento | Contenido |
| --- | --- |
| [`controladores-api.md`](controladores-api.md) | Los diez controladores de la API restantes, la clase base `Controller`, las búsquedas de `ProductoController` y los métodos sin ruta de `CitaController` |
| [`controladores-web.md`](controladores-web.md) | Constructor, `showLogin`, `login`, `showRegister` y `logout` de `AuthController` |
| [`requests.md`](requests.md) | Los 17 requests no explicados en `capas.md` |
| [`servicios.md`](servicios.md) | Los siete servicios que delegan y los métodos restantes de los servicios con reglas |
| [`interfaces.md`](interfaces.md) | Las once interfaces de entidad restantes |
| [`repositorios.md`](repositorios.md) | Los repositorios y métodos restantes |
| [`modelos.md`](modelos.md) | Los diez modelos restantes y las propiedades y métodos de autenticación de `Usuario` |
| [`policies.md`](policies.md) | `ClientePolicy`, `FacturaPolicy` y `FacturaServicioPolicy` |
| [`consola-y-configuracion.md`](consola-y-configuracion.md) | Comando `app:make-administrator`, `routes/console.php` y estructura de `config/permisos.php` |

Convenciones:

- Los fragmentos de código son copias literales, con su archivo y líneas.
- “Sin ruta” significa que ningún archivo de rutas llega al método.
- “Ningún llamador” significa que no se encontró código del proyecto que lo invoque.
- Ningún comportamiento se ejecutó: los códigos HTTP y resultados descritos provienen de la lectura del código.
