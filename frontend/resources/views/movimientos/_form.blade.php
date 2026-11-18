{{-- Formulario compartido por create y edit. Recibe $movimiento y $productos. --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-form-field name="producto_id" label="Producto" type="select" :required="true" :options="$productos"
                  :value="$movimiento->producto_id" placeholder="Selecciona un producto" class="sm:col-span-2" />

    <fieldset class="flex flex-col gap-1.5">
        <legend class="etiqueta mb-1.5">Tipo de movimiento <span class="text-danger">*</span></legend>
        <div class="grid grid-cols-2 gap-2">
            @foreach (['entrada' => 'Entrada', 'salida' => 'Salida'] as $valor => $texto)
                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-line-strong px-3 py-2 text-sm has-[:checked]:border-primary has-[:checked]:bg-primary-soft has-[:checked]:text-primary">
                    <input type="radio" name="tipo" value="{{ $valor }}" class="accent-primary" @checked(old('tipo', $movimiento->tipo) === $valor)>
                    {{ $texto }}
                </label>
            @endforeach
        </div>
        @error('tipo') <p class="text-xs font-medium text-danger" role="alert">{{ $message }}</p> @enderror
    </fieldset>

    <x-form-field name="cantidad" label="Cantidad" type="number" min="1" step="1" :required="true" :value="$movimiento->cantidad" />
    <x-form-field name="fecha_hora" label="Fecha y hora" type="datetime-local" :required="true"
                  :value="$movimiento->fecha_hora?->format('Y-m-d\TH:i')" />
    <x-form-field name="motivo" label="Motivo" :value="$movimiento->motivo" placeholder="Compra al proveedor, uso en servicio…" />
</div>
