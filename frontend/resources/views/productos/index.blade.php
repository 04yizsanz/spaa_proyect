@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<x-page-header title="Productos" subtitle="Inventario de productos de uso y venta.">
    @can('create', App\Models\MovimientoInventario::class)
        <x-button :href="route('movimientos.create')" variant="secondary">Registrar movimiento</x-button>
    @endcan
    @can('create', App\Models\Producto::class)
        <x-button :href="route('productos.create')">Nuevo producto</x-button>
    @endcan
</x-page-header>

<x-search-bar :action="route('productos.index')" placeholder="Buscar producto">
    <label for="filtro-proveedor" class="sr-only">Proveedor</label>
    <select id="filtro-proveedor" name="proveedor" class="campo sm:w-56">
        <option value="">Todos los proveedores</option>
        @foreach ($proveedores as $id => $nombre)
            <option value="{{ $id }}" @selected(request('proveedor') == $id)>{{ $nombre }}</option>
        @endforeach
    </select>
</x-search-bar>

@if ($productos->isEmpty())
    <x-card>
        <x-empty-state title="No hay productos para mostrar"
                       message="Registra los productos para llevar el control de existencias."
                       :action="auth()->user()->can('create', App\Models\Producto::class) ? 'Nuevo producto' : null"
                       :href="route('productos.create')" />
    </x-card>
@else
    <x-table label="Lista de productos" :paginator="$productos">
        <x-slot:head>
            <th scope="col">Producto</th>
            <th scope="col" class="hidden md:table-cell">Proveedor</th>
            <th scope="col" class="text-right">Existencias</th>
            <th scope="col" class="hidden text-right sm:table-cell">Precio</th>
            <th scope="col"><span class="sr-only">Acciones</span></th>
        </x-slot:head>
        @foreach ($productos as $producto)
            <tr>
                <td><a href="{{ route('productos.show', $producto->producto_id) }}" class="font-medium text-ink hover:text-primary">{{ $producto->nombre }}</a></td>
                <td class="hidden md:table-cell">{{ $producto->proveedor?->nombre ?? '—' }}</td>
                <td class="text-right">
                    <span class="tabular-nums">{{ $producto->cantidad }}</span>
                    @if ($producto->cantidad <= $stockMinimo)
                        <span class="ml-2 rounded-full bg-warning-soft px-2 py-0.5 text-xs font-semibold text-warning">Stock bajo</span>
                    @endif
                </td>
                <td class="hidden text-right sm:table-cell"><x-money :value="$producto->precio" /></td>
                <td class="text-right"><a href="{{ route('productos.show', $producto->producto_id) }}" class="enlace text-sm">Ver</a></td>
            </tr>
        @endforeach
    </x-table>
@endif
@endsection
