<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- El título puede personalizarse por vista con @section('title') --}}
    <title>@yield('title', 'Asha Spa') — Asha Spa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col">

    {{-- Enlace de accesibilidad: permite saltar al contenido principal con teclado --}}
    <a
        href="#contenido-principal"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:rounded-lg focus:text-sm focus:font-medium"
        style="background-color: var(--color-terracotta); color: white;"
    >
        Saltar al contenido
    </a>

    {{-- ===== CABECERA ===== --}}
    <header
        style="background-color: var(--color-warm-white); border-bottom: 1px solid var(--color-beige);"
        class="sticky top-0 z-40 shadow-sm"
        role="banner"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo / Marca --}}
                <a
                    href="{{ route('home') }}"
                    class="flex items-center gap-2 group"
                    aria-label="Asha Spa — Ir a inicio"
                >
                    {{-- Ícono decorativo simple en lugar de imagen externa --}}
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

                {{-- Navegación principal (escritorio) --}}
                <nav
                    class="hidden md:flex items-center gap-1"
                    aria-label="Navegación principal"
                >
                    @php
                        $navItems = [
                            ['route' => 'home',           'label' => 'Inicio'],
                            ['route' => 'citas.index',    'label' => 'Citas'],
                            ['route' => 'clientes.index', 'label' => 'Clientes'],
                            ['route' => 'servicios.index','label' => 'Servicios'],
                            ['route' => 'empleados.index','label' => 'Empleados'],
                        ];
                    @endphp

                    @foreach ($navItems as $item)
                        @php
                            $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                        @endphp
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

                    {{-- Módulos próximos: sin enlace activo --}}
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

                {{-- Botón hamburguesa (móvil) --}}
                <button
                    id="menu-toggle"
                    type="button"
                    class="md:hidden p-2 rounded-lg"
                    style="color: var(--color-charcoal-light);"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                    aria-label="Abrir menú de navegación"
                >
                    {{-- Ícono hamburguesa en SVG puro --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

            </div>
        </div>

        {{-- Menú móvil (oculto por defecto) --}}
        <div
            id="mobile-menu"
            class="hidden md:hidden border-t"
            style="border-color: var(--color-beige); background-color: var(--color-warm-white);"
            role="navigation"
            aria-label="Navegación móvil"
        >
            <div class="px-4 py-3 flex flex-col gap-1">
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
                <div class="flex gap-2 pt-1">
                    <span class="px-3 py-2 rounded-lg text-sm" style="color: var(--color-charcoal-muted);">Ventas <span style="font-size:0.65rem; background-color:var(--color-beige); color:var(--color-charcoal-muted);" class="px-1.5 py-0.5 rounded-full">Próx.</span></span>
                    <span class="px-3 py-2 rounded-lg text-sm" style="color: var(--color-charcoal-muted);">Inventario <span style="font-size:0.65rem; background-color:var(--color-beige); color:var(--color-charcoal-muted);" class="px-1.5 py-0.5 rounded-full">Próx.</span></span>
                </div>
            </div>
        </div>
    </header>

    {{-- ===== MENSAJES GLOBALES DE SESIÓN ===== --}}
    {{-- Solo se muestran cuando el servidor los envía --}}
    @if (session('success') || session('error') || session('warning'))
        <div
            class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4"
            role="alert"
            aria-live="polite"
        >
            @if (session('success'))
                <div
                    class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
                    style="background-color: var(--color-sage-light); color: var(--color-sage-dark); border: 1px solid var(--color-sage);"
                >
                    <span aria-hidden="true">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div
                    class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
                    style="background-color: var(--color-red-light); color: var(--color-red-soft); border: 1px solid var(--color-red-soft);"
                >
                    <span aria-hidden="true">✕</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
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

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
    <main
        id="contenido-principal"
        class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8"
        role="main"
    >
        @yield('content')
    </main>

    {{-- ===== PIE DE PÁGINA ===== --}}
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
                    &mdash; Sistema de gestión administrativa
                </p>
                <p>Desarrollado con cariño por el equipo ADSO</p>
            </div>
        </div>
    </footer>

    {{-- Script para el menú móvil --}}
    <script>
        (function () {
            const toggle = document.getElementById('menu-toggle');
            const menu   = document.getElementById('mobile-menu');
            if (!toggle || !menu) return;

            toggle.addEventListener('click', function () {
                const isOpen = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden', isOpen);
                toggle.setAttribute('aria-expanded', String(!isOpen));
                toggle.setAttribute('aria-label', isOpen ? 'Abrir menú de navegación' : 'Cerrar menú de navegación');
            });
        })();
    </script>

    {{-- Slot para scripts adicionales por vista --}}
    @stack('scripts')

</body>
</html>
