{{--
    ARCHIVO: resources/views/layouts/app.blade.php

    PROPÓSITO: Plantilla maestra (layout) del sistema Asha Spa. Define la
    estructura HTML completa que es compartida por todas las vistas de la
    aplicación mediante la directiva @extends('layouts.app') de Blade.

    Las vistas hijas pueden personalizar el contenido de las secciones
    definidas con @yield() mediante la directiva @section()/@endsection.

    SECCIONES DISPONIBLES PARA VISTAS HIJAS:
      - 'title':   Título de la pestaña del navegador. Predeterminado: 'Asha Spa'.
      - 'content': Contenido principal de la página, inyectado dentro del <main>.

    PILA DE SCRIPTS:
      - @stack('scripts'): Permite que las vistas hijas inyecten bloques
        JavaScript al final del documento mediante @push('scripts').

    MOTOR DE PLANTILLAS: Blade (Laravel). Los comentarios con doble llave y
    guiones {{-- --}} no se renderizan en el HTML final enviado al navegador.
    Las expresiones {{ }} se evalúan como PHP y su resultado se escapa
    automáticamente para prevenir ataques XSS (Cross-Site Scripting).
--}}

{{-- DOCTYPE html: declaración del tipo de documento HTML5.
     Informa al navegador que el documento sigue el estándar HTML5. --}}
<!DOCTYPE html>

{{-- lang="es": atributo que declara el idioma principal del documento.
     Es utilizado por lectores de pantalla, motores de búsqueda y
     herramientas de accesibilidad. --}}
