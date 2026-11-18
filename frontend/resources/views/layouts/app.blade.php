<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Cada vista puede personalizar el título con @section('title') --}}
    <title>@yield('title', 'Inicio') — Asha Spa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen flex-col">

    {{-- Accesibilidad: saltar directamente al contenido con el teclado --}}
    <a href="#contenido-principal"
       class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-primary focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-on-primary">
        Saltar al contenido
    </a>

    {{--
        Menú según los permisos del usuario.
        Cada opción declara qué permiso necesita ('puede'); si el usuario no lo
        tiene, la opción no aparece. Esto es solo una ayuda visual: la seguridad
        real está en las rutas (middleware 'can') y en las Policies.
    --}}
    @php
        $usuario = auth()->user();

        $menu = [
            ['label' => 'Inicio',    'route' => 'home',            'puede' => true],
            ['label' => 'Citas',     'route' => 'citas.index',     'puede' => $usuario->can('viewAny', \App\Models\Cita::class)],
            ['label' => 'Clientes',  'route' => 'clientes.index',  'puede' => $usuario->can('viewAny', \App\Models\Cliente::class)],
            ['label' => 'Servicios', 'route' => 'servicios.index', 'puede' => $usuario->can('viewAny', \App\Models\Servicio::class)],
            ['label' => 'Personal',  'route' => 'empleados.index', 'puede' => $usuario->can('viewAny', \App\Models\Empleado::class)],
            ['label' => 'Caja', 'puede' => true, 'grupo' => [
                ['label' => 'Pagos',    'route' => 'pagos.index',    'puede' => $usuario->can('viewAny', \App\Models\Pago::class)],
                ['label' => 'Facturas', 'route' => 'facturas.index', 'puede' => $usuario->can('viewAny', \App\Models\Factura::class)],
            ]],
            ['label' => 'Inventario', 'puede' => true, 'grupo' => [
                ['label' => 'Productos',   'route' => 'productos.index',   'puede' => $usuario->can('viewAny', \App\Models\Producto::class)],
                ['label' => 'Proveedores', 'route' => 'proveedores.index', 'puede' => $usuario->can('viewAny', \App\Models\Proveedor::class)],
                ['label' => 'Movimientos', 'route' => 'movimientos.index', 'puede' => $usuario->can('viewAny', \App\Models\MovimientoInventario::class)],
            ]],
            ['label' => 'Usuarios',  'route' => 'usuarios.index',  'puede' => $usuario->can('administrar-sistema')],
        ];

        // Quita opciones sin permiso y grupos que quedaron vacíos.
        $menu = collect($menu)
            ->map(function ($item) {
                if (isset($item['grupo'])) {
                    $item['grupo'] = array_values(array_filter($item['grupo'], fn ($sub) => $sub['puede']));
                }
                return $item;
            })
            ->filter(fn ($item) => $item['puede'] && (! isset($item['grupo']) || count($item['grupo']) > 0))
            ->values();

        $activo = fn (string $ruta) => request()->routeIs($ruta) || request()->routeIs(\Illuminate\Support\Str::beforeLast($ruta, '.') . '.*');
    @endphp

    {{-- ===== CABECERA ===== --}}
    <header class="sticky top-0 z-40 border-b border-accent/30 bg-primary">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">

            {{-- Marca --}}
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2" aria-label="Asha Spa, ir al inicio">
                <span class="flex h-8 w-8 items-center justify-center rounded-full border border-on-primary font-serif text-sm font-bold text-on-primary" aria-hidden="true">A</span>
                <span class="font-serif text-xl font-bold tracking-wide text-on-primary">Asha Spa</span>
            </a>

            {{-- Navegación (escritorio) --}}
            <nav class="hidden items-center gap-1 lg:flex" aria-label="Navegación principal">
                @foreach ($menu as $item)
                    @isset($item['grupo'])
                        @php $grupoActivo = collect($item['grupo'])->contains(fn ($sub) => $activo($sub['route'])); @endphp
                        <details class="group relative">
                            <summary @class([
                                'flex cursor-pointer list-none items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                                'bg-canvas/10 text-on-primary' => $grupoActivo,
                                'text-canvas/75 hover:bg-canvas/10 hover:text-canvas' => ! $grupoActivo,
                            ])>
                                {{ $item['label'] }}
                                <svg class="transition-transform group-open:rotate-180" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </summary>
                            <div class="absolute left-0 mt-2 w-48 rounded-xl border border-line bg-surface p-1.5 shadow-lg">
                                @foreach ($item['grupo'] as $sub)
                                    <a href="{{ route($sub['route']) }}" @class([
                                        'block rounded-lg px-3 py-2 text-sm',
                                        'bg-primary-soft font-medium text-primary' => $activo($sub['route']),
                                        'text-ink-soft hover:bg-subtle hover:text-ink' => ! $activo($sub['route']),
                                    ]) @if($activo($sub['route'])) aria-current="page" @endif>{{ $sub['label'] }}</a>
                                @endforeach
                            </div>
                        </details>
                    @else
                        <a href="{{ route($item['route']) }}" @class([
                            'rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                            'bg-canvas/10 text-on-primary' => $activo($item['route']),
                            'text-canvas/75 hover:bg-canvas/10 hover:text-canvas' => ! $activo($item['route']),
                        ]) @if($activo($item['route'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                    @endisset
                @endforeach
            </nav>

            {{-- Usuario y salida --}}
            <div class="hidden items-center gap-3 border-l border-canvas/15 pl-4 lg:flex">
                <div class="text-right leading-tight">
                    <p class="text-sm font-medium text-canvas">{{ $usuario->nombre }}</p>
                    <p class="text-xs text-on-primary">{{ $usuario->rol?->nombre }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm border-canvas/25 bg-transparent text-canvas hover:bg-canvas/10" title="Cerrar sesión">Salir</button>
                </form>
            </div>

            {{-- Botón de menú (móvil) --}}
            <button id="menu-toggle" type="button" class="rounded-lg p-2 text-on-primary lg:hidden"
                    aria-expanded="false" aria-controls="mobile-menu" aria-label="Abrir menú de navegación">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        {{-- Menú móvil --}}
        <nav id="mobile-menu" class="hidden border-t border-line bg-surface lg:hidden" aria-label="Navegación móvil">
            <div class="flex flex-col gap-1 px-4 py-3">
                @foreach ($menu as $item)
                    @isset($item['grupo'])
                        <p class="mt-2 px-3 text-xs font-semibold uppercase tracking-wider text-ink-muted">{{ $item['label'] }}</p>
                        @foreach ($item['grupo'] as $sub)
                            <a href="{{ route($sub['route']) }}" @class([
                                'rounded-lg px-3 py-2.5 text-sm font-medium',
                                'bg-primary-soft text-primary' => $activo($sub['route']),
                                'text-ink' => ! $activo($sub['route']),
                            ])>{{ $sub['label'] }}</a>
                        @endforeach
                    @else
                        <a href="{{ route($item['route']) }}" @class([
                            'rounded-lg px-3 py-2.5 text-sm font-medium',
                            'bg-primary-soft text-primary' => $activo($item['route']),
                            'text-ink' => ! $activo($item['route']),
                        ])>{{ $item['label'] }}</a>
                    @endisset
                @endforeach
                <div class="mt-2 border-t border-line pt-3">
                    <p class="px-3 text-xs text-ink-muted">{{ $usuario->nombre }} · {{ $usuario->rol?->nombre }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium text-danger">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    {{-- ===== MENSAJES DEL SERVIDOR ===== --}}
    @if (session('success') || session('error') || session('warning'))
        <div class="mx-auto w-full max-w-7xl space-y-2 px-4 pt-4 sm:px-6 lg:px-8" aria-live="polite">
            @if (session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
            @if (session('error'))   <x-alert type="error">{{ session('error') }}</x-alert> @endif
            @if (session('warning')) <x-alert type="warning">{{ session('warning') }}</x-alert> @endif
        </div>
    @endif

    {{-- ===== CONTENIDO ===== --}}
    <main id="contenido-principal" class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    {{-- ===== PIE ===== --}}
    <footer class="mt-auto border-t border-line bg-surface py-6">
        <div class="mx-auto max-w-7xl px-4 text-sm text-ink-muted sm:px-6 lg:px-8">
            <span class="font-serif font-semibold text-ink-soft">Asha Spa</span> — Sistema de gestión del spa y salón de belleza
        </div>
    </footer>

    <script>
        // Menú móvil: abre y cierra el panel de navegación.
        (function () {
            const boton = document.getElementById('menu-toggle');
            const menu  = document.getElementById('mobile-menu');
            if (!boton || !menu) return;
            boton.addEventListener('click', function () {
                const abierto = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden', abierto);
                boton.setAttribute('aria-expanded', String(!abierto));
                boton.setAttribute('aria-label', abierto ? 'Abrir menú de navegación' : 'Cerrar menú de navegación');
            });
        })();

        // Cierra los submenús de escritorio al hacer clic fuera de ellos.
        document.addEventListener('click', function (e) {
            document.querySelectorAll('header details[open]').forEach(function (d) {
                if (!d.contains(e.target)) d.removeAttribute('open');
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
