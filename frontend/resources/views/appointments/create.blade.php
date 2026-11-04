@extends('layouts.app')

@section('title', 'Nueva cita')

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
    <h1 class="text-2xl" style="font-family: var(--font-serif);">Agendar nueva cita</h1>
    <p class="text-sm mt-1" style="color: var(--color-charcoal-light);">
        Completa los campos para registrar una cita. Los campos marcados con <span style="color:var(--color-red-soft);">*</span> son obligatorios.
    </p>
</div>

@if ($modoDemo)
    <div
        class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm mb-6"
        style="background-color: var(--color-amber-light); color: #92650A; border: 1px solid var(--color-amber-soft);"
        role="status"
    >
        <span aria-hidden="true">⚠</span>
        <span><strong>Modo demostración:</strong> Los selectores de cliente, empleado y servicio están vacíos porque la base de datos no está conectada. Este formulario no puede guardarse en este estado.</span>
    </div>
@endif

{{--
    Formulario de creación.
    - action: ruta POST /citas
    - @csrf: protección obligatoria contra falsificación de peticiones
--}}
<form
    method="POST"
    action="{{ route('citas.store') }}"
    novalidate
    aria-label="Formulario para agendar una nueva cita"
>
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== COLUMNA PRINCIPAL (2/3) ===== --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Grupo: Identificación de la cita --}}
            <x-card>
                <x-slot name="header">
                    <h2 class="text-base font-semibold" style="font-family: var(--font-sans); color: var(--color-charcoal);">
                        Datos de la cita
                    </h2>
                </x-slot>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <x-form-field
                        name="codigo_cita"
                        label="Código de cita"
                        type="number"
                        :required="true"
                        placeholder="Ej: 1001"
                        help="Número único que identifica esta cita."
                    />

                    <x-form-field
                        name="fecha"
                        label="Fecha"
                        type="date"
                        :required="true"
                    />

                    <x-form-field
                        name="hora"
                        label="Hora"
                        type="time"
                        :required="true"
                        help="Formato 24h. Ej: 09:30"
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

                    {{-- Selector de cliente --}}
                    <x-form-field
                        name="cliente_id"
                        label="Cliente"
                        type="select"
                        :required="true"
                        placeholder="Selecciona un cliente"
                        :options="$clientes->mapWithKeys(function ($c) {
                            $nombre = $c->usuario
                                ? $c->usuario->nombre . ' ' . $c->usuario->apellido
                                : 'Cliente #' . $c->cliente_id;
                            return [$c->cliente_id => $nombre];
                        })->toArray()"
                    />

                    {{-- Selector de servicio --}}
                    <x-form-field
                        name="servicio_id"
                        label="Servicio"
                        type="select"
                        :required="true"
                        placeholder="Selecciona un servicio"
                        :options="$servicios->mapWithKeys(function ($s) {
                            return [$s->servicio_id => $s->nombre . ' — $' . number_format($s->precio, 2)];
                        })->toArray()"
                    />

                    {{-- Selector de empleado --}}
                    <x-form-field
                        name="empleado_id"
                        label="Empleado asignado"
                        type="select"
                        :required="true"
                        placeholder="Selecciona un empleado"
                        :options="$empleados->mapWithKeys(function ($e) {
                            return [$e->empleado_id => $e->nombre . ' ' . $e->apellido . ' (' . $e->rol . ')'];
                        })->toArray()"
                        help="Solo muestra empleados disponibles."
                    />

                </div>
            </x-card>

        </div>

        {{-- ===== COLUMNA LATERAL (1/3) ===== --}}
        <div class="flex flex-col gap-6">

            {{-- Estado inicial --}}
            <x-card>
                <x-slot name="header">
                    <h2 class="text-base font-semibold" style="font-family: var(--font-sans); color: var(--color-charcoal);">
                        Estado
                    </h2>
                </x-slot>

                <x-form-field
                    name="estado"
                    label="Estado inicial"
                    type="select"
                    :options="[
                        'pendiente'  => 'Pendiente',
                        'confirmada' => 'Confirmada',
                    ]"
                    :value="old('estado', 'pendiente')"
                    help="Por lo general, una cita nueva empieza como Pendiente."
                />
            </x-card>

            {{-- Acciones --}}
            <x-card padding="md">
                <div class="flex flex-col gap-3">
                    <x-button type="submit" variant="primary" class="w-full justify-center">
                        Guardar cita
                    </x-button>
                    <x-button :href="route('citas.index')" variant="secondary" class="w-full justify-center">
                        Cancelar
                    </x-button>
                </div>
                <p class="text-xs mt-4 text-center" style="color: var(--color-charcoal-muted);">
                    La cita se confirmará solo después de que el servidor la guarde correctamente.
                </p>
            </x-card>

        </div>
    </div>

</form>

@endsection
