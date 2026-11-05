<?php

/*
|--------------------------------------------------------------------------
| Permisos por rol — Asha Spa
|--------------------------------------------------------------------------
|
| Mapa de permisos por NOMBRE de rol (columna roles.nombre).
|
| Regla de denegación por defecto:
|  - Un rol que no figure aquí (nuevo, renombrado o desconocido) no tiene
|    ningún permiso y su cuenta no puede operar.
|  - Un permiso que no esté en la lista del rol no se concede.
|  - '*' concede todos los permisos (solo Administrador).
|
| Además, el usuario y su rol deben estar activos (columna estado).
| Para habilitar un rol nuevo, añádalo aquí de forma explícita.
|
| Catálogo de permisos:
|   sistema.administrar     Gestión de roles y usuarios.
|   clientes.ver            Directorio completo de clientes.
|   clientes.gestionar      Crear, editar y eliminar clientes.
|   clientes.propio         Consultar el propio perfil de cliente.
|   citas.ver_todas         Agenda completa (solo lectura).
|   citas.gestionar         Crear y editar cualquier cita, reasignarla y cambiar su estado.
|   citas.eliminar          Eliminar citas.
|   citas.propias           Consultar, agendar y cancelar las propias citas.
|   servicios.ver           Catálogo de servicios.
|   servicios.gestionar     Crear, editar y eliminar servicios.
|   empleados.ver           Datos públicos del personal (nombre, rol, disponibilidad).
|   empleados.ver_contacto  Documento, correo y teléfono del personal.
|   empleados.ver_salario   Salario del personal.
|   empleados.gestionar     Crear, editar y eliminar empleados.
|   pagos.ver_todos         Todos los pagos.
|   pagos.propios           Pagos de las propias citas.
|   pagos.registrar         Registrar y editar pagos pendientes.
|   pagos.aprobar           Aprobar o rechazar pagos.
|   facturas.ver_todas      Todas las facturas y su detalle.
|   facturas.propias        Facturas propias y su detalle.
|   facturas.gestionar      Emitir facturas y editar su detalle.
|   inventario.ver          Proveedores, productos y movimientos (lectura).
|   inventario.gestionar    Proveedores, productos y movimientos (escritura).
|
| Los registros financieros (pagos, facturas y su detalle) no se eliminan
| para ningún rol: lo impiden las Policies, no este mapa.
|
*/

return [

    'roles' => [

        'Administrador' => ['*'],

        // "Gestiona clientes y citas" (RolSeeder). Sin acceso financiero.
        'Recepcionista' => [
            'clientes.ver',
            'clientes.gestionar',
            'citas.ver_todas',
            'citas.gestionar',
            'servicios.ver',
            'empleados.ver',
            'empleados.ver_contacto',
        ],

        // Consultan la agenda sin modificarla. Sin salarios ni finanzas.
        'Estilista' => [
            'citas.ver_todas',
            'servicios.ver',
            'empleados.ver',
        ],

        'Esteticista' => [
            'citas.ver_todas',
            'servicios.ver',
            'empleados.ver',
        ],

        // Solo sus propios datos.
        'Cliente' => [
            'clientes.propio',
            'citas.propias',
            'pagos.propios',
            'facturas.propias',
            'servicios.ver',
            'empleados.ver',
        ],
    ],

];
