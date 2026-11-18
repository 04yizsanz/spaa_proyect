{{-- Formulario compartido por create y edit. Recibe $factura y $clientes. --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-form-field name="cliente_id" label="Cliente" type="select" :required="true" :options="$clientes"
                  :value="$factura->cliente_id" placeholder="Selecciona el cliente" class="sm:col-span-2" />
    <x-form-field name="fecha_hora" label="Fecha y hora" type="datetime-local" :required="true"
                  :value="$factura->fecha_hora?->format('Y-m-d\TH:i')" />
    <x-form-field name="impuestos" label="Impuestos (COP)" type="number" min="0" step="100" :value="$factura->impuestos"
                  help="El subtotal y el total los calcula el sistema con los servicios facturados." />
</div>
