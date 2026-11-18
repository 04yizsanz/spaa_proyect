{{-- Formulario compartido por create y edit. Recibe $pago, $citas y $metodos. --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-form-field name="codigo_cita" label="Cita que se paga" type="select" :required="true" :options="$citas"
                  :value="$pago->codigo_cita" placeholder="Selecciona la cita" class="sm:col-span-2" />
    <x-form-field name="monto" label="Monto (COP)" type="number" min="1" step="100" :required="true" :value="$pago->monto" />
    <x-form-field name="metodo" label="Método de pago" type="select" :required="true" :options="$metodos"
                  :value="$pago->metodo" placeholder="Selecciona el método" />
    <x-form-field name="fecha_hora" label="Fecha y hora del pago" type="datetime-local" :required="true"
                  :value="$pago->fecha_hora?->format('Y-m-d\TH:i')" />
</div>
<p class="mt-5 text-xs text-ink-muted">El pago queda <strong>pendiente</strong> hasta que alguien con permiso lo apruebe o rechace.</p>
