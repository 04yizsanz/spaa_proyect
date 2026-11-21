@extends('layouts.app')

@section('title', 'Factura N.º ' . $factura->factura_id)

@section('content')
@php $numero = str_pad($factura->factura_id, 5, '0', STR_PAD_LEFT); @endphp

<x-page-header :title="'Factura N.º ' . $numero" :subtitle="$factura->fecha_hora?->format('d/m/Y H:i')" :back="route('facturas.index')">
    <x-button variant="secondary" onclick="window.print()">Imprimir</x-button>
    @if ($puedeEditar)
        <x-button :href="route('facturas.edit', $factura->factura_id)" variant="secondary">Editar datos</x-button>
    @endif
</x-page-header>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <x-card class="lg:col-span-2" padding="none">
        <div class="flex flex-col gap-1 border-b border-line p-6 sm:flex-row sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-muted">Facturado a</p>
                <p class="mt-1 font-medium text-ink">{{ $factura->cliente?->usuario?->nombre }} {{ $factura->cliente?->usuario?->apellido }}</p>
                <p class="text-sm text-ink-soft">{{ $factura->cliente?->usuario?->documento }} · {{ $factura->cliente?->usuario?->email }}</p>
            </div>
            <div class="sm:text-right">
                <p class="font-serif text-xl font-bold">Asha Spa</p>
                <p class="text-sm text-ink-muted">Factura N.º {{ $numero }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="tabla" aria-label="Servicios facturados">
                <thead><tr>
                    <th scope="col">Servicio</th>
                    <th scope="col" class="text-right">Cantidad</th>
                    <th scope="col" class="text-right">Precio unitario</th>
                    <th scope="col" class="text-right">Importe</th>
                </tr></thead>
                <tbody>
                    @forelse ($factura->servicios as $linea)
                        <tr>
                            <td class="font-medium text-ink">{{ $linea->servicio?->nombre ?? 'Servicio' }}</td>
                            <td class="text-right">
                                @if ($puedeEditar)
                                    <form method="POST" action="{{ route('facturas.servicios.update', [$factura->factura_id, $linea->servicio_id]) }}" class="inline-flex items-center justify-end gap-1">
                                        @csrf @method('PUT')
                                        <label for="cant-{{ $linea->servicio_id }}" class="sr-only">Cantidad</label>
                                        <input id="cant-{{ $linea->servicio_id }}" type="number" name="cantidad" min="1" max="99" value="{{ $linea->cantidad }}" class="campo w-16 py-1 text-right">
                                        <button type="submit" class="btn btn-sm btn-fantasma" title="Actualizar cantidad">✓</button>
                                    </form>
                                @else
                                    <span class="tabular-nums">{{ $linea->cantidad }}</span>
                                @endif
                            </td>
                            <td class="text-right"><x-money :value="$linea->precio_unitario" /></td>
                            <td class="text-right text-ink"><x-money :value="$linea->cantidad * $linea->precio_unitario" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-ink-muted">La factura aún no tiene servicios.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <dl class="ml-auto w-full max-w-xs space-y-2 border-t border-line p-6 text-sm">
            <div class="flex justify-between"><dt class="text-ink-muted">Subtotal</dt><dd><x-money :value="$factura->subtotal" /></dd></div>
            <div class="flex justify-between"><dt class="text-ink-muted">Impuestos</dt><dd><x-money :value="$factura->impuestos" /></dd></div>
            <div class="flex justify-between border-t border-line pt-2 text-base font-semibold text-ink"><dt>Total</dt><dd><x-money :value="$factura->total" /></dd></div>
        </dl>
    </x-card>

    @if ($puedeEditar)
        <x-card class="h-fit print:hidden">
            <x-slot:header>
                <h2 class="text-lg">Agregar servicio</h2>
                <p class="mt-1 text-sm text-ink-soft">El precio se toma del catálogo en este momento.</p>
            </x-slot:header>
            @if ($servicios->isEmpty())
                <p class="text-sm text-ink-muted">Todos los servicios activos ya están en la factura.</p>
            @else
                <form method="POST" action="{{ route('facturas.servicios.store', $factura->factura_id) }}" class="flex flex-col gap-4">
                    @csrf
                    <x-form-field name="servicio_id" label="Servicio" type="select" :required="true"
                                  :options="$servicios->mapWithKeys(fn ($s) => [$s->servicio_id => $s->nombre . ' · $ ' . number_format($s->precio, 0, ',', '.')])->all()"
                                  placeholder="Selecciona un servicio" />
                    <x-form-field name="cantidad" label="Cantidad" type="number" min="1" max="99" :value="1" :required="true" />
                    <x-button type="submit">Agregar a la factura</x-button>
                </form>
            @endif
        </x-card>
    @endif
</div>
@endsection
