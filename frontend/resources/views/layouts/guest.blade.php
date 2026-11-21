<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bienvenida') — Asha Spa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Las hojas se "dibujan" al cargar la página y luego se mecen suavemente. */
        .trazo { stroke-dasharray: 1; stroke-dashoffset: 1; animation: dibujar 2.4s ease-out forwards; }
        .trazo-2 { animation-delay: .35s; }
        .trazo-3 { animation-delay: .7s; }
        .rama { transform-origin: 50% 100%; animation: mecer 9s ease-in-out 2.6s infinite alternate; }
        .rama-2 { animation-duration: 11s; }
        @keyframes dibujar { to { stroke-dashoffset: 0; } }
        @keyframes mecer { from { transform: rotate(-1.2deg); } to { transform: rotate(1.6deg); } }

        /* Respeta a quien prefiere menos movimiento en su sistema operativo. */
        @media (prefers-reduced-motion: reduce) {
            .trazo { animation: none; stroke-dashoffset: 0; }
            .rama { animation: none; }
        }
    </style>
</head>
{{--
    Layout para personas SIN sesión (login y registro).
    No tiene menú de navegación: solo la portada de la marca y el formulario.

    Portada "Botánico dorado": ramas dibujadas con línea dorada sobre negro.
    Todos los colores salen de la paleta (app.css): bg-primary, text-on-primary,
    stroke-accent… así la portada cambia sola si se cambia la paleta.
--}}
<body class="min-h-screen">
    <div class="grid min-h-screen lg:grid-cols-[1.1fr_1fr]">

        {{-- ===== PORTADA (pantallas grandes) ===== --}}
        <aside class="relative hidden overflow-hidden bg-primary lg:block" aria-hidden="true">

            {{-- Resplandor dorado muy suave detrás de las ramas --}}
            <div class="absolute -right-32 top-1/4 h-[28rem] w-[28rem] rounded-full bg-accent/15 blur-3xl"></div>
            <div class="absolute -bottom-40 left-10 h-80 w-80 rounded-full bg-accent/10 blur-3xl"></div>

            {{-- Ramas botánicas en línea dorada --}}
            <svg class="absolute bottom-0 right-0 h-[92%] w-auto" viewBox="0 0 420 760" fill="none"
                 stroke-linecap="round" stroke-linejoin="round">
                {{-- Rama principal --}}
                <g class="rama stroke-on-primary" stroke-width="1.4">
                    <path class="trazo" pathLength="1" d="M250 760 C246 640 252 520 282 400 C304 310 330 230 372 120"/>
                    <path class="trazo trazo-2" pathLength="1" d="M252 690 C206 682 170 652 160 606 C206 606 240 640 252 690Z"/>
                    <path class="trazo trazo-2" pathLength="1" d="M258 610 C304 596 336 562 340 512 C296 520 264 556 258 610Z"/>
                    <path class="trazo trazo-2" pathLength="1" d="M266 540 C222 526 194 492 192 446 C236 456 262 492 266 540Z"/>
                    <path class="trazo trazo-3" pathLength="1" d="M282 462 C324 444 350 408 350 362 C310 374 286 414 282 462Z"/>
                    <path class="trazo trazo-3" pathLength="1" d="M296 392 C258 374 238 340 240 298 C278 312 296 350 296 392Z"/>
                    <path class="trazo trazo-3" pathLength="1" d="M318 318 C354 296 372 258 368 218 C332 234 316 274 318 318Z"/>
                    <path class="trazo trazo-3" pathLength="1" d="M340 250 C312 228 302 196 308 162 C338 182 346 216 340 250Z"/>
                    <path class="trazo trazo-3" pathLength="1" d="M362 168 C384 148 392 120 386 92 C364 108 358 138 362 168Z"/>
                    {{-- Nervaduras --}}
                    <g stroke-width=".8" opacity=".55">
                        <path class="trazo trazo-3" pathLength="1" d="M252 690 C226 668 200 638 172 612"/>
                        <path class="trazo trazo-3" pathLength="1" d="M258 610 C284 586 312 552 336 518"/>
                        <path class="trazo trazo-3" pathLength="1" d="M266 540 C240 514 218 484 198 452"/>
                        <path class="trazo trazo-3" pathLength="1" d="M282 462 C306 434 328 400 346 368"/>
                    </g>
                </g>

                {{-- Rama secundaria, más fina y al fondo --}}
                <g class="rama rama-2 stroke-accent" stroke-width="1" opacity=".7">
                    <path class="trazo trazo-2" pathLength="1" d="M110 760 C112 680 104 600 80 520"/>
                    <path class="trazo trazo-3" pathLength="1" d="M108 700 C78 690 60 664 60 632 C90 640 106 668 108 700Z"/>
                    <path class="trazo trazo-3" pathLength="1" d="M104 640 C132 626 146 598 144 566 C116 576 102 606 104 640Z"/>
                    <path class="trazo trazo-3" pathLength="1" d="M94 584 C66 570 52 542 56 510 C84 522 96 552 94 584Z"/>
                </g>

                {{-- Pequeños botones dorados (rellenos) --}}
                <g class="fill-on-primary" stroke="none">
                    <circle cx="372" cy="120" r="3"/>
                    <circle cx="386" cy="92" r="2"/>
                    <circle cx="80" cy="520" r="2.5"/>
                </g>
            </svg>

            {{-- Contenido de la portada --}}
            <div class="relative z-10 flex h-full flex-col justify-between p-12 xl:p-16">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full border border-on-primary font-serif text-lg font-bold text-on-primary">A</span>
                    <span class="font-serif text-2xl font-bold tracking-wide text-on-primary">Asha Spa</span>
                </div>

                <div class="max-w-md">
                    <p class="mb-4 text-xs font-semibold uppercase tracking-[0.3em] text-accent">Spa · Salón de belleza</p>
                    <p class="font-serif text-5xl leading-[1.1] text-canvas">
                        Tu ritual de<br><span class="italic text-on-primary">bienestar</span><br>empieza aquí.
                    </p>
                    <p class="mt-6 max-w-sm text-canvas/70">
                        Agenda tus citas, descubre nuestros servicios y prueba tu próximo look antes de llegar.
                    </p>
                </div>

                <ul class="flex flex-wrap gap-2 text-sm text-canvas/80">
                    @foreach (['Cabello', 'Uñas', 'Piel', 'Relajación'] as $categoria)
                        <li class="rounded-full border border-accent/40 bg-primary/80 px-4 py-1.5 backdrop-blur-sm">{{ $categoria }}</li>
                    @endforeach
                </ul>
            </div>
        </aside>

        {{-- ===== FORMULARIO ===== --}}
        <main id="contenido-principal" class="relative flex items-center justify-center overflow-hidden px-4 py-12 sm:px-8">

            {{-- Hoja decorativa en móvil y tableta (la portada se oculta allí) --}}
            <svg class="pointer-events-none absolute -right-10 -top-6 h-48 w-auto lg:hidden" viewBox="0 0 120 200" fill="none"
                 stroke-linecap="round" aria-hidden="true">
                <g class="stroke-accent" stroke-width="1.2" opacity=".5">
                    <path d="M70 200 C70 150 74 100 96 40"/>
                    <path d="M71 160 C50 152 40 134 42 114 C60 122 70 140 71 160Z"/>
                    <path d="M76 124 C96 114 106 96 104 76 C86 84 76 102 76 124Z"/>
                    <path d="M84 86 C68 76 62 60 66 44 C80 54 86 70 84 86Z"/>
                </g>
            </svg>

            <div class="relative w-full max-w-md">
                <div class="mb-8 text-center lg:hidden">
                    <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary font-serif text-xl font-bold text-on-primary" aria-hidden="true">A</span>
                    <p class="font-serif text-2xl font-bold">Asha Spa</p>
                    <p class="text-xs uppercase tracking-[0.25em] text-accent-ink">Spa · Salón de belleza</p>
                </div>

                @if (session('success')) <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert> @endif
                @if (session('error'))   <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert> @endif

                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>