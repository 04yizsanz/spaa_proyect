@extends('layouts.app')

@section('title', 'Movimientos de inventario')

@section('content')
<x-page-header title="Movimientos de inventario" subtitle="Entradas y salidas de productos.">
    @can('create', App\Models\MovimientoInventario::class)
        <x-button :href="route('movimientos.create')">Registrar movimiento</x-button>
    @endcan
</x-page-header>

<x-search-bar :action="route('movimientos.index')" placeholder="Buscar por motivo">
    <label for="filtro-producto" class="sr-only">Producto</label>
    <select id="filtro-producto" name="producto" class="campo sm:w-56">
        <option value="">Todos los productos</option>
        @foreach ($productos as $id => $nombre)
            <option value="{{ $id }}" @selected(request('producto') == $id)>{{ $nombre }}</option>
        @endforeach
    </select>
    <label for="filtro-tipo" class="sr-only">Tipo</label>
    <select id="filtro-tipo" name="tipo" class="campo sm:w-40">
        <option value="">Entradas y salidas</option>
        <option value="entrada" @selected(request('tipo') === 'entrada')>Entradas</option>
        <option value="salida" @selected(request('tipo') === 'salida')>Salidas</option>
    </select>
</x-search-bar>

@if ($movimientos->isEmpty())
    <x-card>
        <x-empty-state title="No hay movimientos para mostrar"
                       message="Cada compra o uso de un producto se registra como entrada o salida." />
    </x-card>
@else
    <x-table label="Lista de movimientos de inventario" :paginator="$movimientos">
        <x-slot:head>
            <th scope="col">Fecha</th>
            <th scope="col">Producto</th>
            <th scope="col">Tipo</th>
            <th scope="col" class="text-right">Cantidad</th>
            <th scope="col" class="hidden md:table-cell">Motivo</th>
            <th scope="col"><span class="sr-only">Acciones</span></th>
        </x-slot:head>
        @foreach ($movimientos as $movimiento)
            <tr>
                <td class="tabular-nums whitespace-nowrap">{{ $movimiento->fecha_hora?->format('d/m/Y H:i') }}</td>
                <td>
                    @if ($movimiento->producto)
                        <a href="{{ route('productos.show', $movimiento->producto_id) }}" class="font-medium text-ink hover:text-primary">{{ $movimiento->producto->nombre }}</a>
                    @else <span class="text-ink-muted">Producto eliminado</span> @endif
                </td>
                <td><x-status-badge :status="$movimiento->tipo" /></td>
                <td class="text-right tabular-nums">{{ $movimiento->tipo === 'salida' ? '−' : '+' }}{{ $movimiento->cantidad }}</td>
                <td class="hidden md:table-cell">{{ $movimiento->motivo ?? '—' }}</td>
                <td class="text-right"><a href="{{ route('movimientos.show', $movimiento->movimiento_id) }}" class="enlace text-sm">Ver</a></td>
            </tr>
        @endforeach
    </x-table>
@endif
@endsection
