{{-- Formulario compartido por create y edit. Recibe $proveedor. --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-form-field name="nombre" label="Nombre o razón social" :required="true" :value="$proveedor->nombre" class="sm:col-span-2" />
    <x-form-field name="registro_tributario" label="NIT / registro tributario" :required="true" :value="$proveedor->registro_tributario" placeholder="900123456-7" />
    <x-form-field name="contacto" label="Persona de contacto" :value="$proveedor->contacto" />
    <x-form-field name="email" label="Correo electrónico" type="email" :value="$proveedor->email" placeholder="ventas@proveedor.com" class="sm:col-span-2" />
</div>
