@extends('layouts.app')

@section('title', $producto->nombre)

@section('content')
<x-page-header :title="$producto->nombre" subtitle="Producto" :back="route('productos.index')">
    @can('create', App\Models\MovimientoInventario::class)
        <x-button :href="route('movimientos.create', ['producto' => $producto->producto_id])" variant="secondary">Registrar movimiento</x-button>
    @endcan
    @can('update', $producto)
        <x-button :href="route('productos.edit', $producto->producto_id)" variant="secondary">Editar</x-button>
    @endcan
    @can('delete', $producto)
        <x-delete-button :action="route('productos.destroy', $producto->producto_id)" :name="$producto->nombre" />
    @endcan
</x-page-header>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <x-card padding="sm">
        <p class="text-sm text-ink-muted">Existencias</p>
        <p class="mt-1 text-3xl font-semibold tabular-nums {{ $producto->cantidad <= $stockMinimo ? 'text-warning' : 'text-ink' }}">{{ $producto->cantidad }}</p>
        @if ($producto->cantidad <= $stockMinimo) <p class="text-xs text-warning">Stock bajo</p> @endif
    </x-card>
    <x-card padding="sm">
        <p class="text-sm text-ink-muted">Precio unitario</p>
        <p class="mt-1 text-3xl font-semibold"><x-money :value="$producto->precio" /></p>
    </x-card>
    <x-card padding="sm">
        <p class="text-sm text-ink-muted">Proveedor</p>
        <p class="mt-1 text-lg font-medium">
            @if ($producto->proveedor)
                <a href="{{ route('proveedores.show', $producto->proveedor_id) }}" class="enlace">{{ $producto->proveedor->nombre }}</a>
            @else — @endif
        </p>
        <p class="text-xs text-ink-muted">Registrado el {{ \Illuminate\Support\Carbon::parse($producto->fecha_registro)->format('d/m/Y') }}</p>
    </x-card>
</div>

<x-table label="Últimos movimientos del producto">
    <x-slot:head>
        <th scope="col">Fecha</th>
        <th scope="col">Tipo</th>
        <th scope="col" class="text-right">Cantidad</th>
        <th scope="col" class="hidden md:table-cell">Motivo</th>
    </x-slot:head>
    @forelse ($movimientos as $movimiento)
        <tr>
            <td class="tabular-nums"><a href="{{ route('movimientos.show', $movimiento->movimiento_id) }}" class="hover:text-primary">{{ $movimiento->fecha_hora?->format('d/m/Y H:i') }}</a></td>
            <td><x-status-badge :status="$movimiento->tipo" /></td>
            <td class="text-right tabular-nums">{{ $movimiento->tipo === 'salida' ? '−' : '+' }}{{ $movimiento->cantidad }}</td>
            <td class="hidden md:table-cell">{{ $movimiento->motivo ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="py-8 text-center text-ink-muted">Sin movimientos registrados.</td></tr>
    @endforelse
</x-table>
@endsection
