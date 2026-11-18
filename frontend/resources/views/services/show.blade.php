@extends('layouts.app')

@section('title', $servicio->nombre)

@section('content')
<x-page-header :title="$servicio->nombre" subtitle="Servicio" :back="route('servicios.index')">
    @if ($servicio->activo)
        @can('create', App\Models\Cita::class)
            <x-button :href="route('citas.create')">Agendar este servicio</x-button>
        @endcan
    @endif
    @can('update', $servicio)
        <x-button :href="route('servicios.edit', $servicio->servicio_id)" variant="secondary">Editar</x-button>
    @endcan
    @can('delete', $servicio)
        <x-delete-button :action="route('servicios.destroy', $servicio->servicio_id)" :name="$servicio->nombre" />
    @endcan
</x-page-header>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <x-card class="lg:col-span-2">
        <h2 class="mb-2 text-lg">Descripción</h2>
        <p class="whitespace-pre-line text-ink-soft">{{ $servicio->descripcion ?: 'Este servicio aún no tiene descripción.' }}</p>
    </x-card>
    <x-card>
        <dl class="divide-y divide-line">
            <x-detail label="Precio"><span class="text-lg font-semibold"><x-money :value="$servicio->precio" /></span></x-detail>
            <x-detail label="Duración">{{ $servicio->duracion_min }} minutos</x-detail>
            <x-detail label="Estado"><x-status-badge :status="$servicio->activo" /></x-detail>
        </dl>
    </x-card>
</div>
@endsection
