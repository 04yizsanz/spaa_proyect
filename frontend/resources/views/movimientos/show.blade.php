@extends('layouts.app')

@section('title', 'Movimiento de inventario')

@section('content')
<x-page-header :title="ucfirst($movimiento->tipo) . ' de inventario'" :subtitle="$movimiento->fecha_hora?->format('d/m/Y H:i')" :back="route('movimientos.index')">
    @can('update', $movimiento)
        <x-button :href="route('movimientos.edit', $movimiento->movimiento_id)" variant="secondary">Editar</x-button>
    @endcan
    @can('delete', $movimiento)
        <x-delete-button :action="route('movimientos.destroy', $movimiento->movimiento_id)" name="este movimiento" />
    @endcan
</x-page-header>

<x-card class="max-w-3xl">
    <dl class="divide-y divide-line">
        <x-detail label="Producto">
            @if ($movimiento->producto)
                <a href="{{ route('productos.show', $movimiento->producto_id) }}" class="enlace">{{ $movimiento->producto->nombre }}</a>
            @else — @endif
        </x-detail>
        <x-detail label="Tipo"><x-status-badge :status="$movimiento->tipo" /></x-detail>
        <x-detail label="Cantidad"><span class="tabular-nums">{{ $movimiento->cantidad }}</span></x-detail>
        <x-detail label="Fecha y hora">{{ $movimiento->fecha_hora?->format('d/m/Y H:i') }}</x-detail>
        <x-detail label="Motivo">{{ $movimiento->motivo ?? '—' }}</x-detail>
    </dl>
</x-card>
@endsection
