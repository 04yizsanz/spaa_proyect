@extends('layouts.guest')

@section('title', 'Crear cuenta')

@section('content')
<h1 class="text-3xl">Crea tu cuenta</h1>
<p class="mb-6 mt-1 text-sm text-ink-soft">Regístrate para agendar y consultar tus citas.</p>

<form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4" novalidate aria-label="Formulario de registro">
    @csrf

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-form-field name="nombre" label="Nombre" :required="true" autocomplete="given-name" autofocus />
        <x-form-field name="apellido" label="Apellido" :required="true" autocomplete="family-name" />
    </div>

    {{--
        El tipo de cuenta es SOLO informativo: el servidor siempre crea la
        cuenta con el rol Cliente (AuthController@register), sin importar
        lo que se envíe en el formulario.
    --}}
    <div class="rounded-xl border border-line bg-subtle px-4 py-3">
        <p class="etiqueta">Tipo de cuenta</p>
        <p class="mt-0.5 flex items-center gap-2 text-sm text-ink">
            <span class="h-2 w-2 rounded-full bg-primary" aria-hidden="true"></span> Cliente
        </p>
        <p class="mt-1 text-xs text-ink-muted">Por seguridad, los roles del equipo y de administrador los asigna un administrador del sistema.</p>
    </div>

    <x-form-field name="documento" label="Número de documento" :required="true" inputmode="numeric" />
    <x-form-field name="email" label="Correo electrónico" type="email" :required="true" placeholder="nombre@correo.com" autocomplete="email" />
    <x-form-field name="telefono" label="Teléfono" type="tel" placeholder="3001234567" autocomplete="tel" />

    <x-password-field name="password" label="Contraseña" autocomplete="new-password"
                      help="Mínimo 8 caracteres, con mayúsculas, minúsculas y números." />
    <x-password-field name="password_confirmation" label="Confirmar contraseña" autocomplete="new-password" />

    <x-button type="submit" class="mt-2 w-full py-2.5">Crear cuenta</x-button>
</form>

<p class="mt-8 text-center text-sm text-ink-muted">
    ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="enlace">Inicia sesión</a>
</p>
@endsection
