{{--
    Formulario compartido por create y edit de citas.
    Recibe: $cita, $clientes, $servicios, $empleados, $estados, $gestiona, $fechaMinima
    El código de la cita lo asigna el servidor (CitaService).
--}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="flex flex-col gap-6 lg:col-span-2">
        <x-card>
            <x-slot:header><h2 class="text-lg">¿Qué servicio y con quién?</h2></x-slot:header>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                @if ($gestiona)
                    <x-form-field name="cliente_id" label="Cliente" type="select" :required="true" placeholder="Selecciona un cliente"
                                  :value="$cita->cliente_id" class="sm:col-span-2"
                                  :options="$clientes->mapWithKeys(fn ($c) => [$c->cliente_id => $c->usuario ? $c->usuario->nombre . ' ' . $c->usuario->apellido . ' · ' . $c->usuario->documento : 'Cliente #' . $c->cliente_id])->sort()->all()" />
                @endif
                <x-form-field name="servicio_id" label="Servicio" type="select" :required="true" placeholder="Selecciona un servicio"
                              :value="$cita->servicio_id"
                              :options="$servicios->mapWithKeys(fn ($s) => [$s->servicio_id => $s->nombre . ((float) $s->precio > 0 ? ' · $ ' . number_format($s->precio, 0, ',', '.') : '')])->all()" />
                <x-form-field name="empleado_id" label="Te atiende" type="select" :required="true" placeholder="Selecciona a alguien del equipo"
                              :value="$cita->empleado_id" help="Solo aparece el personal disponible."
                              :options="$empleados->mapWithKeys(fn ($e) => [$e->empleado_id => $e->nombre . ' ' . $e->apellido])->all()" />
            </div>
        </x-card>

        <x-card>
            <x-slot:header><h2 class="text-lg">¿Cuándo?</h2></x-slot:header>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <x-form-field name="fecha" label="Fecha" type="date" :required="true" :min="$fechaMinima"
                              :value="$cita->fecha?->toDateString()" help="No es posible agendar en fechas pasadas." />
                <x-form-field name="hora" label="Hora" type="time" :required="true" min="09:00" max="21:00" step="60"
                              :value="$cita->hora ? substr((string) $cita->hora, 0, 5) : ''" help="Horario de atención: 09:00 a 21:00." />
            </div>
        </x-card>
    </div>

    <div class="flex flex-col gap-6">
        <x-card>
            <x-slot:header><h2 class="text-lg">Estado</h2></x-slot:header>
            @if (count($estados) > 1)
                <x-form-field name="estado" label="Estado de la cita" type="select" :options="$estados" :value="$cita->estado ?? 'pendiente'" />
            @else
                <input type="hidden" name="estado" value="{{ array_key_first($estados) }}">
                <x-status-badge :status="array_key_first($estados)" />
                <p class="mt-2 text-xs text-ink-muted">El spa confirmará tu cita.</p>
            @endif
        </x-card>
        <x-card padding="md">
            <div class="flex flex-col gap-2">
                <x-button type="submit" class="w-full">{{ $cita->exists ? 'Guardar cambios' : 'Agendar cita' }}</x-button>
                <x-button :href="route('citas.index')" variant="secondary" class="w-full">Cancelar</x-button>
            </div>
        </x-card>
    </div>
</div>

@push('scripts')
<script>
    // Ajusta la hora mínima cuando la fecha elegida es hoy (el servidor valida igual).
    (() => {
        const fecha = document.getElementById('field-fecha');
        const hora  = document.getElementById('field-hora');
        const ayuda = document.getElementById('help-hora');
        if (!fecha || !hora) return;
        const zona = @json($zonaHoraria);
        const [apertura, cierre] = ['09:00', '21:00'];

        function ahoraLocal() {
            const p = Object.fromEntries(new Intl.DateTimeFormat('en-CA', {
                timeZone: zona, year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hourCycle: 'h23',
            }).formatToParts(new Date()).map(({ type, value }) => [type, value]));
            return { fecha: `${p.year}-${p.month}-${p.day}`, hora: `${p.hour}:${p.minute}` };
        }

        function actualizar() {
            const ahora = ahoraLocal();
            const esHoy = fecha.value === ahora.fecha;
            let minima = apertura;
            if (esHoy && ahora.hora >= apertura) {
                const m = Number(ahora.hora.slice(0, 2)) * 60 + Number(ahora.hora.slice(3, 5)) + 1;
                minima = `${String(Math.floor(m / 60)).padStart(2, '0')}:${String(m % 60).padStart(2, '0')}`;
            }
            const sinCupo = esHoy && minima > cierre;
            hora.disabled = sinCupo;
            hora.min = minima;
            if (ayuda) {
                ayuda.textContent = sinCupo
                    ? 'Por hoy ya no hay horarios disponibles. Selecciona otra fecha.'
                    : 'Horario de atención: 09:00 a 21:00.' + (esHoy ? ' Para hoy, elige una hora futura.' : '');
            }
        }
        fecha.addEventListener('change', actualizar);
        actualizar();
    })();
</script>
@endpush
