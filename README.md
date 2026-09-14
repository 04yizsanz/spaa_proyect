# Diccionario de Datos
## Sistema de Gestión de Spa & Salón de Belleza

Incluye tres niveles de documentación (entidades, atributos y relaciones), con tipo de dato MySQL y equivalente en migraciones de Laravel para cada campo. Los campos marcados como **AMBIGUO** requieren confirmación del equipo antes de cerrar el modelo al 100%.

**Módulo diferenciador:** Realidad Aumentada para prueba virtual de cortes, peinados y colores. Módulo de predicción de demanda con IA.

---

## NIVEL 1 — Diccionario de Entidades

| Tabla | Descripción | Tipo | Observaciones |
|---|---|---|---|
| TCliente | Persona que adquiere servicios del spa | Fuerte | Entidad base del sistema |
| TEmpleado | Personal que presta los servicios (estilistas, terapeutas, etc.) | Fuerte | - |
| TServicio | Catálogo de servicios ofrecidos (cortes, tratamientos, etc.) | Fuerte | - |
| TCita | Reserva de un servicio con un empleado, para un cliente, en fecha/hora | Fuerte | Nodo central: conecta Cliente, Empleado y Servicio |
| TPago | Registro de pago asociado a una cita | Fuerte | Relación 1:1 o 1:N con TCita |
| TFactura | Documento de cobro emitido a un cliente | Fuerte | Se relaciona con servicios vía TFacturaServicio |
| TFacturaServicio | Detalle de servicios incluidos en una factura | Débil (intermedia) | Resuelve relación N:M entre TFactura y TServicio |
| TProveedor | Empresa o persona que suministra productos | Fuerte | - |
| TProducto | Insumos o productos vendidos/usados en el spa | Fuerte | - |
| TMovimientoInventario | Registro de entradas/salidas de inventario | Fuerte | - |
| TCampanaMarketing | Campañas de promoción del negocio | Fuerte | Sin FK hacia otras tablas: entidad aislada |
| TPrediccionIA | Resultados de predicciones de demanda/compras generadas por IA | Fuerte (con reservas) | Sin FK: no se sabe a qué servicio/producto corresponde |
| TVisualizacionAR | Registro de sesiones del módulo de Realidad Aumentada | Fuerte (con reservas) | Sin FK hacia TCliente |

---

## NIVEL 2 — Diccionario de Atributos

Cada tabla incluye tipo de dato en MySQL y su equivalente en migración Laravel. Las filas marcadas como **AMBIGUO** indican campo ambiguo, requiere definición.

### TCliente

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| ClienteId | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('cliente_id')` | No | PK | - | Identificador único del cliente | Autoincremental |
| Nombre | VARCHAR(50) | `$table->string('nombre',50)` | No | - | - | Nombre(s) del cliente | - |
| Apellido | VARCHAR(50) | `$table->string('apellido',50)` | No | - | - | Apellido(s) del cliente | - |
| Correo | VARCHAR(100) | `$table->string('correo',100)->nullable()->unique()` | Sí | - | - | Correo de contacto | Formato email; único si se usa para login |
| Telefono | VARCHAR(20) | `$table->string('telefono',20)->nullable()` | Sí | - | - | Teléfono de contacto | - |
| Documento | VARCHAR(20) | `$table->string('documento',20)->unique()` | No | - | - | Cédula/documento de identidad | Único (evita duplicados) |

### TEmpleado

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| EmpleadoId | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('empleado_id')` | No | PK | - | Identificador único del empleado | Autoincremental |
| Nombre | VARCHAR(50) | `$table->string('nombre',50)` | No | - | - | Nombre(s) del empleado | - |
| Apellido | VARCHAR(50) | `$table->string('apellido',50)` | No | - | - | Apellido(s) del empleado | - |
| Documento | VARCHAR(20) | `$table->string('documento',20)->unique()` | No | - | - | Documento de identidad | Único |
| Correo | VARCHAR(100) | `$table->string('correo',100)->nullable()` | Sí | - | - | Correo institucional/contacto | - |
| Telefono | VARCHAR(20) | `$table->string('telefono',20)->nullable()` | Sí | - | - | Teléfono de contacto | - |
| Rol | ENUM('estilista','terapeuta','recepcionista','admin') | `$table->enum('rol',[...])` | No | - | - | Función que desempeña en el spa | **AMBIGUO:** definir catálogo exacto de roles |
| Salario | DECIMAL(10,2) | `$table->decimal('salario',10,2)` | No | - | - | Salario del empleado | > 0 |
| FechaContratacion | DATE | `$table->date('fecha_contratacion')` | No | - | - | Fecha de ingreso | <= fecha actual |
| Disponibilidad | BOOLEAN o JSON | `$table->boolean(...)` o `$table->json(...)` | Sí | - | - | Indica si/cuándo el empleado puede atender | **AMBIGUO:** requiere aclaración del formato |

