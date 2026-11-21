@extends('layouts.guest')

@section('title', 'Nueva contraseña')

@section('content')
<h1 class="text-3xl">Crea tu nueva contraseña</h1>
<p class="mb-8 mt-1 text-sm text-ink-soft">Elige una contraseña que no hayas usado antes.</p>

<form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <x-form-field name="email" label="Correo electrónico" type="email" :required="true"
                  :value="$email" autocomplete="email" />

    <x-password-field name="password" label="Nueva contraseña" autocomplete="new-password"
                      help="Mínimo 8 caracteres, con mayúsculas, minúsculas y números." autofocus />
    <x-password-field name="password_confirmation" label="Confirmar nueva contraseña" autocomplete="new-password" />

    <x-button type="submit" class="w-full py-2.5">Guardar nueva contraseña</x-button>
</form>

<p class="mt-8 text-center text-sm text-ink-muted">
    ¿El enlace venció? <a href="{{ route('password.request') }}" class="enlace">Pide uno nuevo</a>
</p>
@endsection
