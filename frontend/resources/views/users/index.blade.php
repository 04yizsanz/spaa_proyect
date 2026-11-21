@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<x-page-header title="Usuarios y roles" subtitle="Cuentas que pueden iniciar sesión y lo que cada una puede hacer.">
    <x-button :href="route('usuarios.create')">Nueva cuenta</x-button>
</x-page-header>

<x-alert type="info" class="mb-5">
    El registro público siempre crea cuentas de <strong>Cliente</strong>. Aquí creas las cuentas del personal o cambias el rol de una cuenta existente.
</x-alert>

<x-search-bar :action="route('usuarios.index')" placeholder="Buscar por nombre, documento o correo">
    <label for="filtro-rol" class="sr-only">Rol</label>
    <select id="filtro-rol" name="rol" class="campo sm:w-48">
        <option value="">Todos los roles</option>
        @foreach ($roles as $id => $nombre)
            <option value="{{ $id }}" @selected(request('rol') == $id)>{{ $nombre }}</option>
        @endforeach
    </select>
    <label for="filtro-estado" class="sr-only">Estado</label>
    <select id="filtro-estado" name="estado" class="campo sm:w-40">
        <option value="">Activas e inactivas</option>
        <option value="1" @selected(request('estado') === '1')>Activas</option>
        <option value="0" @selected(request('estado') === '0')>Inactivas</option>
    </select>
</x-search-bar>

@if ($usuarios->isEmpty())
    <x-card><x-empty-state title="No hay cuentas para mostrar" /></x-card>
@else
    <x-table label="Lista de usuarios" :paginator="$usuarios">
        <x-slot:head>
            <th scope="col">Nombre</th>
            <th scope="col" class="hidden md:table-cell">Correo</th>
            <th scope="col">Rol</th>
            <th scope="col">Estado</th>
            <th scope="col"><span class="sr-only">Acciones</span></th>
        </x-slot:head>
        @foreach ($usuarios as $usuario)
            <tr>
                <td>
                    <span class="font-medium text-ink">{{ $usuario->nombre }} {{ $usuario->apellido }}</span>
                    @if ($usuario->usuario_id === auth()->id()) <span class="ml-1 text-xs text-ink-muted">(tú)</span> @endif
                    <span class="block text-xs text-ink-muted md:hidden">{{ $usuario->email }}</span>
                </td>
                <td class="hidden md:table-cell">{{ $usuario->email }}</td>
                <td>
                    <span @class([
                        'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                        'bg-primary-soft text-primary' => $usuario->rol?->nombre === 'Administrador',
                        'bg-accent-soft text-accent-ink' => ! in_array($usuario->rol?->nombre, ['Administrador', 'Cliente']),
                        'bg-subtle text-ink-soft' => $usuario->rol?->nombre === 'Cliente',
                    ])>{{ $usuario->rol?->nombre ?? 'Sin rol' }}</span>
                </td>
                <td><x-status-badge :status="(bool) $usuario->estado" /></td>
                <td class="text-right"><a href="{{ route('usuarios.edit', $usuario->usuario_id) }}" class="enlace text-sm">Editar</a></td>
            </tr>
        @endforeach
    </x-table>
@endif
@endsection
