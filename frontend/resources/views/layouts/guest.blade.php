<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bienvenida') — Asha Spa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{--
    Layout para personas SIN sesión (login, registro).
    No tiene menú de navegación: solo la marca y el formulario.
--}}
<body class="min-h-screen">
    <div class="grid min-h-screen lg:grid-cols-2">

        {{-- Panel de marca (solo en pantallas grandes) --}}
        <aside class="relative hidden flex-col justify-between overflow-hidden bg-primary p-12 text-on-primary lg:flex" aria-hidden="true">
            <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-full border border-on-primary font-serif font-bold text-on-primary">A</span>
                <span class="font-serif text-2xl font-bold text-on-primary">Asha Spa</span>
            </div>
            <div>
                <p class="font-serif text-4xl leading-tight text-canvas">Belleza y bienestar,<br>en un solo lugar.</p>
                <p class="mt-4 max-w-sm text-canvas/75">Agenda tus citas, consulta tus servicios y descubre tu próximo look.</p>
            </div>
            <p class="text-sm text-on-primary/70">Spa y salón de belleza</p>
            <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full border border-on-primary/20"></div>
            <div class="absolute -bottom-32 right-16 h-96 w-96 rounded-full border border-on-primary/10"></div>
        </aside>

        {{-- Formulario --}}
        <main id="contenido-principal" class="flex items-center justify-center px-4 py-12 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-8 text-center lg:hidden">
                    <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary font-serif text-xl font-bold text-on-primary" aria-hidden="true">A</span>
                    <p class="font-serif text-2xl font-bold">Asha Spa</p>
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
