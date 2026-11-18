{{-- Formulario compartido por create y edit. Recibe $empleado y $roles. --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-form-field name="nombre" label="Nombre" :required="true" :value="$empleado->nombre" />
    <x-form-field name="apellido" label="Apellido" :required="true" :value="$empleado->apellido" />
    <x-form-field name="documento" label="Documento" :required="true" :value="$empleado->documento" inputmode="numeric" />
    <x-form-field name="rol" label="Cargo" type="select" :required="true" :options="$roles" :value="$empleado->rol" placeholder="Selecciona el cargo" />
    <x-form-field name="correo" label="Correo" type="email" :value="$empleado->correo" />
    <x-form-field name="telefono" label="Teléfono" type="tel" :value="$empleado->telefono" />
    <x-form-field name="salario" label="Salario mensual (COP)" type="number" min="1" step="1000" :required="true" :value="$empleado->salario" />
    <x-form-field name="fecha_contratacion" label="Fecha de contratación" type="date" :required="true" :max="now()->toDateString()"
                  :value="$empleado->fecha_contratacion?->toDateString()" />
    <div class="sm:col-span-2">
        <x-toggle name="disponibilidad" label="Disponible para atender citas" :checked="$empleado->disponibilidad"
                  help="Si está apagado, no aparece al agendar (vacaciones, incapacidad…)." />
    </div>
</div>
<p class="mt-5 text-xs text-ink-muted">
    Este registro es la ficha laboral. Para que la persona pueda iniciar sesión,
    el administrador le crea una cuenta en <strong>Usuarios</strong>.
</p>
