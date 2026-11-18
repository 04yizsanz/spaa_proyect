@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<x-page-header title="Clientes" subtitle="Personas registradas en el spa.">
    @can('create', App\Models\Cliente::class)
        <x-button :href="route('clientes.create')">Nuevo cliente</x-button>
    @endcan
</x-page-header>

<x-search-bar :action="route('clientes.index')" placeholder="Buscar por nombre, documento o correo" />

@if ($clientes->isEmpty())
    <x-card>
        <x-empty-state title="No hay clientes para mostrar"
                       message="Los clientes se registran desde la web o en el mostrador."
                       :action="auth()->user()->can('create', App\Models\Cliente::class) ? 'Nuevo cliente' : null"
                       :href="route('clientes.create')" />
    </x-card>
@else
    <x-table label="Lista de clientes" :paginator="$clientes">
        <x-slot:head>
            <th scope="col">Cliente</th>
            <th scope="col" class="hidden sm:table-cell">Documento</th>
            <th scope="col" class="hidden md:table-cell">Correo</th>
            <th scope="col" class="hidden lg:table-cell">Teléfono</th>
            <th scope="col"><span class="sr-only">Acciones</span></th>
        </x-slot:head>
        @foreach ($clientes as $cliente)
            <tr>
                <td>
                    <a href="{{ route('clientes.show', $cliente->cliente_id) }}" class="flex items-center gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-soft text-xs font-semibold text-primary" aria-hidden="true">
                            {{ mb_substr($cliente->usuario?->nombre ?? '?', 0, 1) }}{{ mb_substr($cliente->usuario?->apellido ?? '', 0, 1) }}
                        </span>
                        <span class="font-medium text-ink hover:text-primary">{{ $cliente->usuario?->nombre }} {{ $cliente->usuario?->apellido }}</span>
                    </a>
                </td>
                <td class="hidden sm:table-cell tabular-nums">{{ $cliente->usuario?->documento ?? '—' }}</td>
                <td class="hidden md:table-cell">{{ $cliente->usuario?->email ?? '—' }}</td>
                <td class="hidden lg:table-cell">{{ $cliente->usuario?->telefono ?? '—' }}</td>
                <td class="text-right"><a href="{{ route('clientes.show', $cliente->cliente_id) }}" class="enlace text-sm">Ver</a></td>
            </tr>
        @endforeach
    </x-table>
@endif
@endsection
