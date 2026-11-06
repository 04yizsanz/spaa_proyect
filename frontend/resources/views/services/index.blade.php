@extends('layouts.app')

@section('title', 'Servicios')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl" style="font-family: var(--font-serif);">Servicios</h1>
        <p class="text-sm mt-1" style="color: var(--color-charcoal-light);">
            Referencias de precios públicos consultados en Bogotá D.C., Colombia (4 nov. 2026); no son tarifas oficiales. Las medianas de manicure y pedicure se calcularon con tres listados cada una y solo describen esa muestra. Coloraciones, alisados y depilación por zona pueden requerir cotización. Valores en pesos colombianos (COP).
        </p>
        <p class="text-xs mt-2" style="color: var(--color-charcoal-muted);">
            Fuentes: <a href="https://www.fresha.com/es/a/spa-sandra-garcia-bogota-d-c-calle-77a-113a-28-hbb2wr6r" target="_blank" rel="noopener noreferrer" class="underline">Spa Sandra García</a>,
            <a href="https://www.fresha.com/a/guapisimas-spa-fontibon-centro-bogota-cl-17-99-89-igfmru4s" target="_blank" rel="noopener noreferrer" class="underline">Guapísimas Spa</a>,
            <a href="https://www.fresha.com/es/a/biospa-vip-bogota-carrera-18-84-67-j2kgw9jh" target="_blank" rel="noopener noreferrer" class="underline">BioSpa VIP</a>,
            <a href="https://www.fresha.com/es/a/glam-spa-bogota-sede-restrepo-bogota-d-c-carrera-24-16-39-pbkk8tst" target="_blank" rel="noopener noreferrer" class="underline">Glam Spa Bogotá</a>,
            <a href="https://www.fresha.com/es/a/lolas-peluqueria-bogota-calle-79a-8-63-ji94i6aj" target="_blank" rel="noopener noreferrer" class="underline">Lolas Peluquería</a> y
            <a href="https://www.fresha.com/es/a/fk-peluqueria-asesoria-de-imagen-bogota-carrera-113-77-43-l1m2jhoj" target="_blank" rel="noopener noreferrer" class="underline">FK Peluquería</a> y
            <a href="https://www.fresha.com/es/a/jose-luis-style-bogota-carrera-13-63-50-xejgkfy4" target="_blank" rel="noopener noreferrer" class="underline">José Luis Style</a>.
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
                                        {{ \Illuminate\Support\Str::limit($servicio->descripcion, 180) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell tabular-nums"
                                style="color: var(--color-charcoal-light);">
                                {{ $servicio->duracion_min }} min
                            </td>
                            <td class="px-6 py-4 font-medium tabular-nums"
                                style="color: var(--color-charcoal);">
                                COP ${{ number_format($servicio->precio, 0, ',', '.') }}
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