<html lang="es">
<head>
    {{-- charset="utf-8": declara la codificación de caracteres del documento.
         UTF-8 soporta todos los caracteres del estándar Unicode, incluyendo
         tildes y caracteres especiales del español. --}}
    <meta charset="utf-8">

    {{-- viewport: metaetiqueta esencial para el diseño responsivo.
         width=device-width: establece el ancho del viewport al ancho real
         del dispositivo. initial-scale=1: desactiva el zoom automático
         en dispositivos móviles al cargar la página. --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- csrf-token: metaetiqueta que expone el token CSRF al JavaScript.
         csrf_token() es un helper de Laravel que genera el token de
         verificación de formularios almacenado en la sesión del usuario.
         Este token se utiliza para prevenir ataques CSRF (Cross-Site
         Request Forgery) en peticiones AJAX realizadas desde el frontend. --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- @yield('title', 'Asha Spa'): directiva Blade que inyecta el contenido
         de la sección 'title' definida en la vista hija. Si la vista hija no
         define esta sección, se utiliza el valor predeterminado 'Asha Spa'.
         El resultado se concatena con ' — Asha Spa' para construir el título
         completo que aparece en la pestaña del navegador. --}}
    <title>@yield('title', 'Asha Spa') — Asha Spa</title>

    {{-- @vite([...]): directiva de Laravel que genera las etiquetas HTML
         necesarias para cargar los recursos compilados con Vite.
         En modo desarrollo, Vite inyecta los recursos directamente desde
         su servidor de desarrollo (http://localhost:5173) con Hot Module
         Replacement (HMR) activo.
         En modo producción, referencia los archivos compilados y versionados
         en el directorio public/build/.
         - resources/css/app.css: hoja de estilos principal con Tailwind CSS.
         - resources/js/app.js: punto de entrada JavaScript de la aplicación. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- min-h-screen: clase de Tailwind que establece altura mínima del 100% de
     la ventana (min-height: 100vh), garantizando que el fondo ocupe toda la
     pantalla incluso en páginas con poco contenido.
     flex flex-col: establece el body como contenedor flexbox vertical,
     necesario para que el footer permanezca al final de la página. --}}
<body class="min-h-screen flex flex-col">

    {{-- ENLACE DE ACCESIBILIDAD (Skip Link)
         Permite a los usuarios de teclado y lectores de pantalla omitir
         la navegación y saltar directamente al contenido principal.
         sr-only: clase de Tailwind que oculta visualmente el elemento
         (posición absoluta fuera de la pantalla) pero lo mantiene accesible
         para tecnologías de asistencia.
         focus:not-sr-only: revierte el ocultamiento cuando el elemento
         recibe el foco mediante la tecla Tab, haciéndolo visible. --}}
    <a
        href="#contenido-principal"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:rounded-lg focus:text-sm focus:font-medium"
        style="background-color: var(--color-terracotta); color: white;"
    >
        Saltar al contenido
    </a>

    {{-- ===== CABECERA =====
         role="banner": atributo ARIA que identifica semánticamente este
         elemento como la cabecera principal del sitio.
         sticky top-0 z-40: mantiene la cabecera visible en la parte
         superior de la ventana al hacer scroll (posición fija relativa
         al flujo del documento). --}}
    <header
        style="background-color: var(--color-warm-white); border-bottom: 1px solid var(--color-beige);"
        class="sticky top-0 z-40 shadow-sm"
        role="banner"
    >
        {{-- Contenedor centrado con ancho máximo de 7xl (80rem) y
             padding horizontal responsivo según el ancho de pantalla. --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- LOGO / MARCA
                     route('home'): helper de Laravel que genera la URL de la
                     ruta nombrada 'home', definida en routes/web.php.
                     aria-label: describe el propósito del enlace para
                     tecnologías de asistencia. --}}
                <a
                    href="{{ route('home') }}"
                    class="flex items-center gap-2 group"
                    aria-label="Asha Spa — Ir a inicio"
                >
                    {{-- Círculo decorativo con la inicial 'A'. aria-hidden="true"
                         oculta este elemento puramente decorativo a los lectores
                         de pantalla. --}}
                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                        style="background-color: var(--color-terracotta);"
                        aria-hidden="true"
                    >
                        A
                    </div>
                    <span
                        class="text-xl font-bold tracking-wide"
                        style="font-family: var(--font-serif); color: var(--color-charcoal);"
                    >
                        Asha Spa
                    </span>
                </a>

                {{-- NAVEGACIÓN PRINCIPAL (visible en pantallas medianas y superiores)
                     hidden md:flex: oculta el elemento en pantallas pequeñas
                     y lo muestra como flex en pantallas medianas (≥768px). --}}
                <nav
                    class="hidden md:flex items-center gap-1"
                    aria-label="Navegación principal"
                >
                    {{-- @php ... @endphp: bloque de código PHP embebido en Blade.
                         Define el arreglo $navItems con los elementos de navegación.
                         Cada elemento es un arreglo asociativo con las claves
                         'route' (nombre de la ruta) y 'label' (texto visible). --}}
                    @php
                        $navItems = [
                            ['route' => 'home',           'label' => 'Inicio'],
                            ['route' => 'citas.index',    'label' => 'Citas'],
                            ['route' => 'clientes.index', 'label' => 'Clientes'],
                            ['route' => 'servicios.index','label' => 'Servicios'],
                            ['route' => 'empleados.index','label' => 'Empleados'],
                        ];

                        {{--
                            NOTA TÉCNICA: El siguiente bloque verifica si el usuario
                            autenticado tiene el método esCliente(). Dicho método no
                            está declarado en el modelo Usuario. Si este código se
                            ejecuta, PHP lanzará un error de método no encontrado.
                            Este bloque requiere que se agregue el método esCliente()
                            al modelo Usuario para funcionar correctamente.
                            Auth::check(): retorna true si hay un usuario autenticado.
                            Auth::user(): retorna la instancia del modelo Usuario activo.
                            array_values(array_filter(...)): filtra el arreglo y
                            reindexa las claves numéricas del resultado.
                            fn ($item) => ...: función flecha (arrow function) de PHP 7.4+
                            que evalúa si la ruta del item está en el arreglo permitido.
                        --}}
                        if (Auth::check() && Auth::user()->esCliente()) {
                            $navItems = array_values(array_filter(
                                $navItems,
                                fn ($item) => in_array($item['route'], ['home', 'citas.index'])
                            ));
                        }
                    @endphp

                    {{-- @foreach: directiva Blade equivalente a foreach en PHP.
                         Itera sobre el arreglo $navItems y genera un enlace
                         por cada elemento. --}}
                    @foreach ($navItems as $item)
                        @php
                            {{-- Determina si el enlace corresponde a la ruta activa.
                                 request()->routeIs(): verifica si la ruta actual
                                 coincide con el nombre indicado. El segundo patrón
                                 con '.*' captura también subrutas del mismo módulo.
                                 El operador || (OR lógico) devuelve true si cualquiera
                                 de las dos condiciones es verdadera. --}}
                            $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                        @endphp
                        {{-- Enlace de navegación con estilo dinámico según estado activo.
                             aria-current="page": atributo ARIA que indica al lector
                             de pantalla que este enlace corresponde a la página actual. --}}
                        <a
                            href="{{ route($item['route']) }}"
                            class="px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150"
                            style="
                                color: {{ $isActive ? 'var(--color-terracotta)' : 'var(--color-charcoal-light)' }};
                                background-color: {{ $isActive ? 'var(--color-terracotta-light)' : 'transparent' }};
                            "
                            @if($isActive) aria-current="page" @endif
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    {{-- Elementos de navegación deshabilitados para módulos en desarrollo.
                         Se usan etiquetas <span> en lugar de <a> para que no sean
                         interactivos ni navegables por teclado. --}}
                    <span
                        class="px-3 py-2 rounded-lg text-sm font-medium flex items-center gap-1.5 cursor-default select-none"
                        style="color: var(--color-charcoal-muted);"
                        title="Módulo en desarrollo"
                        aria-label="Ventas — Próximamente"
                    >
                        Ventas
                        <span
                            class="text-xs px-1.5 py-0.5 rounded-full font-medium"
                            style="background-color: var(--color-beige); color: var(--color-charcoal-muted); font-size: 0.65rem;"
                            aria-hidden="true"
                        >
                            Próx.
                        </span>
                    </span>
                    <span
                        class="px-3 py-2 rounded-lg text-sm font-medium flex items-center gap-1.5 cursor-default select-none"
                        style="color: var(--color-charcoal-muted);"
                        title="Módulo en desarrollo"
                        aria-label="Inventario — Próximamente"
                    >
                        Inventario
                        <span
                            class="text-xs px-1.5 py-0.5 rounded-full font-medium"
                            style="background-color: var(--color-beige); color: var(--color-charcoal-muted); font-size: 0.65rem;"
                            aria-hidden="true"
                        >
                            Próx.
                        </span>
                    </span>
                </nav>

                {{-- SECCIÓN DE USUARIO AUTENTICADO Y CIERRE DE SESIÓN
                     @auth ... @endauth: directiva Blade que renderiza su contenido
                     únicamente cuando hay un usuario con sesión activa.
                     Es equivalente a @if(Auth::check()) ... @endif. --}}
                @auth
                <div class="hidden md:flex items-center gap-3 ml-2 pl-3" style="border-left: 1px solid var(--color-beige);">
                    {{-- Muestra el nombre del usuario autenticado.
                         Auth::user(): retorna la instancia del modelo Usuario activo.
                         ->nombre: accede al atributo 'nombre' del modelo.
                         ?? Auth::user()->email: operador de coalescencia nula; si
                         $nombre es null, muestra el email como alternativa. --}}
                    <span class="text-sm font-medium" style="color: var(--color-charcoal-light);" aria-label="Usuario: {{ Auth::user()->nombre ?? Auth::user()->email }}">
                        {{ Auth::user()->nombre ?? Auth::user()->email }}
                    </span>
                    {{-- Formulario de cierre de sesión.
                         Se utiliza un formulario POST (no un enlace GET) para
                         que el logout sea una acción con verificación CSRF.
                         @csrf: directiva Blade que inserta un campo oculto con
                         el token CSRF necesario para que Laravel acepte la petición. --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors duration-150 cursor-pointer"
                            style="color: var(--color-charcoal-muted); background-color: transparent; border: 1px solid var(--color-beige-mid);"
                            title="Cerrar sesión"
                        >
                            {{-- Ícono SVG de logout. aria-hidden="true" lo oculta
                                 a lectores de pantalla ya que es decorativo. --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Salir
                        </button>
                    </form>
                </div>
                @endauth

                {{-- BOTÓN HAMBURGUESA (visible solo en pantallas pequeñas)
                     md:hidden: oculta el elemento en pantallas medianas y superiores.
                     aria-expanded="false": valor ARIA inicial que indica que el menú
                     móvil está cerrado. Este valor es actualizado por JavaScript.
                     aria-controls="mobile-menu": asocia este botón con el elemento
                     que controla, identificado por el id 'mobile-menu'. --}}
                <button
                    id="menu-toggle"
                    type="button"
                    class="md:hidden p-2 rounded-lg"
                    style="color: var(--color-charcoal-light);"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                    aria-label="Abrir menú de navegación"
                >
                    {{-- Ícono hamburguesa: tres líneas horizontales representadas
                         mediante trazos SVG. --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

            </div>
        </div>

        {{-- MENÚ MÓVIL
             hidden: clase de Tailwind que aplica display:none por defecto.
             El JavaScript al final del documento alterna esta clase según
             el estado del botón hamburguesa.
             role="navigation": atributo ARIA que identifica este div como
             una región de navegación secundaria. --}}
        <div
            id="mobile-menu"
            class="hidden md:hidden border-t"
            style="border-color: var(--color-beige); background-color: var(--color-warm-white);"
            role="navigation"
            aria-label="Navegación móvil"
        >
            <div class="px-4 py-3 flex flex-col gap-1">
                {{-- Reutiliza el arreglo $navItems definido anteriormente
                     para generar los mismos enlaces en el menú móvil. --}}
                @foreach ($navItems as $item)
                    @php
                        $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                    @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="px-3 py-2.5 rounded-lg text-sm font-medium"
                        style="
                            color: {{ $isActive ? 'var(--color-terracotta)' : 'var(--color-charcoal)' }};
                            background-color: {{ $isActive ? 'var(--color-terracotta-light)' : 'transparent' }};
                        "
                        @if($isActive) aria-current="page" @endif
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
                {{-- Módulos en desarrollo en versión móvil. --}}
                <div class="flex gap-2 pt-1">
                    <span class="px-3 py-2 rounded-lg text-sm" style="color: var(--color-charcoal-muted);">Ventas <span style="font-size:0.65rem; background-color:var(--color-beige); color:var(--color-charcoal-muted);" class="px-1.5 py-0.5 rounded-full">Próx.</span></span>
                    <span class="px-3 py-2 rounded-lg text-sm" style="color: var(--color-charcoal-muted);">Inventario <span style="font-size:0.65rem; background-color:var(--color-beige); color:var(--color-charcoal-muted);" class="px-1.5 py-0.5 rounded-full">Próx.</span></span>
                </div>
                {{-- Sección de usuario y logout en el menú móvil.
                     Solo se renderiza si hay una sesión activa (@auth). --}}
                @auth
                <div class="pt-2 mt-1" style="border-top: 1px solid var(--color-beige);">
                    <p class="px-3 py-1 text-xs" style="color: var(--color-charcoal-muted);">{{ Auth::user()->nombre ?? Auth::user()->email }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg text-sm font-medium" style="color: var(--color-red-soft);">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </header>

    {{-- ===== MENSAJES GLOBALES DE SESIÓN =====
         Bloque condicional que verifica si alguno de los tres tipos de
         mensajes flash existe en la sesión activa.
         session(): helper de Laravel que accede a los datos de la sesión.
         Los mensajes flash existen únicamente durante la petición
         inmediatamente posterior a su creación (->with('success', ...)).
         role="alert": atributo ARIA que indica a los lectores de pantalla
         que el contenido es un mensaje importante.
         aria-live="polite": indica al lector de pantalla que anuncie el
         contenido cuando el usuario termine la tarea actual. --}}
    @if (session('success') || session('error') || session('warning'))
        <div
            class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4"
            role="alert"
            aria-live="polite"
        >
            {{-- Mensaje de éxito: fondo verde suave con icono de verificación. --}}
            @if (session('success'))
                <div
                    class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
                    style="background-color: var(--color-sage-light); color: var(--color-sage-dark); border: 1px solid var(--color-sage);"
                >
                    <span aria-hidden="true">✓</span>
                    {{-- session('success'): recupera y elimina el mensaje de la sesión. --}}
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            {{-- Mensaje de error: fondo rojo suave con icono de cruz. --}}
            @if (session('error'))
                <div
                    class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
                    style="background-color: var(--color-red-light); color: var(--color-red-soft); border: 1px solid var(--color-red-soft);"
                >
                    <span aria-hidden="true">✕</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            {{-- Mensaje de advertencia: fondo ámbar con icono de exclamación. --}}
            @if (session('warning'))
                <div
                    class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
                    style="background-color: var(--color-amber-light); color: #92650A; border: 1px solid var(--color-amber-soft);"
                >
                    <span aria-hidden="true">!</span>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif
        </div>
    @endif

    {{-- ===== CONTENIDO PRINCIPAL =====
         id="contenido-principal": identificador referenciado por el enlace
         de accesibilidad para permitir el salto de navegación.
         flex-1: clase de Tailwind que hace que este elemento crezca para
         ocupar todo el espacio disponible entre la cabecera y el footer,
         manteniendo el footer al final de la página.
         role="main": atributo ARIA que identifica esta región como el
         contenido principal del documento. --}}
    <main
        id="contenido-principal"
        class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8"
        role="main"
    >
        {{-- @yield('content'): directiva Blade que inyecta el contenido
             definido por la sección 'content' de la vista hija. Si la
             vista hija no define esta sección, este espacio queda vacío. --}}
        @yield('content')
    </main>

    {{-- ===== PIE DE PÁGINA =====
         mt-auto: margen superior automático que empuja el footer al final
         cuando el contenido principal no llena la pantalla.
         role="contentinfo": atributo ARIA que identifica esta región como
         el pie de página del sitio. --}}
    <footer
        class="mt-auto py-6"
        style="background-color: var(--color-warm-white); border-top: 1px solid var(--color-beige);"
        role="contentinfo"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-sm"
                 style="color: var(--color-charcoal-muted);">
                <p>
                    <span style="font-family: var(--font-serif); color: var(--color-charcoal-light); font-weight: 600;">Asha Spa</span>
                    {{-- &mdash;: entidad HTML que representa el guión largo (—). --}}
                    &mdash; Sistema de gestión administrativa
                </p>
                <p>Desarrollado con cariño por el equipo ADSO</p>
            </div>
        </div>
    </footer>

    {{-- SCRIPT DE INTERACTIVIDAD DEL MENÚ MÓVIL
         El código JavaScript está envuelto en una IIFE (Immediately Invoked
         Function Expression): función anónima que se ejecuta de inmediato.
         Esto evita contaminar el scope global de JavaScript con las variables
         declaradas dentro. --}}
    <script>
        (function () {
            // document.getElementById(): método del DOM que busca el elemento
            // HTML cuyo atributo id coincide con el valor indicado.
            // Retorna null si el elemento no existe en el documento.
            const toggle = document.getElementById('menu-toggle');
            const menu   = document.getElementById('mobile-menu');

            // Verificación de existencia: si alguno de los dos elementos
            // no existe en el DOM, la función termina sin ejecutar más código.
            // El operador || evalúa si alguna de las dos condiciones es verdadera.
            if (!toggle || !menu) return;

            // addEventListener('click', ...): registra una función manejadora
            // que se ejecuta cada vez que el usuario hace clic en el botón.
            toggle.addEventListener('click', function () {
                // Verifica si el menú está actualmente abierto.
                // classList.contains('hidden'): retorna true si el elemento
                // tiene la clase CSS 'hidden' aplicada.
                // El operador ! niega el resultado: isOpen es true cuando
                // el menú NO tiene la clase 'hidden' (es decir, está visible).
                const isOpen = !menu.classList.contains('hidden');

                // classList.toggle('hidden', isOpen): agrega la clase 'hidden'
                // si isOpen es true (cierra el menú), o la elimina si es false
                // (abre el menú). El segundo argumento fuerza el estado.
                menu.classList.toggle('hidden', isOpen);

                // setAttribute(): actualiza el atributo aria-expanded del botón.
                // String(!isOpen): convierte el booleano negado a cadena ('true' o 'false')
                // porque los atributos HTML son siempre valores de texto.
                toggle.setAttribute('aria-expanded', String(!isOpen));

                // Actualiza la etiqueta del botón según el nuevo estado del menú.
                // El operador ternario selecciona el texto apropiado.
                toggle.setAttribute('aria-label', isOpen ? 'Abrir menú de navegación' : 'Cerrar menú de navegación');
            });
        })();
    </script>

    {{-- @stack('scripts'): directiva Blade que renderiza todos los bloques
         JavaScript registrados por las vistas hijas mediante @push('scripts').
         Permite que cada vista inyecte su JavaScript específico al final del
         documento sin modificar el layout. --}}
    @stack('scripts')

</body>
</html>
