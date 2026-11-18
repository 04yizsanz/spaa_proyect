@extends('layouts.app')

@php $nombre = trim(($cliente->usuario?->nombre ?? '') . ' ' . ($cliente->usuario?->apellido ?? '')); @endphp

@section('title', $nombre)

@section('content')
<x-page-header :title="$nombre" subtitle="Cliente" :back="auth()->user()->can('viewAny', App\Models\Cliente::class) ? route('clientes.index') : null">
    @can('create', App\Models\Cita::class)
        @unless (auth()->user()->esCliente())
            <x-button :href="route('citas.create')" variant="secondary">Agendar cita</x-button>
        @endunless
    @endcan
    @can('update', $cliente)
        <x-button :href="route('clientes.edit', $cliente->cliente_id)" variant="secondary">Editar</x-button>
    @endcan
    @can('delete', $cliente)
        <x-delete-button :action="route('clientes.destroy', $cliente->cliente_id)" :name="$nombre" />
    @endcan
</x-page-header>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <x-card>
        <x-slot:header><h2 class="text-lg">Datos</h2></x-slot:header>
        <dl class="divide-y divide-line">
            <x-detail label="Documento">{{ $cliente->usuario?->documento ?? '—' }}</x-detail>
            <x-detail label="Correo">{{ $cliente->usuario?->email ?? '—' }}</x-detail>
            <x-detail label="Teléfono">{{ $cliente->usuario?->telefono ?? '—' }}</x-detail>
            <x-detail label="Nacimiento">{{ $cliente->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</x-detail>
            <x-detail label="Preferencias"><span class="whitespace-pre-line">{{ $cliente->preferencias ?: '—' }}</span></x-detail>
        </dl>
    </x-card>

    <div class="lg:col-span-2">
        <h2 class="mb-3 text-lg">Últimas citas</h2>
        <x-table label="Últimas citas del cliente">
            <x-slot:head>
                <th scope="col">Fecha</th>
                <th scope="col">Servicio</th>
                <th scope="col" class="hidden sm:table-cell">Atiende</th>
                <th scope="col">Estado</th>
            </x-slot:head>
            @forelse ($citas as $cita)
                <tr>
                    <td class="whitespace-nowrap tabular-nums">{{ $cita->fecha?->format('d/m/Y') }} {{ substr((string) $cita->hora, 0, 5) }}</td>
                    <td>{{ $cita->servicio?->nombre ?? '—' }}</td>
                    <td class="hidden sm:table-cell">{{ $cita->empleado?->nombre }} {{ $cita->empleado?->apellido }}</td>
                    <td><x-status-badge :status="$cita->estado" /></td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-8 text-center text-ink-muted">Este cliente aún no tiene citas.</td></tr>
            @endforelse
        </x-table>
    </div>
</div>
@endsection
