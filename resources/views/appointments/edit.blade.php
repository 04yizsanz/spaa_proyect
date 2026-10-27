@extends('layouts.app')

@section('title', 'Editar cita')

@section('content')

{{-- Encabezado --}}
<div class="mb-6">
    <a
        href="{{ route('citas.index') }}"
        class="inline-flex items-center gap-1 text-sm mb-3"
        style="color: var(--color-charcoal-muted);"
    >
        ← Volver a citas
    </a>
    <div class="flex items-center gap-3">
        <h1 class="text-2xl" style="font-family: var(--font-serif);">Editar cita</h1>
        <x-status-badge :status="$cita->estado" />
    </div>
    <p class="text-sm mt-1" style="color: var(--color-charcoal-light);">
        Código de cita: <strong>#{{ $cita->codigo_cita }}</strong>
    </p>
</div>

{{--
    Formulario de edición.
    - method POST + @method('PUT'): los navegadores no soportan PUT directamente,
      Laravel lo resuelve con el campo oculto _method.
    - action: ruta PUT /citas/{id}
    - @csrf: protección obligatoria
--}}
<form
    method="POST"
    action="{{ route('citas.update', $cita->citas_id) }}"
    novalidate
    aria-label="Formulario para editar la cita"
>
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== COLUMNA PRINCIPAL (2/3) ===== --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Grupo: Datos de la cita --}}
            <x-card>
                <x-slot name="header">
                    <h2 class="text-base font-semibold" style="font-family: var(--font-sans); color: var(--color-charcoal);">
                        Datos de la cita
                    </h2>
                </x-slot>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    {{--
                        old() recupera el valor que el usuario envió si hubo un error de validación.
                        Si no hay old(), usa el valor actual del modelo.
                    --}}
                    <x-form-field
                        name="codigo_cita"
                        label="Código de cita"
                        type="number"
                        :required="true"
                        :value="$cita->codigo_cita"
                    />

                    <x-form-field
                        name="fecha"
                        label="Fecha"
                        type="date"
                        :required="true"
                        :value="$cita->fecha?->format('Y-m-d')"
                    />

                    <x-form-field
                        name="hora"
                        label="Hora"
                        type="time"
                        :required="true"
                        :value="$cita->hora ? \Illuminate\Support\Carbon::parse($cita->hora)->format('H:i') : ''"
                        help="Formato 24h."
                    />

                </div>
            </x-card>

            {{-- Grupo: Personas involucradas --}}
            <x-card>
                <x-slot name="header">
                    <h2 class="text-base font-semibold" style="font-family: var(--font-sans); color: var(--color-charcoal);">
                        Cliente, servicio y empleado
                    </h2>
                </x-slot>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <x-form-field
                        name="cliente_id"
                        label="Cliente"
                        type="select"
                        :required="true"
                        placeholder="Selecciona un cliente"
                        :value="$cita->cliente_id"
                        :options="$clientes->mapWithKeys(function ($c) {
                            $nombre = $c->usuario
                                ? $c->usuario->nombre . ' ' . $c->usuario->apellido
                                : 'Cliente #' . $c->cliente_id;
                            return [$c->cliente_id => $nombre];
                        })->toArray()"
                    />

                    <x-form-field
                        name="servicio_id"
                        label="Servicio"
                        type="select"
                        :required="true"
                        placeholder="Selecciona un servicio"
                        :value="$cita->servicio_id"
                        :options="$servicios->mapWithKeys(function ($s) {
                            return [$s->servicio_id => $s->nombre . ' — $' . number_format($s->precio, 2)];
                        })->toArray()"
                    />

                    <x-form-field
                        name="empleado_id"
                        label="Empleado asignado"
                        type="select"
                        :required="true"
                        placeholder="Selecciona un empleado"
                        :value="$cita->empleado_id"
                        :options="$empleados->mapWithKeys(function ($e) {
                            return [$e->empleado_id => $e->nombre . ' ' . $e->apellido . ' (' . $e->rol . ')'];
                        })->toArray()"
                    />

                </div>
            </x-card>

        </div>

        {{-- ===== COLUMNA LATERAL (1/3) ===== --}}
        <div class="flex flex-col gap-6">

            {{-- Estado --}}
            <x-card>
                <x-slot name="header">
                    <h2 class="text-base font-semibold" style="font-family: var(--font-sans); color: var(--color-charcoal);">
                        Estado
                    </h2>
                </x-slot>

                <x-form-field
                    name="estado"
                    label="Estado de la cita"
                    type="select"
                    :required="true"
                    :value="$cita->estado"
                    :options="[
                        'pendiente'  => 'Pendiente',
                        'confirmada' => 'Confirmada',
                        'completada' => 'Completada',
                        'cancelada'  => 'Cancelada',
                    ]"
                />
            </x-card>

            {{-- Información de registro --}}
            <x-card padding="md">
                <p class="text-xs font-semibold uppercase tracking-wider mb-3"
                   style="color: var(--color-charcoal-muted);">
                    Información del registro
                </p>
                <dl class="flex flex-col gap-2 text-sm">
                    <div class="flex justify-between">
                        <dt style="color: var(--color-charcoal-muted);">Creada</dt>
                        <dd style="color: var(--color-charcoal);">
                            {{ $cita->created_at?->format('d/m/Y H:i') ?? '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt style="color: var(--color-charcoal-muted);">Actualizada</dt>
                        <dd style="color: var(--color-charcoal);">
                            {{ $cita->updated_at?->format('d/m/Y H:i') ?? '—' }}
                        </dd>
                    </div>
                </dl>
            </x-card>

            {{-- Acciones --}}
            <x-card padding="md">
                <div class="flex flex-col gap-3">
                    <x-button type="submit" variant="primary" class="w-full justify-center">
                        Guardar cambios
                    </x-button>
                    <x-button :href="route('citas.index')" variant="secondary" class="w-full justify-center">
                        Cancelar
                    </x-button>
                </div>
            </x-card>

        </div>
    </div>

</form>

@endsection
