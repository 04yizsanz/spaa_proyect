@extends('layouts.app')

@section('title', $empleado->nombre . ' ' . $empleado->apellido)

@section('content')
<x-page-header :title="$empleado->nombre . ' ' . $empleado->apellido" :subtitle="$roles[$empleado->rol] ?? ucfirst($empleado->rol)" :back="route('empleados.index')">
    @if ($puedeGestionar)
        <x-button :href="route('empleados.edit', $empleado->empleado_id)" variant="secondary">Editar</x-button>
        <x-delete-button :action="route('empleados.destroy', $empleado->empleado_id)" :name="$empleado->nombre . ' ' . $empleado->apellido" />
    @endif
</x-page-header>

<x-card class="max-w-3xl">
    <dl class="divide-y divide-line">
        <x-detail label="Cargo">{{ $roles[$empleado->rol] ?? ucfirst($empleado->rol) }}</x-detail>
        <x-detail label="Disponibilidad"><x-status-badge :status="$empleado->disponibilidad ? 'disponible' : 'no disponible'" /></x-detail>
        @if ($verContacto)
            <x-detail label="Documento">{{ $empleado->documento }}</x-detail>
            <x-detail label="Correo">{{ $empleado->correo ?? '—' }}</x-detail>
            <x-detail label="Teléfono">{{ $empleado->telefono ?? '—' }}</x-detail>
            <x-detail label="Contratación">{{ $empleado->fecha_contratacion?->format('d/m/Y') }}</x-detail>
        @endif
        @if ($verSalario)
            <x-detail label="Salario"><x-money :value="$empleado->salario" /></x-detail>
        @endif
    </dl>
</x-card>
@endsection
