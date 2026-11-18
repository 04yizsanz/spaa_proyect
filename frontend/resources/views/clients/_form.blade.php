{{-- Formulario compartido por create y edit. Recibe $cliente y $usuario (su cuenta). --}}
<h2 class="mb-4 font-sans text-sm font-semibold uppercase tracking-wider text-ink-muted">Datos personales</h2>
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-form-field name="nombre" label="Nombre" :required="true" :value="$usuario->nombre" />
    <x-form-field name="apellido" label="Apellido" :required="true" :value="$usuario->apellido" />
    <x-form-field name="documento" label="Documento" :required="true" :value="$usuario->documento" inputmode="numeric" />
    <x-form-field name="telefono" label="Teléfono" type="tel" :value="$usuario->telefono" />
    <x-form-field name="email" label="Correo electrónico" type="email" :required="true" :value="$usuario->email" class="sm:col-span-2" />
    @unless ($cliente->exists)
        <x-password-field name="password" label="Contraseña inicial" autocomplete="new-password"
                          help="El cliente la usará para entrar. Mínimo 8 caracteres, con mayúsculas, minúsculas y números." />
    @endunless
</div>

<h2 class="mb-4 mt-8 font-sans text-sm font-semibold uppercase tracking-wider text-ink-muted">Ficha del cliente</h2>
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-form-field name="fecha_nacimiento" label="Fecha de nacimiento" type="date" :max="now()->subDay()->toDateString()"
                  :value="$cliente->fecha_nacimiento?->toDateString()" />
    <x-form-field name="preferencias" label="Preferencias y notas" type="textarea" :value="$cliente->preferencias"
                  placeholder="Alergias, productos preferidos, tipo de cabello…" class="sm:col-span-2" />
</div>
