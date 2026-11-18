{{-- Formulario compartido por create y edit. Recibe $producto y $proveedores. --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-form-field name="nombre" label="Nombre del producto" :required="true" :value="$producto->nombre" class="sm:col-span-2" placeholder="Shampoo hidratante 500 ml" />
    <x-form-field name="proveedor_id" label="Proveedor" type="select" :required="true" :options="$proveedores"
                  :value="$producto->proveedor_id" placeholder="Selecciona un proveedor" class="sm:col-span-2" />
    <x-form-field name="cantidad" label="Existencias" type="number" min="0" step="1" :required="true" :value="$producto->cantidad"
                  help="Unidades disponibles hoy." />
    <x-form-field name="precio" label="Precio unitario (COP)" type="number" min="0" step="100" :required="true" :value="$producto->precio" />
    <x-form-field name="fecha_registro" label="Fecha de registro" type="date" :required="true"
                  :value="$producto->fecha_registro ? \Illuminate\Support\Carbon::parse($producto->fecha_registro)->toDateString() : ''" />
</div>
@if (empty($proveedores))
    <x-alert type="warning" class="mt-5">Primero registra un proveedor: todo producto debe tener uno.</x-alert>
@endif
