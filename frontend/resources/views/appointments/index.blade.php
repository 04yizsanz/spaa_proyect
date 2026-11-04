@extends('layouts.app')

@section('title', 'Citas')

@section('content')

{{-- Encabezado del módulo --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl mb-1" style="font-family: var(--font-serif);">Agenda de citas</h1>
        <p class="text-sm" style="color: var(--color-charcoal-light);">
            Consulta, filtra y gestiona todas las citas del spa.
        </p>
    </div>
    <x-button :href="route('citas.create')" variant="primary">
        + Nueva cita
    </x-button>
</div>

{{-- Aviso modo demo --}}
@if ($modoDemo)
    <div
        class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm mb-6"
        style="background-color: var(--color-amber-light); color: #92650A; border: 1px solid var(--color-amber-soft);"
        role="status"
    >
        <span aria-hidden="true">⚠</span>
        <span><strong>Modo demostración:</strong> La base de datos no está conectada. Ejecuta las migraciones para ver citas reales.</span>
    </div>
@endif

{{-- ===== FILTROS ===== --}}
{{--
    Los filtros usan GET para que la URL refleje la búsqueda activa.
    Esto permite compartir, marcar y navegar con el botón "atrás" correctamente.
--}}
<x-card padding="md" class="mb-6">
    <form
        method="GET"
        action="{{ route('citas.index') }}"
        role="search"
        aria-label="Filtrar citas"
    >
        <div class="flex flex-col sm:flex-row gap-4">

            {{-- Filtro: Fecha --}}
            <div class="flex-1">
                <label for="filtro-fecha" class="block text-sm font-medium mb-1.5"
                       style="color: var(--color-charcoal);">
                    Fecha
                </label>
                <input
                    type="date"
                    id="filtro-fecha"
                    name="fecha"
                    value="{{ $filtroFecha ?? '' }}"
                    class="block w-full px-3 py-2 rounded-lg text-sm"
                    style="border: 1px solid var(--color-beige-mid); background-color: var(--color-warm-white); color: var(--color-charcoal);"
                >
            </div>

            {{-- Filtro: Estado --}}
            <div class="flex-1">
                <label for="filtro-estado" class="block text-sm font-medium mb-1.5"
                       style="color: var(--color-charcoal);">
                    Estado
                </label>
                <select
                    id="filtro-estado"
                    name="estado"
                    class="block w-full px-3 py-2 rounded-lg text-sm"
                    style="border: 1px solid var(--color-beige-mid); background-color: var(--color-warm-white); color: var(--color-charcoal);"
                >
                    <option value="">Todos los estados</option>
                    <option value="pendiente"   {{ ($filtroEstado ?? '') === 'pendiente'   ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmada"  {{ ($filtroEstado ?? '') === 'confirmada'  ? 'selected' : '' }}>Confirmada</option>
                    <option value="completada"  {{ ($filtroEstado ?? '') === 'completada'  ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada"   {{ ($filtroEstado ?? '') === 'cancelada'   ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>

            {{-- Botones --}}
            <div class="flex items-end gap-2">
                <x-button type="submit" variant="primary">
                    Filtrar
                </x-button>

                {{-- Limpiar filtros: solo se muestra si hay algún filtro activo --}}
                @if ($filtroFecha || $filtroEstado)
                    <x-button :href="route('citas.index')" variant="secondary">
                        Limpiar
                    </x-button>
                @endif
            </div>

        </div>

        {{-- Indicador visual de filtros activos --}}
        @if ($filtroFecha || $filtroEstado)
            <p class="text-xs mt-3" style="color: var(--color-charcoal-muted);">
                Mostrando resultados filtrados
                @if ($filtroFecha) por fecha <strong>{{ \Carbon\Carbon::parse($filtroFecha)->format('d/m/Y') }}</strong> @endif
                @if ($filtroEstado) · estado <strong>{{ $filtroEstado }}</strong> @endif
            </p>
        @endif
    </form>
</x-card>

{{-- ===== TABLA DE CITAS ===== --}}
<x-card padding="none">

    @if (!$modoDemo && $citas->isEmpty())
        <x-empty-state
            title="Aún no hay citas para mostrar"
            :message="($filtroFecha || $filtroEstado)
                ? 'No hay citas que coincidan con los filtros aplicados. Prueba con otra fecha o estado.'
                : 'Cuando agendes citas, aparecerán aquí.'"
            :action="!($filtroFecha || $filtroEstado) ? 'Agendar una cita' : null"
            :href="!($filtroFecha || $filtroEstado) ? route('citas.create') : null"
            icon="📅"
        />
    @elseif ($modoDemo)
        <x-empty-state
            title="Base de datos sin conectar"
            message="Las citas aparecerán aquí cuando la base de datos esté disponible."
            icon="🔌"
        />
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm" aria-label="Lista de citas">
                <thead>
                    <tr style="border-bottom: 2px solid var(--color-beige); background-color: var(--color-cream);">
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--color-charcoal-muted);">
                            Fecha y hora
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--color-charcoal-muted);">
                            Cliente
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider hidden sm:table-cell"
                            style="color: var(--color-charcoal-muted);">
                            Servicio
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider hidden md:table-cell"
                            style="color: var(--color-charcoal-muted);">
                            Empleado
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--color-charcoal-muted);">
                            Estado
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <span class="sr-only">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($citas as $cita)
                        <tr
                            style="border-bottom: 1px solid var(--color-beige);"
                            class="transition-colors"
                        >
                            {{-- Fecha y hora --}}
                            <td class="px-6 py-4" style="color: var(--color-charcoal);">
                                <div class="font-medium tabular-nums">
                                    {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                </div>
                                <div class="text-xs tabular-nums" style="color: var(--color-charcoal-muted);">
                                    {{ \Carbon\Carbon::parse($cita->hora)->format('H:i') }}
                                </div>
                            </td>

                            {{-- Cliente (necesita relación con Usuario) --}}
                            <td class="px-6 py-4" style="color: var(--color-charcoal);">
                                @if ($cita->cliente && $cita->cliente->usuario)
                                    {{ $cita->cliente->usuario->nombre }} {{ $cita->cliente->usuario->apellido }}
                                @else
                                    <span style="color: var(--color-charcoal-muted);">Sin asignar</span>
                                @endif
                            </td>

                            {{-- Servicio --}}
                            <td class="px-6 py-4 hidden sm:table-cell" style="color: var(--color-charcoal-light);">
                                {{ $cita->servicio?->nombre ?? '—' }}
                            </td>

                            {{-- Empleado --}}
                            <td class="px-6 py-4 hidden md:table-cell" style="color: var(--color-charcoal-light);">
                                {{ $cita->empleado ? $cita->empleado->nombre . ' ' . $cita->empleado->apellido : '—' }}
                            </td>

                            {{-- Estado --}}
                            <td class="px-6 py-4">
                                <x-status-badge :status="$cita->estado" />
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4 text-right">
                                <x-button
                                    :href="route('citas.edit', $cita->citas_id)"
                                    variant="secondary"
                                    size="sm"
                                >
                                    Editar
                                </x-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($citas->hasPages())
            <div class="px-6 py-4" style="border-top: 1px solid var(--color-beige);">
                {{ $citas->links() }}
            </div>
        @endif
    @endif

</x-card>

@endsection
