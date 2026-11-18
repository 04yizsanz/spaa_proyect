@extends('layouts.app')

@section('title', 'Servicios')

@section('content')
<x-page-header title="Servicios" subtitle="Catálogo de servicios del spa y salón.">
    @if ($puedeGestionar)
        <x-button :href="route('servicios.create')">Nuevo servicio</x-button>
    @endif
</x-page-header>

<x-search-bar :action="route('servicios.index')" placeholder="Buscar servicio">
    @if ($puedeGestionar)
        <label for="filtro-activo" class="sr-only">Estado</label>
        <select id="filtro-activo" name="activo" class="campo sm:w-44">
            <option value="">Todos</option>
            <option value="1" @selected(request('activo') === '1')>Activos</option>
            <option value="0" @selected(request('activo') === '0')>Inactivos</option>
        </select>
    @endif
</x-search-bar>

@if ($servicios->isEmpty())
    <x-card>
        <x-empty-state title="No hay servicios para mostrar"
                       message="Agrega los servicios que ofrece el spa."
                       :action="$puedeGestionar ? 'Nuevo servicio' : null" :href="route('servicios.create')" />
    </x-card>
@else
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($servicios as $servicio)
            <a href="{{ route('servicios.show', $servicio->servicio_id) }}"
               class="group flex flex-col rounded-2xl border border-line bg-surface p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-primary hover:shadow-md">
                <div class="mb-3 flex items-start justify-between gap-3">
                    <h2 class="font-sans text-base font-semibold text-ink group-hover:text-primary">{{ $servicio->nombre }}</h2>
                    @unless ($servicio->activo) <x-status-badge :status="false" /> @endunless
                </div>
                <p class="mb-4 line-clamp-2 flex-1 text-sm text-ink-soft">{{ $servicio->descripcion ?: 'Sin descripción.' }}</p>
                <div class="flex items-end justify-between border-t border-line pt-3">
                    <span class="text-lg font-semibold text-ink"><x-money :value="$servicio->precio" /></span>
                    <span class="text-xs text-ink-muted">{{ $servicio->duracion_min }} min</span>
                </div>
            </a>
        @endforeach
    </div>
    @if ($servicios->hasPages())
        <div class="mt-6">{{ $servicios->links() }}</div>
    @endif
@endif
@endsection
