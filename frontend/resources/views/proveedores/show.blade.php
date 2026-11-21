@extends('layouts.app')

@section('title', $proveedor->nombre)

@section('content')
<x-page-header :title="$proveedor->nombre" subtitle="Proveedor" :back="route('proveedores.index')">
    @can('update', $proveedor)
        <x-button :href="route('proveedores.edit', $proveedor->proveedor_id)" variant="secondary">Editar</x-button>
    @endcan
    @can('delete', $proveedor)
        <x-delete-button :action="route('proveedores.destroy', $proveedor->proveedor_id)" :name="$proveedor->nombre" />
    @endcan
</x-page-header>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <x-card>
        <x-slot:header><h2 class="text-lg">Datos</h2></x-slot:header>
        <dl class="divide-y divide-line">
            <x-detail label="NIT">{{ $proveedor->registro_tributario }}</x-detail>
            <x-detail label="Contacto">{{ $proveedor->contacto ?? '—' }}</x-detail>
            <x-detail label="Correo">
                @if ($proveedor->email) <a href="mailto:{{ $proveedor->email }}" class="enlace">{{ $proveedor->email }}</a> @else — @endif
            </x-detail>
        </dl>
    </x-card>

    <div class="lg:col-span-2">
        <x-table label="Productos de este proveedor">
            <x-slot:head>
                <th scope="col">Producto</th>
                <th scope="col" class="text-right">Existencias</th>
                <th scope="col" class="text-right">Precio</th>
            </x-slot:head>
            @forelse ($proveedor->producto as $producto)
                <tr>
                    <td><a href="{{ route('productos.show', $producto->producto_id) }}" class="font-medium text-ink hover:text-primary">{{ $producto->nombre }}</a></td>
                    <td class="text-right tabular-nums">{{ $producto->cantidad }}</td>
                    <td class="text-right"><x-money :value="$producto->precio" /></td>
                </tr>
            @empty
                <tr><td colspan="3" class="py-8 text-center text-ink-muted">Este proveedor aún no tiene productos registrados.</td></tr>
            @endforelse
        </x-table>
    </div>
</div>
@endsection
