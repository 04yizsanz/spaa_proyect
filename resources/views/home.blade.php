@extends('layouts.app')

@section('title', 'Inicio')

@section('content')

{{-- ===== SECCIÓN A: Saludo y encabezado ===== --}}
<div class="mb-8">
    <h1 class="text-3xl mb-1" style="font-family: var(--font-serif);">
        Hola, qué bueno tenerte aquí
    </h1>
    <p class="text-base" style="color: var(--color-charcoal-light);">
        Hoy es {{ $hoy->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}.
        @if ($totalCitasHoy > 0)
            Tienes <strong>{{ $totalCitasHoy }} {{ $totalCitasHoy === 1 ? 'cita' : 'citas' }}</strong> programadas para hoy.
        @else
            No hay citas programadas para hoy.
        @endif
    </p>
</div>

{{-- Aviso de modo demostración --}}
@if ($modoDemo)
    <div
        class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm mb-8"
        style="background-color: var(--color-amber-light); color: #92650A; border: 1px solid var(--color-amber-soft);"
        role="status"
    >
        <span aria-hidden="true">⚠</span>
        <span>
            <strong>Modo demostración:</strong>
            La base de datos no está conectada aún. Los contadores muestran ceros y la agenda está vacía.
            Cuando ejecutes las migraciones, aquí aparecerán los datos reales.
        </span>
    </div>
@endif

{{-- ===== SECCIÓN B: Resumen del día ===== --}}
<section aria-labelledby="resumen-heading" class="mb-8">
    <h2 id="resumen-heading" class="text-lg font-semibold mb-4" style="font-family: var(--font-sans);">
        Resumen del día
    </h2>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

        {{-- Tarjeta: Citas hoy --}}
        <x-card padding="md">
            <p class="text-xs font-medium uppercase tracking-wider mb-2"
               style="color: var(--color-charcoal-muted); letter-spacing: 0.08em;">
                Citas hoy
            </p>
            <p class="text-3xl font-bold" style="font-family: var(--font-serif); color: var(--color-terracotta);">
                {{ $totalCitasHoy }}
            </p>
            <p class="text-xs mt-1" style="color: var(--color-charcoal-muted);">
                {{ $hoy->format('d/m/Y') }}
            </p>
        </x-card>

        {{-- Tarjeta: Clientes --}}
        <x-card padding="md">
            <p class="text-xs font-medium uppercase tracking-wider mb-2"
               style="color: var(--color-charcoal-muted); letter-spacing: 0.08em;">
                Clientes
            </p>
            <p class="text-3xl font-bold" style="font-family: var(--font-serif); color: var(--color-charcoal);">
                {{ $totalClientes }}
            </p>
            <p class="text-xs mt-1" style="color: var(--color-charcoal-muted);">registrados</p>
        </x-card>

        {{-- Tarjeta: Empleados disponibles --}}
        <x-card padding="md">
            <p class="text-xs font-medium uppercase tracking-wider mb-2"
               style="color: var(--color-charcoal-muted); letter-spacing: 0.08em;">
                Equipo
            </p>
            <p class="text-3xl font-bold" style="font-family: var(--font-serif); color: var(--color-sage-dark);">
                {{ $totalEmpleados }}
            </p>
            <p class="text-xs mt-1" style="color: var(--color-charcoal-muted);">disponibles hoy</p>
        </x-card>

        {{-- Tarjeta: Servicios activos --}}
        <x-card padding="md">
            <p class="text-xs font-medium uppercase tracking-wider mb-2"
               style="color: var(--color-charcoal-muted); letter-spacing: 0.08em;">
                Servicios
            </p>
            <p class="text-3xl font-bold" style="font-family: var(--font-serif); color: var(--color-charcoal);">
                {{ $totalServicios }}
            </p>
            <p class="text-xs mt-1" style="color: var(--color-charcoal-muted);">activos</p>
        </x-card>

    </div>
</section>

{{-- ===== SECCIÓN C: Agenda de hoy ===== --}}
<section aria-labelledby="agenda-heading" class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 id="agenda-heading" class="text-lg font-semibold" style="font-family: var(--font-sans);">
            Agenda de hoy
        </h2>
        <x-button :href="route('citas.create')" variant="primary" size="sm">
            + Agendar cita
        </x-button>
    </div>

    <x-card padding="none">
        @if ($citasHoy->isEmpty())
            <x-empty-state
                title="Aún no hay citas para hoy"
                message="Cuando agendes una cita para hoy, aparecerá aquí."
                action="Agendar una cita"
                :href="route('citas.create')"
                icon="📅"
            />
        @else
            {{-- Tabla de citas accesible --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm" aria-label="Citas programadas para hoy">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--color-beige);">
                            <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--color-charcoal-muted);">
                                Hora
                            </th>
                            <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--color-charcoal-muted);">
                                Cliente
                            </th>
                            <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider hidden sm:table-cell"
                                style="color: var(--color-charcoal-muted);">
                                Servicio
                            </th>
                            <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider hidden md:table-cell"
                                style="color: var(--color-charcoal-muted);">
                                Empleado
                            </th>
                            <th class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                                style="color: var(--color-charcoal-muted);">
                                Estado
                            </th>
                            <th class="px-6 py-3">
                                <span class="sr-only">Acciones</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($citasHoy as $cita)
                            <tr
                                style="border-bottom: 1px solid var(--color-beige);"
                                class="transition-colors"
                            >
                                <td class="px-6 py-4 font-medium tabular-nums" style="color: var(--color-charcoal);">
                                    {{ \Illuminate\Support\Carbon::parse($cita->hora)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4" style="color: var(--color-charcoal);">
                                    @if ($cita->cliente && $cita->cliente->usuario)
                                        {{ $cita->cliente->usuario->nombre }} {{ $cita->cliente->usuario->apellido }}
                                    @else
                                        <span style="color: var(--color-charcoal-muted);">Sin asignar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell" style="color: var(--color-charcoal-light);">
                                    {{ $cita->servicio?->nombre ?? '—' }}
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell" style="color: var(--color-charcoal-light);">
                                    {{ $cita->empleado ? $cita->empleado->nombre . ' ' . $cita->empleado->apellido : '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-status-badge :status="$cita->estado" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <x-button
                                        :href="route('citas.edit', $cita->citas_id)"
                                        variant="secondary"
                                        size="sm"
                                    >
                                        Ver
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    @if (!$citasHoy->isEmpty())
        <div class="mt-3 text-right">
            <a
                href="{{ route('citas.index') }}"
                class="text-sm font-medium"
                style="color: var(--color-terracotta);"
            >
                Ver todas las citas →
            </a>
        </div>
    @endif
</section>

{{-- ===== SECCIÓN D: Accesos rápidos ===== --}}
<section aria-labelledby="accesos-heading">
    <h2 id="accesos-heading" class="text-lg font-semibold mb-4" style="font-family: var(--font-sans);">
        Módulos del sistema
    </h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">

        {{-- Acceso: Citas --}}
        <a href="{{ route('citas.index') }}" class="group block">
            <x-card padding="md" class="h-full text-center transition-all duration-150 group-hover:shadow-md">
                <div class="text-2xl mb-2" aria-hidden="true">📋</div>
                <p class="text-sm font-semibold" style="color: var(--color-charcoal);">Citas</p>
                <p class="text-xs mt-1" style="color: var(--color-charcoal-muted);">Agenda y gestión</p>
            </x-card>
        </a>

        {{-- Acceso: Clientes --}}
        <a href="{{ route('clientes.index') }}" class="group block">
            <x-card padding="md" class="h-full text-center transition-all duration-150 group-hover:shadow-md">
                <div class="text-2xl mb-2" aria-hidden="true">👤</div>
                <p class="text-sm font-semibold" style="color: var(--color-charcoal);">Clientes</p>
                <p class="text-xs mt-1" style="color: var(--color-charcoal-muted);">Información y registro</p>
            </x-card>
        </a>

        {{-- Acceso: Servicios --}}
        <a href="{{ route('servicios.index') }}" class="group block">
            <x-card padding="md" class="h-full text-center transition-all duration-150 group-hover:shadow-md">
                <div class="text-2xl mb-2" aria-hidden="true">✨</div>
                <p class="text-sm font-semibold" style="color: var(--color-charcoal);">Servicios</p>
                <p class="text-xs mt-1" style="color: var(--color-charcoal-muted);">Catálogo y precios</p>
            </x-card>
        </a>

        {{-- Acceso: Empleados --}}
        <a href="{{ route('empleados.index') }}" class="group block">
            <x-card padding="md" class="h-full text-center transition-all duration-150 group-hover:shadow-md">
                <div class="text-2xl mb-2" aria-hidden="true">💼</div>
                <p class="text-sm font-semibold" style="color: var(--color-charcoal);">Empleados</p>
                <p class="text-xs mt-1" style="color: var(--color-charcoal-muted);">Personal disponible</p>
            </x-card>
        </a>

        {{-- Próximamente: Ventas --}}
        <div aria-label="Ventas — Módulo en desarrollo">
            <x-card padding="md" class="h-full text-center opacity-60 cursor-default">
                <div class="text-2xl mb-2" aria-hidden="true">💳</div>
                <p class="text-sm font-semibold" style="color: var(--color-charcoal);">Ventas</p>
                <p class="text-xs mt-1.5">
                    <span
                        class="px-2 py-0.5 rounded-full text-xs font-medium"
                        style="background-color: var(--color-beige); color: var(--color-charcoal-muted);"
                    >
                        Próximamente
                    </span>
                </p>
            </x-card>
        </div>

    </div>
</section>

@endsection