### TServicio

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| ServicioId | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('servicio_id')` | No | PK | - | Identificador único del servicio | Autoincremental |
| Nombre | VARCHAR(100) | `$table->string('nombre',100)` | No | - | - | Nombre del servicio | - |
| DuracionMin | SMALLINT UNSIGNED | `$table->unsignedSmallInteger('duracion_min')` | No | - | - | Duración estimada en minutos | > 0 |
| Precio | DECIMAL(10,2) | `$table->decimal('precio',10,2)` | No | - | - | Precio base del servicio | >= 0 |
| Descripcion | TEXT | `$table->text('descripcion')->nullable()` | Sí | - | - | Detalle del servicio | - |
| Activo | TINYINT(1) | `$table->boolean('activo')->default(true)` | No | - | - | Servicio disponible o no | Default: true |

### TCita

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| CodigoCita | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('codigo_cita')` | No | PK | - | Identificador único de la cita | Autoincremental |
| Fecha | DATE | `$table->date('fecha')` | No | - | - | Fecha de la cita | >= fecha actual al crearse |
| Hora | TIME | `$table->time('hora')` | No | - | - | Hora de la cita | Dentro del horario del spa |
| Estado | ENUM('pendiente','confirmada','completada','cancelada') | `$table->enum('estado',[...])->default('pendiente')` | No | - | - | Estado actual de la cita | **AMBIGUO:** confirmar catálogo exacto |
| ClienteId | BIGINT UNSIGNED | `$table->foreignId('cliente_id')->constrained()` | No | FK | TCliente.ClienteId | Cliente que agenda la cita | - |
| EmpleadoId | BIGINT UNSIGNED | `$table->foreignId('empleado_id')->constrained()` | No | FK | TEmpleado.EmpleadoId | Empleado asignado | Debe estar disponible en fecha/hora |
| ServicioId | BIGINT UNSIGNED | `$table->foreignId('servicio_id')->constrained()` | No | FK | TServicio.ServicioId | Servicio solicitado | - |

### TPago

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| PagoId | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('pago_id')` | No | PK | - | Identificador único del pago | Autoincremental |
| Monto | DECIMAL(10,2) | `$table->decimal('monto',10,2)` | No | - | - | Valor pagado | > 0 |
| Metodo | ENUM('efectivo','tarjeta','transferencia','pse') | `$table->enum('metodo',[...])` | No | - | - | Medio de pago usado | **AMBIGUO:** confirmar catálogo exacto |
| FechaHora | DATETIME | `$table->dateTime('fecha_hora')` | No | - | - | Fecha y hora del pago | - |
| Estado | ENUM('pendiente','aprobado','rechazado') | `$table->enum('estado',[...])->default('pendiente')` | No | - | - | Estado del pago | **AMBIGUO:** confirmar catálogo exacto |
| CodigoCita | BIGINT UNSIGNED | `$table->foreignId('codigo_cita')->constrained()` | No | FK | TCita.CodigoCita | Cita a la que corresponde el pago | Definir si admite pagos parciales (1:N) o es 1:1 |

### TFactura

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| FacturaId | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('factura_id')` | No | PK | - | Identificador único de la factura | Autoincremental |
| FechaHora | DATETIME | `$table->dateTime('fecha_hora')` | No | - | - | Fecha y hora de emisión | - |
| Subtotal | DECIMAL(10,2) | `$table->decimal('subtotal',10,2)` | No | - | - | Suma antes de impuestos | >= 0 |
| Impuestos | DECIMAL(10,2) | `$table->decimal('impuestos',10,2)` | No | - | - | Valor de impuestos aplicados | >= 0 |
| Total | DECIMAL(10,2) | `$table->decimal('total',10,2)` | No | - | - | Subtotal + Impuestos | Total = Subtotal + Impuestos |
| PdfUrl | VARCHAR(255) | `$table->string('pdf_url')->nullable()` | Sí | - | - | Ruta/URL del PDF generado | - |
| ClienteId | BIGINT UNSIGNED | `$table->foreignId('cliente_id')->constrained()` | No | FK | TCliente.ClienteId | Cliente facturado | - |

