@extends('layouts.app')

@section('title', 'Pago #' . $pago->pago_id)

@section('content')
@php
    $pendiente     = $pago->estado === 'pendiente';
    $puedeEditar   = $pendiente && Gate::allows('update', $pago);
    $puedeAprobar  = $pendiente && Gate::allows('aprobar', $pago);
@endphp

<x-page-header :title="'Pago #' . $pago->pago_id" :back="route('pagos.index')">
    @if ($puedeEditar)
        <x-button :href="route('pagos.edit', $pago->pago_id)" variant="secondary">Editar</x-button>
    @endif
</x-page-header>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <x-card class="lg:col-span-2">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <p class="text-sm text-ink-muted">Monto</p>
                <p class="text-4xl font-semibold"><x-money :value="$pago->monto" /></p>
            </div>
            <x-status-badge :status="$pago->estado" class="text-sm" />
        </div>
        <dl class="divide-y divide-line border-t border-line">
            <x-detail label="Método">{{ $metodos[$pago->metodo] ?? $pago->metodo }}</x-detail>
            <x-detail label="Fecha y hora">{{ $pago->fecha_hora?->format('d/m/Y H:i') }}</x-detail>
            <x-detail label="Cita">
                #{{ $pago->codigo_cita }}
                @if ($pago->cita)
                    · {{ $pago->cita->servicio?->nombre }} · {{ $pago->cita->fecha?->format('d/m/Y') }} {{ substr((string) $pago->cita->hora, 0, 5) }}
                @endif
            </x-detail>
            <x-detail label="Cliente">{{ $pago->cita?->cliente?->usuario?->nombre }} {{ $pago->cita?->cliente?->usuario?->apellido }}</x-detail>
        </dl>
    </x-card>

    @if ($puedeAprobar)
        <x-card>
            <x-slot:header>
                <h2 class="text-lg">Revisar pago</h2>
                <p class="mt-1 text-sm text-ink-soft">La decisión es definitiva: un pago aprobado o rechazado ya no se puede modificar.</p>
            </x-slot:header>
            <div class="flex flex-col gap-2">
                <form method="POST" action="{{ route('pagos.estado', $pago->pago_id) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="estado" value="aprobado">
                    <x-button type="submit" class="w-full">Aprobar pago</x-button>
                </form>
                <form method="POST" action="{{ route('pagos.estado', $pago->pago_id) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="estado" value="rechazado">
                    <x-button type="submit" variant="danger" class="w-full">Rechazar pago</x-button>
                </form>
            </div>
        </x-card>
    @elseif (! $pendiente)
        <x-alert type="info" class="h-fit">Este pago ya fue {{ $pago->estado }} y no se puede modificar.</x-alert>
    @endif
</div>
@endsection
