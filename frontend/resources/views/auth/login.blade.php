@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
<h1 class="text-3xl">Bienvenida de nuevo</h1>
<p class="mb-8 mt-1 text-sm text-ink-soft">Ingresa con tu correo y contraseña.</p>

<form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5" novalidate>
    @csrf

    <x-form-field name="email" label="Correo electrónico" type="email" :required="true"
                  placeholder="nombre@correo.com" autocomplete="email" autofocus />

    <x-password-field name="password" label="Contraseña" autocomplete="current-password" />

    <div class="flex flex-wrap items-center justify-between gap-2">
        <label class="flex items-center gap-2 text-sm text-ink-soft">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-line-strong accent-primary" @checked(old('remember'))>
            Mantener la sesión iniciada
        </label>
        <a href="{{ route('password.request') }}" class="enlace text-sm">¿Olvidaste tu contraseña?</a>
    </div>

    <x-button type="submit" class="w-full py-2.5">Iniciar sesión</x-button>
</form>

<p class="mt-8 text-center text-sm text-ink-muted">
    ¿Aún no tienes cuenta?
    <a href="{{ route('register') }}" class="enlace">Regístrate como cliente</a>
</p>
@endsection
