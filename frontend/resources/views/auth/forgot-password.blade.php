@extends('layouts.guest')

@section('title', 'Recuperar contraseña')

@section('content')
<a href="{{ route('login') }}" class="mb-6 inline-flex items-center gap-1 text-sm text-ink-muted hover:text-accent-ink">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver a iniciar sesión
</a>

<h1 class="text-3xl">¿Olvidaste tu contraseña?</h1>
<p class="mb-8 mt-1 text-sm text-ink-soft">
    Escribe el correo con el que te registraste y te enviaremos un enlace para crear una nueva.
</p>

@if (session('enlace_enviado'))
    {{-- Mismo mensaje exista o no la cuenta (no se revela qué correos están registrados). --}}
    <x-alert type="success" class="mb-6">
        Si el correo <strong>{{ old('email') }}</strong> pertenece a una cuenta activa, te llegará un enlace
        para crear una nueva contraseña. Revisa también la carpeta de spam. El enlace vence en
        {{ config('auth.passwords.users.expire', 60) }} minutos.
    </x-alert>

    @if (app()->environment('local') && config('mail.default') === 'log')
        <x-alert type="info" class="mb-6">
            Modo desarrollo: el correo no se envía, se guarda en <code>backend/storage/logs/laravel.log</code>.
            Busca la línea que empieza con <strong>[Recuperación de contraseña]</strong> y abre ese enlace.
        </x-alert>
    @endif
@endif

<form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5" novalidate>
    @csrf

    <x-form-field name="email" label="Correo electrónico" type="email" :required="true"
                  placeholder="nombre@correo.com" autocomplete="email" autofocus />

    <x-button type="submit" class="w-full py-2.5">Enviarme el enlace</x-button>
</form>

<p class="mt-8 text-center text-sm text-ink-muted">
    ¿Ya la recordaste? <a href="{{ route('login') }}" class="enlace">Inicia sesión</a>
</p>
@endsection