### TFacturaServicio (tabla intermedia N:M)

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| FacturaId | BIGINT UNSIGNED | `$table->foreignId('factura_id')->constrained()` | No | PK comp./FK | TFactura.FacturaId | Factura a la que pertenece el detalle | - |
| ServicioId | BIGINT UNSIGNED | `$table->foreignId('servicio_id')->constrained()` | No | PK comp./FK | TServicio.ServicioId | Servicio incluido en la factura | - |
| Cantidad | SMALLINT UNSIGNED | `$table->unsignedSmallInteger('cantidad')` | No | - | - | Cantidad del servicio facturado | > 0 |
| PrecioUnitario | DECIMAL(10,2) | `$table->decimal('precio_unitario',10,2)` | No | - | - | Precio en el momento de facturar | >= 0; se guarda aparte para no perder histórico |

### TProveedor

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| ProveedorId | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('proveedor_id')` | No | PK | - | Identificador único del proveedor | Autoincremental |
| Nombre | VARCHAR(100) | `$table->string('nombre',100)` | No | - | - | Razón social o nombre | - |
| Contacto | VARCHAR(50) | `$table->string('contacto',50)->nullable()` | Sí | - | - | Persona de contacto | - |
| Email | VARCHAR(100) | `$table->string('email',100)->nullable()` | Sí | - | - | Correo de contacto | Formato email |
| RegistroTributario | VARCHAR(20) | `$table->string('registro_tributario',20)->unique()` | No | - | - | NIT/RUT u otro ID fiscal | Único |

### TProducto

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| ProductoId | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('producto_id')` | No | PK | - | Identificador único del producto | Autoincremental |
| Nombre | VARCHAR(100) | `$table->string('nombre',100)` | No | - | - | Nombre del producto | - |
| Cantidad | INT UNSIGNED | `$table->unsignedInteger('cantidad')->default(0)` | No | - | - | Stock actual disponible | >= 0 |
| Precio | DECIMAL(10,2) | `$table->decimal('precio',10,2)` | No | - | - | Precio de venta/costo | **AMBIGUO:** aclarar si es costo, venta o ambos |
| FechaRegistro | DATE | `$table->date('fecha_registro')` | No | - | - | Fecha de alta del producto | - |
| ProveedorId | BIGINT UNSIGNED | `$table->foreignId('proveedor_id')->nullable()->constrained()` | Sí | FK | TProveedor.ProveedorId | Proveedor del producto | - |

### TMovimientoInventario

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| MovInvId | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('mov_inv_id')` | No | PK | - | Identificador único del movimiento | Autoincremental |
| Tipo | ENUM('entrada','salida') | `$table->enum('tipo',['entrada','salida'])` | No | - | - | Tipo de movimiento | Entrada / Salida |
| Cantidad | INT UNSIGNED | `$table->unsignedInteger('cantidad')` | No | - | - | Unidades movidas | > 0 |
| FechaHora | DATETIME | `$table->dateTime('fecha_hora')` | No | - | - | Momento del movimiento | - |
| Motivo | VARCHAR(100) | `$table->string('motivo',100)->nullable()` | Sí | - | - | Razón del movimiento | Ej: Compra, Venta, Ajuste, Merma |
| ProductoId | BIGINT UNSIGNED | `$table->foreignId('producto_id')->constrained()` | No | FK | TProducto.ProductoId | Producto afectado | - |

### TCampanaMarketing (entidad aislada)

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| CampanaId | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('campana_id')` | No | PK | - | Identificador único de la campaña | Autoincremental |
| Nombre | VARCHAR(100) | `$table->string('nombre',100)` | No | - | - | Nombre de la campaña | - |
| Tipo | VARCHAR(30) | `$table->string('tipo',30)->nullable()` | Sí | - | - | Categoría de la campaña | **AMBIGUO:** ej. Descuento, Referidos, Temporada |
| Canal | VARCHAR(30) | `$table->string('canal',30)->nullable()` | Sí | - | - | Medio de difusión | **AMBIGUO:** ej. Email, SMS, Redes Sociales |
| Inicio | DATE | `$table->date('inicio')` | No | - | - | Fecha de inicio | - |
| Fin | DATE | `$table->date('fin')->nullable()` | Sí | - | - | Fecha de finalización | Fin >= Inicio |
| Estado | VARCHAR(20) | `$table->string('estado',20)` | No | - | - | Estado de la campaña | **AMBIGUO:** ej. Activa, Finalizada, Programada |

