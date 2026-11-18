@extends('layouts.app')

@section('title', 'Personal')

@section('content')
<x-page-header title="Personal" subtitle="Equipo del spa y salón.">
    @if ($puedeGestionar)
        <x-button :href="route('empleados.create')">Nuevo empleado</x-button>
    @endif
</x-page-header>

<x-search-bar :action="route('empleados.index')" placeholder="Buscar por nombre{{ $verContacto ? ', documento o correo' : '' }}">
    <label for="filtro-rol" class="sr-only">Cargo</label>
    <select id="filtro-rol" name="rol" class="campo sm:w-48">
        <option value="">Todos los cargos</option>
        @foreach ($roles as $valor => $texto)
            <option value="{{ $valor }}" @selected(request('rol') === $valor)>{{ $texto }}</option>
        @endforeach
    </select>
</x-search-bar>

@if ($empleados->isEmpty())
    <x-card>
        <x-empty-state title="No hay personal para mostrar"
                       :action="$puedeGestionar ? 'Nuevo empleado' : null" :href="route('empleados.create')" />
    </x-card>
@else
    <x-table label="Lista del personal" :paginator="$empleados">
        <x-slot:head>
            <th scope="col">Nombre</th>
            <th scope="col">Cargo</th>
            @if ($verContacto)
                <th scope="col" class="hidden md:table-cell">Correo</th>
                <th scope="col" class="hidden lg:table-cell">Teléfono</th>
            @endif
            <th scope="col">Disponibilidad</th>
            <th scope="col"><span class="sr-only">Acciones</span></th>
        </x-slot:head>
        @foreach ($empleados as $empleado)
            <tr>
                <td><a href="{{ route('empleados.show', $empleado->empleado_id) }}" class="font-medium text-ink hover:text-primary">{{ $empleado->nombre }} {{ $empleado->apellido }}</a></td>
                <td>{{ $roles[$empleado->rol] ?? ucfirst($empleado->rol) }}</td>
                @if ($verContacto)
                    <td class="hidden md:table-cell">{{ $empleado->correo ?? '—' }}</td>
                    <td class="hidden lg:table-cell">{{ $empleado->telefono ?? '—' }}</td>
                @endif
                <td><x-status-badge :status="$empleado->disponibilidad ? 'disponible' : 'no disponible'" /></td>
                <td class="text-right"><a href="{{ route('empleados.show', $empleado->empleado_id) }}" class="enlace text-sm">Ver</a></td>
            </tr>
        @endforeach
    </x-table>
@endif
@endsection
