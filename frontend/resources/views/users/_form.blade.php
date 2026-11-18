{{-- Formulario compartido por create y edit. Recibe $usuario, $roles y opcionalmente $esMiCuenta. --}}
@php $esMiCuenta = $esMiCuenta ?? false; @endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-form-field name="nombre" label="Nombre" :required="true" :value="$usuario->nombre" />
    <x-form-field name="apellido" label="Apellido" :required="true" :value="$usuario->apellido" />
    <x-form-field name="documento" label="Documento" :required="true" :value="$usuario->documento" inputmode="numeric" />
    <x-form-field name="telefono" label="Teléfono" type="tel" :value="$usuario->telefono" />
    <x-form-field name="email" label="Correo (usuario para entrar)" type="email" :required="true" :value="$usuario->email" class="sm:col-span-2" />

    <x-form-field name="rol_id" label="Rol" type="select" :required="true" :options="$roles" :value="$usuario->rol_id"
                  placeholder="Selecciona el rol" :disabled="$esMiCuenta"
                  :help="$esMiCuenta ? 'No puedes cambiar tu propio rol.' : 'Define qué puede ver y hacer la persona en el sistema.'" />
    @if ($esMiCuenta)
        <input type="hidden" name="rol_id" value="{{ $usuario->rol_id }}">
    @endif

    <x-password-field name="password" :label="$usuario->exists ? 'Nueva contraseña' : 'Contraseña inicial'" :required="! $usuario->exists"
                      autocomplete="new-password"
                      :help="$usuario->exists ? 'Déjala vacía para conservar la actual.' : 'Mínimo 8 caracteres, con mayúsculas, minúsculas y números.'" />

    <div class="sm:col-span-2">
        @if ($esMiCuenta)
            <input type="hidden" name="estado" value="1">
            <p class="text-sm text-ink-muted">Tu cuenta siempre permanece activa.</p>
        @else
            <x-toggle name="estado" label="Cuenta activa" :checked="$usuario->estado"
                      help="Una cuenta inactiva no puede iniciar sesión. Así se da de baja a alguien sin borrar su historial." />
        @endif
    </div>
</div>
