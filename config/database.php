<?php

/**
 * ARCHIVO: config/database.php
 *
 * PROPÓSITO: Archivo de configuración central de conexiones a bases de datos
 * del framework Laravel. Define los parámetros de conexión para cada motor
 * de base de datos soportado por la aplicación, así como la configuración
 * del cliente Redis y del sistema de migraciones.
 *
 * Este archivo retorna un arreglo asociativo de PHP que Laravel registra
 * en su contenedor de servicios al iniciar la aplicación. Sus valores son
 * accedidos mediante el helper `config('database.*')`.
 *
 * La función `env()` lee variables del archivo `.env` del entorno actual,
 * permitiendo que la misma base de código funcione con configuraciones
 * distintas en desarrollo, pruebas y producción sin modificar este archivo.
 */

// Importa la clase Str del paquete de soporte de Laravel.
// Se utiliza para generar el prefijo del caché de Redis mediante Str::slug().
use Illuminate\Support\Str;

// Importa la clase Mysql del espacio de nombres Pdo, disponible desde PHP 8.4.
// Se utiliza para referenciar la constante ATTR_SSL_CA de forma compatible
// con las versiones más recientes de PHP.
use Pdo\Mysql;

// La instrucción return devuelve el arreglo de configuración completo
// al sistema de configuración de Laravel. Este arreglo es registrado
// bajo la clave 'database' en el contenedor de configuración del framework.
return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Aquí se especifica qué conexión de las definidas en 'connections'
    | se utiliza como predeterminada para todas las operaciones de base de datos
    | que no especifiquen una conexión explícita.
    |
    */

    // 'default': clave del arreglo que almacena el nombre de la conexión
    // activa por defecto. La función env() lee la variable de entorno
    // DB_CONNECTION del archivo .env. Si dicha variable no está definida,
    // utiliza 'mysql' como valor de respaldo (segundo argumento de env()).
    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Define todas las conexiones disponibles. Cada entrada es un arreglo
    | asociativo con los parámetros específicos de cada motor de base de datos.
    |
    */

    // 'connections': arreglo asociativo que contiene la configuración de
    // cada motor de base de datos soportado. La clave de cada entrada
    // es el identificador de la conexión (ej: 'sqlite', 'mysql').
    'connections' => [

        // ─────────────────────────────────────────────────────────────────────
        // CONEXIÓN SQLite
        // Motor de base de datos basado en archivos, ideal para desarrollo
        // local y pruebas automatizadas por no requerir un servidor dedicado.
        // ─────────────────────────────────────────────────────────────────────
        'sqlite' => [
            // 'driver': identifica el motor de base de datos a utilizar.
            'driver' => 'sqlite',

            // 'url': permite definir la conexión completa en formato DSN.
            // env('DB_URL') retorna null si la variable no está definida,
            // en cuyo caso se utilizan las demás claves individuales.
            'url' => env('DB_URL'),

            // 'database': ruta absoluta al archivo .sqlite que actúa como
            // base de datos. La función database_path() retorna la ruta
            // absoluta al directorio /database del proyecto. Si la variable
            // DB_DATABASE no está definida en .env, se usa 'database.sqlite'
            // como nombre de archivo por defecto.
            'database' => env('DB_DATABASE', database_path('database.sqlite')),

            // 'prefix': prefijo que se antepone a los nombres de las tablas.
            // La cadena vacía indica que no se aplica ningún prefijo.
            'prefix' => '',

            // 'foreign_key_constraints': activa la verificación de claves
            // foráneas en SQLite, que no las aplica por defecto.
            // env('DB_FOREIGN_KEYS', true) lo activa salvo indicación contraria.
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),

            // Las siguientes claves configuran parámetros de rendimiento y
            // concurrencia del motor SQLite.

            // 'busy_timeout': tiempo máximo en milisegundos que SQLite espera
            // antes de lanzar un error cuando la base de datos está bloqueada.
            // null utiliza el valor predeterminado del motor.
            'busy_timeout' => null,

            // 'journal_mode': modo del diario de transacciones de SQLite.
            // null utiliza el modo WAL o DELETE según la versión instalada.
            'journal_mode' => null,

            // 'synchronous': nivel de sincronización de escritura a disco.
            // null utiliza el valor predeterminado del motor.
            'synchronous' => null,

            // 'transaction_mode': modo de inicio de transacciones en SQLite.
            // 'DEFERRED' es el modo estándar: la transacción no adquiere
            // bloqueo hasta que se realiza la primera operación de lectura
            // o escritura.
            'transaction_mode' => 'DEFERRED',
        ],

        // ─────────────────────────────────────────────────────────────────────
        // CONEXIÓN MySQL
        // Motor de base de datos relacional de código abierto ampliamente
        // utilizado en entornos de producción.
        // ─────────────────────────────────────────────────────────────────────
        'mysql' => [
            // 'driver': identifica el motor MySQL.
            'driver' => 'mysql',

            // 'url': DSN completo opcional. Si se proporciona, tiene
            // precedencia sobre las claves individuales host/port/database.
            'url' => env('DB_URL'),

            // 'host': dirección IP o nombre de host del servidor MySQL.
            // El valor por defecto '127.0.0.1' apunta a la máquina local.
            'host' => env('DB_HOST', '127.0.0.1'),

            // 'port': puerto TCP donde escucha el servidor MySQL.
            // El puerto estándar de MySQL es 3306.
            'port' => env('DB_PORT', '3306'),

            // 'database': nombre del esquema de base de datos a utilizar.
            'database' => env('DB_DATABASE', 'laravel'),

            // 'username': usuario de MySQL con permisos sobre el esquema.
            'username' => env('DB_USERNAME', 'root'),

            // 'password': contraseña del usuario de MySQL.
            'password' => env('DB_PASSWORD', ''),

            // 'unix_socket': ruta al socket Unix para conexiones locales
            // sin pasar por TCP. La cadena vacía indica que no se utiliza.
            'unix_socket' => env('DB_SOCKET', ''),

            // 'charset': conjunto de caracteres utilizado en la conexión.
            // 'utf8mb4' soporta el rango completo de caracteres Unicode,
            // incluyendo emojis y caracteres de 4 bytes.
            'charset' => env('DB_CHARSET', 'utf8mb4'),

            // 'collation': reglas de comparación y ordenamiento de cadenas.
            // 'utf8mb4_unicode_ci' aplica comparación insensible a mayúsculas
            // basada en el estándar Unicode.
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),

            // 'prefix': prefijo para los nombres de tablas. La cadena vacía
            // indica que no se aplica prefijo.
            'prefix' => '',

            // 'prefix_indexes': cuando es true, los prefijos de tabla también
            // se aplican a los índices generados por las migraciones.
            'prefix_indexes' => true,

            // 'strict': activa el modo estricto de MySQL, que rechaza
            // valores inválidos o truncados en lugar de ajustarlos
            // silenciosamente.
            'strict' => true,

            // 'engine': motor de almacenamiento de MySQL (ej: InnoDB, MyISAM).
            // null utiliza el motor predeterminado del servidor.
            'engine' => null,

            // 'options': arreglo de opciones PDO para la conexión.
            // Se utiliza para configurar SSL cuando está disponible.
            // extension_loaded('pdo_mysql') verifica que la extensión PDO
            // de MySQL esté cargada en PHP.
            // array_filter() elimina los valores null del arreglo resultante.
            // La condición ternaria selecciona la constante correcta según
            // la versión de PHP: PHP_VERSION_ID >= 80500 indica PHP 8.5 o superior,
            // donde se usa la constante de la nueva clase Mysql; en versiones
            // anteriores se usa la constante de la clase PDO clásica.
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                (PHP_VERSION_ID >= 80500 ? Mysql::ATTR_SSL_CA : PDO::MYSQL_ATTR_SSL_CA) => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // ─────────────────────────────────────────────────────────────────────
        // CONEXIÓN MariaDB
        // Fork de MySQL compatible con su protocolo de comunicación.
        // La configuración es idéntica a MySQL salvo por el valor de 'driver'.
        // ─────────────────────────────────────────────────────────────────────
        'mariadb' => [
            // 'driver': identifica el motor MariaDB, que utiliza su propio
            // controlador en Laravel 11+ para diferenciarlo de MySQL.
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            // Misma lógica de opciones SSL descrita en la conexión 'mysql'.
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                (PHP_VERSION_ID >= 80500 ? Mysql::ATTR_SSL_CA : PDO::MYSQL_ATTR_SSL_CA) => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // ─────────────────────────────────────────────────────────────────────
        // CONEXIÓN PostgreSQL (pgsql)
        // Motor de base de datos relacional de código abierto con soporte
        // avanzado para tipos de datos y operaciones complejas.
        // ─────────────────────────────────────────────────────────────────────
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),

            // Puerto estándar de PostgreSQL: 5432.
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,

            // 'search_path': esquema de búsqueda predeterminado en PostgreSQL.
            // El valor 'public' es el esquema estándar donde se crean las tablas.
            'search_path' => 'public',

            // 'sslmode': modo de conexión SSL con el servidor PostgreSQL.
            // El valor 'prefer' intenta usar SSL pero acepta conexiones sin él.
            'sslmode' => env('DB_SSLMODE', 'prefer'),
        ],

        // ─────────────────────────────────────────────────────────────────────
        // CONEXIÓN SQL Server (sqlsrv)
        // Motor de base de datos relacional de Microsoft. Utilizado en
        // entornos corporativos con infraestructura Microsoft.
        // ─────────────────────────────────────────────────────────────────────
        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),

            // Puerto estándar de SQL Server: 1433.
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // Las siguientes opciones de cifrado están comentadas.
            // Pueden habilitarse para entornos que requieran conexión segura.
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | Define la tabla donde Laravel registra el historial de migraciones
    | ejecutadas. Esta información permite determinar qué migraciones
    | pendientes deben ejecutarse en el siguiente comando `php artisan migrate`.
    |
    */

    // 'migrations': arreglo de configuración del sistema de migraciones.
    'migrations' => [
        // 'table': nombre de la tabla en base de datos donde se registran
        // las migraciones ejecutadas.
        'table' => 'migrations',

        // 'update_date_on_publish': cuando es true, actualiza la marca de
        // tiempo de la migración al publicarla mediante un paquete de terceros.
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis es un almacén de datos en memoria de tipo clave-valor. Laravel
    | lo puede utilizar como motor de caché, sesiones y colas de trabajo.
    |
    */

    // 'redis': arreglo que contiene toda la configuración del cliente Redis.
    'redis' => [

        // 'client': biblioteca PHP utilizada para interactuar con Redis.
        // 'phpredis' es la extensión nativa de PHP, de mayor rendimiento.
        'client' => env('REDIS_CLIENT', 'phpredis'),

        // 'options': configuración global que aplica a todas las conexiones Redis.
        'options' => [
            // 'cluster': estrategia de agrupamiento de nodos Redis.
            // El valor 'redis' utiliza el agrupamiento nativo de Redis Cluster.
            'cluster' => env('REDIS_CLUSTER', 'redis'),

            // 'prefix': prefijo añadido a todas las claves Redis de esta
            // aplicación para evitar colisiones con otras aplicaciones que
            // compartan el mismo servidor Redis.
            // Str::slug() convierte el nombre de la aplicación en un
            // identificador seguro para usar como prefijo (ej: 'laravel-database-').
            'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-'),

            // 'persistent': cuando es true, mantiene la conexión abierta
            // entre peticiones para reducir la latencia de reconexión.
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        // 'default': configuración de la conexión Redis principal,
        // utilizada para operaciones generales de la aplicación.
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),

            // Puerto estándar de Redis: 6379.
            'port' => env('REDIS_PORT', '6379'),

            // 'database': índice de la base de datos Redis a utilizar.
            // Redis soporta múltiples bases de datos numeradas desde 0.
            'database' => env('REDIS_DB', '0'),

            // 'max_retries': número máximo de intentos de reconexión
            // ante un error de conexión.
            'max_retries' => env('REDIS_MAX_RETRIES', 3),

            // 'backoff_algorithm': algoritmo para calcular el tiempo de espera
            // entre reintentos. 'decorrelated_jitter' introduce variación
            // aleatoria para evitar que múltiples clientes reintentan
            // simultáneamente (efecto conocido como thundering herd).
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),

            // 'backoff_base': tiempo base en milisegundos para el cálculo
            // del intervalo de reintento.
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),

            // 'backoff_cap': tiempo máximo en milisegundos que puede alcanzar
            // el intervalo de reintento, independientemente del algoritmo.
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

        // 'cache': conexión Redis dedicada exclusivamente al almacenamiento
        // de caché de la aplicación. Utiliza la base de datos Redis 1
        // (índice 1) para separarla de los datos operacionales.
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),

            // 'database': índice 1, separado de la conexión 'default' (índice 0)
            // para aislar los datos de caché de los datos generales.
            'database' => env('REDIS_CACHE_DB', '1'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

    ],

];
