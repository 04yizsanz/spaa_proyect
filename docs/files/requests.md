# Referencia: requests de validación

> **Base de verificación.** Contrastado con `spaa_codigo.zip` (5 de noviembre de 2026). Fragmentos copiados sin modificaciones. Ninguna validación se ejecutó: los resultados descritos (422, 403) corresponden a la lectura del código y al funcionamiento documentado de Laravel.

Este documento completa la [capa 2 de `backend/capas.md`](../capas.md#6-capa-2--request). `StoreCitaRequest`, `UpdateCitaRequest`, `StoreUsuarioRequest`, `StoreFacturaRequest` y los dos requests de `FacturaServicio` están explicados allí.

## Funcionamiento común

Todas las clases extienden `Illuminate\Foundation\Http\FormRequest` y tienen tres métodos:

| Método | Qué hace | En estos archivos |
| --- | --- | --- |
| `authorize(): bool` | Decide si la petición puede continuar; `false` → 403 | Devuelve `true`, salvo en `UpdateUsuarioRequest` |
| `rules(): array` | Reglas por campo; si una falla → 422 | Ver cada clase |
| `messages(): array` | Textos en español para los errores | Presente en todas; no se repite aquí su contenido, solo se señala cuándo falta un mensaje relevante |

**Convención de nombres.** `Store` + entidad + `Request` valida la creación (`POST`); `Update` + entidad + `Request` valida la edición (`PUT`/`PATCH`). En los `Update`, la regla `sometimes` hace que un campo solo se valide si viene en la petición; `sometimes|required` significa “si viene, no puede estar vacío”.

**Reglas que se repiten.**

| Regla | Significado |
| --- | --- |
| `required` / `nullable` | Obligatorio / puede venir vacío |
| `integer`, `numeric`, `string`, `boolean`, `date`, `email` | Tipo esperado |
| `max:N` / `min:N` | Longitud máxima de texto o valor mínimo numérico |
| `gt:0` / `gte:0` | Mayor que 0 / mayor o igual que 0 |
| `in:a,b,c` | Solo esos valores |
| `exists:tabla,columna` | El valor debe existir en esa columna. No excluye filas borradas lógicamente |
| `unique:tabla,columna` / `Rule::unique(...)->ignore($id, 'llave')` | No puede repetirse; al editar se ignora la propia fila |
| `before:today` / `before_or_equal:today` | Fecha anterior a hoy / no posterior a hoy |
| `date_format:Y-m-d H:i:s` | Formato exacto de fecha y hora |

---

## 1. Clientes

```php
    public function rules(): array
    {
        return [
            'usuario_id' => ['required', 'integer', 'exists:usuarios,usuario_id', 'unique:clientes,usuario_id'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'preferencias' => ['nullable', 'string'],
        ];
    }
```

Fuente: `backend/app/Http/Requests/Cliente/StoreClienteRequest.php` L14–21.

```php
    public function rules(): array
    {
        $clienteId = $this->route('cliente');

        return [
            'usuario_id' => [
                'required',
                'integer',
                'exists:usuarios,usuario_id',
                Rule::unique('clientes', 'usuario_id')
                    ->ignore($clienteId, 'cliente_id'),
            ],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'preferencias' => ['nullable', 'string'],
        ];
    }
```

Fuente: `backend/app/Http/Requests/Cliente/UpdateClienteRequest.php` L15–30.

| Campo | Al crear | Al editar | Significado |
| --- | --- | --- | --- |
| `usuario_id` | Obligatorio, existente en `usuarios`, único en `clientes` | **Obligatorio también al editar**; único ignorando la ficha actual | Cada usuario tiene como máximo una ficha |
| `fecha_nacimiento` | Opcional, fecha anterior a hoy | Igual | — |
| `preferencias` | Opcional, texto | Igual | Texto libre sin longitud máxima en la validación |

**Variable `$clienteId` (Update, L17).** `$this->route('cliente')` obtiene el `{cliente}` de la URL `PUT /api/clientes/{cliente}`, para ignorar la propia ficha en la regla de unicidad. Como `usuario_id` no tiene `sometimes`, una edición que solo cambie las preferencias también debe enviar el `usuario_id` (observación CAP-20). Usado por `ClienteController::store` y `update`.

## 2. Empleados

```php
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'documento' => ['required', 'string', 'max:20', 'unique:empleados,documento'],
            'correo' => ['nullable', 'email', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'rol' => ['required', 'in:estilista,Estetisista,recepcionista,admin'],
            'salario' => ['required', 'numeric', 'gt:0'],
            'fecha_contratacion' => ['required', 'date', 'before_or_equal:today'],
            'disponibilidad' => ['nullable', 'boolean'],
        ];
    }
```

Fuente: `backend/app/Http/Requests/Empleado/StoreEmpleadoRequest.php` L14–27.

```php
    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'string', 'max:50'],
            'apellido' => ['sometimes', 'string', 'max:50'],
            // El parámetro de ruta del apiResource es {empleado} (antes se leía 'id' y no se excluía el propio registro).
            'documento' => ['sometimes', 'string', 'max:20', Rule::unique('empleados', 'documento')->ignore($this->route('empleado'), 'empleado_id')],
            'correo' => ['nullable', 'email', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'rol' => ['sometimes', 'in:estilista,Estetisista,recepcionista,admin'],
            'salario' => ['sometimes', 'numeric', 'gt:0'],
            'fecha_contratacion' => ['sometimes', 'date', 'before_or_equal:today'],
            'disponibilidad' => ['nullable', 'boolean'],
        ];
    }
```

Fuente: `backend/app/Http/Requests/Empleado/UpdateEmpleadoRequest.php` L14–28.

| Campo | Regla al crear | Significado en el spa |
| --- | --- | --- |
| `nombre`, `apellido` | Obligatorios, hasta 50 caracteres | — |
| `documento` | Obligatorio, hasta 20, único en `empleados` | Al editar, `Rule::unique(...)->ignore($this->route('empleado'), 'empleado_id')`; el comentario L19 explica que antes se leía `id` y no se excluía el propio registro |
| `correo`, `telefono` | Opcionales | Datos de contacto, ocultos según permisos en la respuesta |
| `rol` | Obligatorio, en `estilista`, `Estetisista`, `recepcionista`, `admin` | Cargo del empleado. **No** es el rol de acceso de `roles` (OBS-08). La mayúscula y la escritura de `Estetisista` deben respetarse exactamente |
| `salario` | Obligatorio, número > 0 | Oculto sin `empleados.ver_salario` |
| `fecha_contratacion` | Obligatoria, no posterior a hoy | — |
| `disponibilidad` | Opcional, booleano | Controla si el empleado puede recibir citas nuevas (`StoreCitaRequest`) |

Al editar, todos llevan `sometimes` salvo `correo`, `telefono` y `disponibilidad`, que son `nullable`. Usados por `EmpleadoController::store` y `update`.

## 3. Servicios

```php
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],

            'duracion_min' => [
                'required',
                'integer',
                'gt:0',
            ],

            'precio' => [
                'required',
                'numeric',
                'gte:0',
            ],

            'descripcion' => [
                'nullable',
                'string',
            ],

            'activo' => [
                'nullable',
                'boolean',
            ],
        ];
    }
```

Fuente: `backend/app/Http/Requests/Servicio/StoreServicioRequest.php` L14–41.

| Campo | Al crear | Al editar | Significado |
| --- | --- | --- | --- |
| `nombre` | Obligatorio, hasta 100 | `sometimes` | No se exige único |
| `duracion_min` | Obligatorio, entero > 0 | `sometimes` | Duración en minutos; **no** la usa la verificación de disponibilidad de citas (OBS-07) |
| `precio` | Obligatorio, número ≥ 0 | `sometimes` | Se copia a las líneas de factura al crearlas |
| `descripcion` | Opcional | Opcional | — |
| `activo` | Opcional, booleano | `sometimes` | Si es `false`, el servicio no se puede usar en citas nuevas por la API |

`UpdateServicioRequest.php` L14–45 tiene la misma estructura con `sometimes`. Usados por `ServicioController`.

## 4. Pagos

```php
    public function rules(): array
    {
        return [
            'codigo_cita'    => 'required|integer|exists:citas,codigo_cita',
            'monto'          => 'required|numeric|gt:0',
            'metodo'         => 'required|string|in:efectivo,tarjeta,transferencia,pse',
            'fecha_hora'     => 'required|date',
            'estado'         => 'sometimes|string|in:pendiente,aprobado,rechazado',
        ];
    }
```

Fuente: `backend/app/Http/Requests/Pago/StorePagoRequest.php` L18–27.

| Campo | Al crear | Significado |
| --- | --- | --- |
| `codigo_cita` | Obligatorio, existente en `citas.codigo_cita` | El pago se asocia al **código** de la cita, no a su llave primaria. `exists` incluye citas borradas lógicamente |
| `monto` | Obligatorio, > 0 | — |
| `metodo` | `efectivo`, `tarjeta`, `transferencia` o `pse` | Solo se registra el método; no hay integración con pasarelas |
| `fecha_hora` | Obligatoria | — |
| `estado` | Opcional: `pendiente`, `aprobado`, `rechazado` | Si no es `pendiente`, `PagoService::store` exige `pagos.aprobar` |

`UpdatePagoRequest.php` L18–27: mismos campos con `sometimes`. Permite cambiar monto, método, fecha y cita mientras el pago esté pendiente (`PagoPolicy::update`).

## 5. Facturas: edición

```php
    public function rules(): array
    {
        return [
            'cliente_id' => 'sometimes|integer|exists:clientes,cliente_id',
            'fecha_hora' => 'sometimes|date',
            'impuestos' => 'sometimes|numeric|min:0',
            'pdf_url' => 'sometimes|nullable|string|max:255',
            'subtotal' => 'prohibited',
            'total' => 'prohibited',
        ];
    }
```

Fuente: `backend/app/Http/Requests/Factura/UpdateFacturaRequest.php` L18–28.

Igual que `StoreFacturaRequest` (sección 6.10 de `capas.md`) pero con `sometimes`. `subtotal` y `total` siguen prohibidos. Usado por `FacturaController::update`.

## 6. Proveedores

```php
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'contacto' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],
            'registro_tributario' => [
                'required',
                'string',
                'max:20',
                Rule::unique('proveedor', 'registro_tributario'),
            ],
        ];
    }
```

Fuente: `backend/app/Http/Requests/Proveedor/StoreProveedorRequest.php` L14–27.

```php
    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'contacto' => ['sometimes', 'nullable', 'string', 'max:50'],
            'email' => ['sometimes', 'nullable', 'email', 'max:100'],
            'registro_tributario' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                // apiResource('proveedores') genera el parámetro {proveedore} (singular automático de Laravel).
                Rule::unique('proveedor', 'registro_tributario')->ignore($this->route('proveedore'), 'proveedor_id'),
            ],
        ];
    }
```

Fuente: `backend/app/Http/Requests/Proveedor/UpdateProveedorRequest.php` L14–29.

| Campo | Regla | Significado |
| --- | --- | --- |
| `nombre` | Obligatorio, hasta 100 | — |
| `contacto` | Opcional, hasta 50 | Persona de contacto |
| `email` | Opcional, formato de correo | — |
| `registro_tributario` | Obligatorio, hasta 20, único en `proveedor` | NIT u otro registro. Al editar, ignora el proveedor actual leyendo `$this->route('proveedore')`; el comentario L25 explica que `proveedore` es el singular que Laravel genera para `proveedores` |

## 7. Productos

```php
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'precio' => ['required', 'numeric', 'min:0'],
            'fecha_registro' => ['required', 'date'],
            'proveedor_id' => ['required', 'integer', 'exists:proveedor,proveedor_id'],
        ];
    }
```

Fuente: `backend/app/Http/Requests/Producto/StoreProductoRequest.php` L14–23.

| Campo | Regla | Significado |
| --- | --- | --- |
| `nombre` | Obligatorio, hasta 100 | — |
| `cantidad` | Entero ≥ 0 | Existencias. Solo cambia por esta vía (no por movimientos) |
| `precio` | Número ≥ 0 | — |
| `fecha_registro` | Fecha obligatoria | — |
| `proveedor_id` | Existente en `proveedor` | — |

`UpdateProductoRequest.php` L14–23: los mismos con `sometimes|required`.

## 8. Movimientos de inventario

```php
    public function rules(): array
    {
        return [
            'tipo' => ['required', 'in:entrada,salida'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'fecha_hora' => ['required', 'date_format:Y-m-d H:i:s'],
            'motivo' => ['nullable', 'string', 'max:100'],
            'producto_id' => ['required', 'integer', 'exists:producto,producto_id'],
        ];
    }
```

Fuente: `backend/app/Http/Requests/MovimientoInventario/StoreMovimientoInventarioRequest.php` L13–22.

| Campo | Regla | Significado |
| --- | --- | --- |
| `tipo` | `entrada` o `salida` | Sentido del movimiento; no modifica el stock (CAP-09) |
| `cantidad` | Entero ≥ 1 | — |
| `fecha_hora` | Formato exacto `AAAA-MM-DD HH:MM:SS` | Un valor como `2026-11-05 10:00` (sin segundos) falla |
| `motivo` | Opcional, hasta 100 | — |
| `producto_id` | Existente en `producto` | — |

`UpdateMovimientoInventarioRequest.php` L13–22: los mismos con `sometimes`.

## 9. Roles

```php
    public function rules(): array
    {
        return [
            // La columna roles.nombre es VARCHAR(50) y única.
            'nombre' => ['required', 'string', 'max:50', 'unique:roles,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],
        ];
    }
```

Fuente: `backend/app/Http/Requests/Rol/StoreRolRequest.php` L17–25.

```php
    public function rules(): array
    {
        // apiResource('roles') genera el parámetro {role}; la PK de la tabla es rol_id.
        $rolId = $this->route('role');

        return [
            'nombre' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('roles', 'nombre')->ignore($rolId, 'rol_id'),
            ],
            'descripcion' => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],
            'estado' => [
                'sometimes',
                'boolean'
            ],
        ];
    }
```

Fuente: `backend/app/Http/Requests/Rol/UpdateRolRequest.php` L15–38.

| Campo | Al crear | Al editar | Significado |
| --- | --- | --- | --- |
| `nombre` | Obligatorio, hasta 50, único | `sometimes`, único ignorando el rol actual | Debe coincidir **exactamente** con una llave de `config/permisos.php` para que el rol tenga permisos |
| `descripcion` | Opcional, hasta 255 | `sometimes` | — |
| `estado` | Obligatorio, booleano | `sometimes` | `false` deshabilita a todos los usuarios del rol |

**Variable `$rolId` (Update, L18).** `$this->route('role')`: el comentario L17 explica que `apiResource('roles')` genera el parámetro `{role}` (singular en inglés). Ambos requests devuelven `true` en `authorize()`; la protección es el middleware `EnsureAdministrator`.

## 10. Usuarios: edición

```php
    public function authorize(): bool
    {
        // Gate 'administrar-sistema': cuenta y rol activos con el permiso 'sistema.administrar'.
        return (bool) $this->user()?->can('administrar-sistema');
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario'); // ajusta si el parámetro de ruta se llama distinto
        $roleRules = ['required', 'integer', 'exists:roles,rol_id'];
        $adminRoleId = Rol::where('nombre', 'Administrador')->value('rol_id');

        if ($adminRoleId !== null && (int) $this->input('rol_id') === (int) $adminRoleId) {
            $roleRules[] = Rule::unique('usuarios', 'rol_id')
                ->ignore($usuarioId, 'usuario_id');
        }

        return [
            'rol_id' => $roleRules,
            'nombre' => ['required', 'string', 'max:80'],
            'apellido' => ['required', 'string', 'max:80'],
            'documento' => [
                'required', 'string', 'max:20',
                Rule::unique('usuarios', 'documento')->ignore($usuarioId, 'usuario_id'),
            ],
            'email' => [
                'required', 'string', 'email', 'max:150',
                Rule::unique('usuarios', 'email')->ignore($usuarioId, 'usuario_id'),
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', Password::defaults()],
            'estado' => ['required', 'boolean'],
        ];
    }
```

Fuente: `backend/app/Http/Requests/Usuario/UpdateUsuarioRequest.php` L12–45.

| Aspecto | Detalle |
| --- | --- |
| `authorize()` (L12–16) | Igual que `StoreUsuarioRequest`: Gate `administrar-sistema`. |
| `$usuarioId` (L20) | `{usuario}` de la URL. El comentario de la línea sugiere ajustarlo si el parámetro cambia de nombre. |
| `$roleRules` y `$adminRoleId` (L21–27) | Si el rol enviado es Administrador, debe ser único entre usuarios **ignorando al usuario editado** (así el administrador actual puede editarse sin cambiar de rol). |
| Campos obligatorios | `rol_id`, `nombre`, `apellido`, `documento`, `email` y `estado`: no llevan `sometimes`, así que cada edición debe enviarlos todos. |
| `documento` y `email` | Únicos ignorando al usuario actual. |
| `password` | Opcional; si llega, debe cumplir la política. Si llega vacía, `UsuarioRepository::update` conserva la actual. |
