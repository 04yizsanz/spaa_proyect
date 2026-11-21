@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
@php
    $hora   = (int) now()->format('H');
    $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
@endphp

<div class="mb-8">
    <p class="text-sm text-ink-muted">{{ ucfirst(now()->locale('es')->isoFormat('dddd D [de] MMMM')) }}</p>
    <h1 class="text-3xl">{{ $saludo }}, {{ auth()->user()->nombre }}</h1>
</div>

@if ($esCliente)
    {{-- ===== INICIO DEL CLIENTE ===== --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <x-slot:header>
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg">Tus próximas citas</h2>
                    <x-button :href="route('citas.create')" size="sm">Agendar cita</x-button>
                </div>
            </x-slot:header>
            @forelse ($proximas as $cita)
                <div class="flex items-center gap-4 border-b border-line py-3 last:border-b-0">
                    <div class="w-14 shrink-0 rounded-xl bg-primary-soft py-2 text-center text-primary">
                        <p class="text-xs font-semibold uppercase">{{ $cita->fecha->locale('es')->isoFormat('MMM') }}</p>
                        <p class="text-xl font-bold leading-none">{{ $cita->fecha->format('d') }}</p>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-ink">{{ $cita->servicio?->nombre }}</p>
                        <p class="text-sm text-ink-muted">{{ substr((string) $cita->hora, 0, 5) }} · con {{ $cita->empleado?->nombre }}</p>
                    </div>
                    <x-status-badge :status="$cita->estado" />
                </div>
            @empty
                <x-empty-state title="No tienes citas próximas" message="Reserva tu próximo momento de bienestar." action="Agendar cita" :href="route('citas.create')" />
            @endforelse
        </x-card>

        <div class="flex flex-col gap-4">
            <h2 class="text-lg">Te puede interesar</h2>
            @foreach ($servicios as $servicio)
                <a href="{{ route('servicios.show', $servicio->servicio_id) }}" class="rounded-2xl border border-line bg-surface p-4 transition hover:border-primary">
                    <p class="font-medium text-ink">{{ $servicio->nombre }}</p>
                    <p class="text-sm text-ink-muted"><x-money :value="$servicio->precio" /> · {{ $servicio->duracion_min }} min</p>
                </a>
            @endforeach
            <x-button :href="route('servicios.index')" variant="secondary">Ver todos los servicios</x-button>
        </div>
    </div>
@else
    {{-- ===== INICIO DEL PERSONAL ===== --}}
    <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($indicadores as $indicador)
            <a href="{{ $indicador['ruta'] }}" class="rounded-2xl border border-line bg-surface p-5 shadow-sm transition hover:border-primary">
                <p class="text-sm text-ink-muted">{{ $indicador['etiqueta'] }}</p>
                <p class="mt-1 text-3xl font-semibold tabular-nums text-ink">{{ $indicador['valor'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="mb-3 flex items-center justify-between gap-4">
        <h2 class="text-lg">Agenda de hoy</h2>
        @can('create', App\Models\Cita::class)
            <x-button :href="route('citas.create')" size="sm">Agendar cita</x-button>
        @endcan
    </div>
    <x-table label="Citas de hoy">
        <x-slot:head>
            <th scope="col">Hora</th>
            <th scope="col">Cliente</th>
            <th scope="col" class="hidden sm:table-cell">Servicio</th>
            <th scope="col" class="hidden md:table-cell">Atiende</th>
            <th scope="col">Estado</th>
        </x-slot:head>
        @forelse ($citasHoy as $cita)
            <tr>
                <td class="font-medium tabular-nums text-ink">{{ substr((string) $cita->hora, 0, 5) }}</td>
                <td>{{ $cita->cliente?->usuario?->nombre }} {{ $cita->cliente?->usuario?->apellido }}</td>
                <td class="hidden sm:table-cell">{{ $cita->servicio?->nombre }}</td>
                <td class="hidden md:table-cell">{{ $cita->empleado?->nombre }}</td>
                <td><x-status-badge :status="$cita->estado" /></td>
            </tr>
        @empty
            <tr><td colspan="5" class="py-10 text-center text-ink-muted">No hay citas agendadas para hoy.</td></tr>
        @endforelse
    </x-table>
@endif
@endsection
