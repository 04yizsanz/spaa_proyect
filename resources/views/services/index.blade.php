@extends('layouts.app')

@section('title', 'Servicios')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl" style="font-family: var(--font-serif);">Servicios</h1>
        <p class="text-sm mt-1" style="color: var(--color-charcoal-light);">
            Catálogo de tratamientos y servicios disponibles en Asha Spa.
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
    @if ($servicios->isEmpty())
        <x-empty-state
            title="Aún no hay servicios registrados"
            message="El catálogo de servicios aparecerá aquí cuando se agreguen."
            icon="✨"
        />
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm" aria-label="Catálogo de servicios">
                <thead>
                    <tr style="border-bottom: 2px solid var(--color-beige); background-color: var(--color-cream);">
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--color-charcoal-muted);">
                            Servicio
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider hidden sm:table-cell"
                            style="color: var(--color-charcoal-muted);">
                            Duración
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--color-charcoal-muted);">
                            Precio
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--color-charcoal-muted);">
                            Estado
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servicios as $servicio)
                        <tr style="border-bottom: 1px solid var(--color-beige);">
                            <td class="px-6 py-4" style="color: var(--color-charcoal);">
                                <div class="font-medium">{{ $servicio->nombre }}</div>
                                @if ($servicio->descripcion)
                                    <div class="text-xs mt-0.5" style="color: var(--color-charcoal-muted);">
                                        {{ \Illuminate\Support\Str::limit($servicio->descripcion, 80) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell tabular-nums"
                                style="color: var(--color-charcoal-light);">
                                {{ $servicio->duracion_min }} min
                            </td>
                            <td class="px-6 py-4 font-medium tabular-nums"
                                style="color: var(--color-charcoal);">
                                ${{ number_format($servicio->precio, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$servicio->activo ? 'activo' : 'inactivo'" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($servicios->hasPages())
            <div class="px-6 py-4" style="border-top: 1px solid var(--color-beige);">
                {{ $servicios->links() }}
            </div>
        @endif
    @endif
</x-card>

@endsection
