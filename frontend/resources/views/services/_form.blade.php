{{-- Formulario compartido por create y edit. Recibe $servicio. --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-form-field name="nombre" label="Nombre del servicio" :required="true" :value="$servicio->nombre" class="sm:col-span-2" placeholder="Corte y peinado" />
    <x-form-field name="precio" label="Precio (COP)" type="number" min="0" step="100" :required="true" :value="$servicio->precio" />
    <x-form-field name="duracion_min" label="Duración (minutos)" type="number" min="5" step="5" :required="true" :value="$servicio->duracion_min" />
    <x-form-field name="descripcion" label="Descripción" type="textarea" :value="$servicio->descripcion" class="sm:col-span-2"
                  placeholder="Qué incluye el servicio y recomendaciones para el cliente." />
    <div class="sm:col-span-2">
        <x-toggle name="activo" label="Disponible para agendar" :checked="$servicio->activo"
                  help="Los servicios inactivos no aparecen a los clientes ni al agendar citas." />
    </div>
</div>
