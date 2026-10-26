@extends('layouts.app')

@section('title', 'Empleados')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl" style="font-family: var(--font-serif);">Equipo de trabajo</h1>
        <p class="text-sm mt-1" style="color: var(--color-charcoal-light);">
            Personal registrado en Asha Spa.
        </p>
    </div>
</div>

@if ($modoDemo)
    <div
        class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm mb-6"
        style="background-color: var(--color-amber-light); color: #92650A; border: 1px solid var(--color-amber-soft);"
        role="status"
    >
        <span aria-hidden="true">⚠</span>
        <span><strong>Modo demostración:</strong> La base de datos no está conectada.</span>
    </div>
@endif

<x-card padding="none">
    @if ($empleados->isEmpty())
        <x-empty-state
            title="Aún no hay empleados registrados"
            message="El equipo aparecerá aquí cuando se registren empleados en el sistema."
            icon="💼"
        />
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm" aria-label="Lista de empleados">
                <thead>
                    <tr style="border-bottom: 2px solid var(--color-beige); background-color: var(--color-cream);">
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--color-charcoal-muted);">
                            Nombre
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider hidden sm:table-cell"
                            style="color: var(--color-charcoal-muted);">
                            Rol
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider hidden md:table-cell"
                            style="color: var(--color-charcoal-muted);">
                            Contacto
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--color-charcoal-muted);">
                            Disponibilidad
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($empleados as $empleado)
                        <tr style="border-bottom: 1px solid var(--color-beige);">
                            <td class="px-6 py-4" style="color: var(--color-charcoal);">
                                <div class="font-medium">
                                    {{ $empleado->nombre }} {{ $empleado->apellido }}
                                </div>
                                <div class="text-xs" style="color: var(--color-charcoal-muted);">
                                    Doc: {{ $empleado->documento }}
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell" style="color: var(--color-charcoal-light);">
                                {{ ucfirst($empleado->rol) }}
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell" style="color: var(--color-charcoal-light);">
                                <div>{{ $empleado->correo ?? '—' }}</div>
                                <div class="text-xs">{{ $empleado->telefono ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$empleado->disponibilidad ? 'activo' : 'inactivo'" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($empleados->hasPages())
            <div class="px-6 py-4" style="border-top: 1px solid var(--color-beige);">
                {{ $empleados->links() }}
            </div>
        @endif
    @endif
</x-card>

@endsection