### TPrediccionIA (sin FK)

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| PrediccionId | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('prediccion_id')` | No | PK | - | Identificador único de la predicción | Autoincremental |
| Periodo | VARCHAR(20) | `$table->string('periodo',20)` | No | - | - | Periodo que cubre la predicción | Ej: '2026-09' |
| ServiciosTop | JSON | `$table->json('servicios_top')->nullable()` | Sí | - | - | Servicios más demandados previstos | **AMBIGUO:** candidato a tabla N:M con TServicio |
| DemandaPorFranja | JSON | `$table->json('demanda_por_franja')->nullable()` | Sí | - | - | Demanda estimada por franja horaria | **AMBIGUO:** candidato a tabla propia |
| RecomendacionesCompra | JSON | `$table->json('recomendaciones_compra')->nullable()` | Sí | - | - | Recomendaciones de compra de insumos | **AMBIGUO:** posible relación con TProducto |

### TVisualizacionAR (sin FK a Cliente)

| Atributo | Tipo MySQL | Tipo Laravel (migration) | Nulo | Llave | Referencia | Descripción funcional | Dominio / Regla de negocio |
|---|---|---|---|---|---|---|---|
| VisualizacionId | BIGINT UNSIGNED AUTO_INCREMENT | `$table->id('visualizacion_id')` | No | PK | - | Identificador único de la sesión AR | Autoincremental |
| ImagenEntrada | VARCHAR(255) | `$table->string('imagen_entrada')` | No | - | - | Ruta/URL de la imagen original | - |
| EstiloSeleccionado | VARCHAR(50) | `$table->string('estilo_seleccionado',50)->nullable()` | Sí | - | - | Estilo probado (corte, color, peinado) | **AMBIGUO:** catálogo fijo o texto libre |
| Parametros | JSON | `$table->json('parametros')->nullable()` | Sí | - | - | Parámetros técnicos del render | Probablemente JSON |
| ImagenResultado | VARCHAR(255) | `$table->string('imagen_resultado')->nullable()` | Sí | - | - | Ruta/URL de la imagen generada | - |

---

## NIVEL 3 — Diccionario de Relaciones

| Entidad origen | Entidad destino | Cardinalidad | Llave foránea | Descripción de la relación |
|---|---|---|---|---|
| TCliente | TCita | 1:N | TCita.ClienteId | Un cliente puede tener muchas citas |
| TEmpleado | TCita | 1:N | TCita.EmpleadoId | Un empleado puede atender muchas citas |
| TServicio | TCita | 1:N | TCita.ServicioId | Un servicio puede solicitarse en muchas citas |
| TCita | TPago | 1:N (sugerido) | TPago.CodigoCita | Una cita puede tener uno o varios pagos |
| TCliente | TFactura | 1:N | TFactura.ClienteId | Un cliente puede recibir muchas facturas |
| TFactura | TServicio | N:M | TFacturaServicio (FacturaId, ServicioId) | Una factura incluye varios servicios y viceversa |
| TProveedor | TProducto | 1:N | TProducto.ProveedorId | Un proveedor suministra muchos productos |
| TProducto | TMovimientoInventario | 1:N | TMovimientoInventario.ProductoId | Un producto tiene muchos movimientos de inventario |

### Relaciones no definidas / a confirmar

| Tabla | Observación |
|---|---|
| TVisualizacionAR | No tiene FK hacia TCliente, pero cada sesión de AR debería pertenecer a un cliente. |
| TPrediccionIA | No tiene FK hacia TServicio ni TProducto, aunque sus campos lo sugieren. |
| TCampanaMarketing | No tiene ninguna FK; si se dirige a clientes o promociona servicios, faltan relaciones. |

---

## Resumen General

| Métrica | Valor |
|---|---|
| Total de tablas | 13 |
| Total de atributos | 76 |
| Relaciones 1:N | 8 |
| Relaciones N:M | 1 (TFactura <-> TServicio, vía TFacturaServicio) |
| Tablas intermedias | 1 (TFacturaServicio) |
| Entidades débiles | 1 (TFacturaServicio) |
| Tablas sin relaciones definidas | 3 (TCampanaMarketing, TPrediccionIA, TVisualizacionAR) |

---

## Preguntas pendientes antes de cerrar el diccionario al 100%

| # | Pregunta |
|---|---|
| 1 | TEmpleado.Disponibilidad: ¿booleano simple o tabla de horarios? |
| 2 | TPrediccionIA (ServiciosTop, DemandaPorFranja, RecomendacionesCompra): ¿JSON está bien, o se normaliza en tablas separadas? |
| 3 | TVisualizacionAR: ¿falta la FK hacia TCliente? ¿EstiloSeleccionado es catálogo fijo o texto libre? |
| 4 | TCampanaMarketing: ¿se vincula a clientes o a servicios promocionados? |
| 5 | TProducto.Precio: ¿es costo, venta, o se necesitan ambos campos? |

> **Nota técnica Laravel:** se asume snake_case en columnas (Eloquent). Si se mantienen los nombres originales en PascalCase, declarar `$table` y `$primaryKey` manualmente en cada modelo.