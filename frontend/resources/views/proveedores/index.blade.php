@extends('layouts.app')

@section('title', 'Proveedores')

@section('content')
<x-page-header title="Proveedores" subtitle="Empresas que abastecen los productos del spa.">
    @can('create', App\Models\Proveedor::class)
        <x-button :href="route('proveedores.create')">Nuevo proveedor</x-button>
    @endcan
</x-page-header>

<x-search-bar :action="route('proveedores.index')" placeholder="Buscar por nombre, NIT, contacto o correo" />

@if ($proveedores->isEmpty())
    <x-card>
        <x-empty-state title="No hay proveedores para mostrar"
                       message="Registra a quienes te venden productos para controlar el inventario."
                       :action="auth()->user()->can('create', App\Models\Proveedor::class) ? 'Nuevo proveedor' : null"
                       :href="route('proveedores.create')" />
    </x-card>
@else
    <x-table label="Lista de proveedores" :paginator="$proveedores">
        <x-slot:head>
            <th scope="col">Proveedor</th>
            <th scope="col" class="hidden md:table-cell">NIT</th>
            <th scope="col" class="hidden md:table-cell">Contacto</th>
            <th scope="col" class="hidden lg:table-cell">Correo</th>
            <th scope="col"><span class="sr-only">Acciones</span></th>
        </x-slot:head>
        @foreach ($proveedores as $proveedor)
            <tr>
                <td><a href="{{ route('proveedores.show', $proveedor->proveedor_id) }}" class="font-medium text-ink hover:text-primary">{{ $proveedor->nombre }}</a></td>
                <td class="hidden md:table-cell tabular-nums">{{ $proveedor->registro_tributario }}</td>
                <td class="hidden md:table-cell">{{ $proveedor->contacto ?? '—' }}</td>
                <td class="hidden lg:table-cell">{{ $proveedor->email ?? '—' }}</td>
                <td class="text-right">
                    <a href="{{ route('proveedores.show', $proveedor->proveedor_id) }}" class="enlace text-sm">Ver</a>
                </td>
            </tr>
        @endforeach
    </x-table>
@endif
@endsection
