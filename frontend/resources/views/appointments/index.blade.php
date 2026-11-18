@extends('layouts.app')

@section('title', 'Citas')

@section('content')
@php $esCliente = auth()->user()->esCliente(); @endphp

<x-page-header :title="$esCliente ? 'Mis citas' : 'Agenda de citas'"
               :subtitle="$esCliente ? 'Consulta, agenda o cancela tus citas.' : 'Consulta y filtra las citas del spa.'">
    @can('create', App\Models\Cita::class)
        <x-button :href="route('citas.create')">Agendar cita</x-button>
    @endcan
</x-page-header>

{{-- Filtros por GET: la URL refleja la búsqueda y se puede compartir --}}
<x-search-bar :action="route('citas.index')" :placeholder="null">
    <label for="filtro-fecha" class="sr-only">Fecha</label>
    <input id="filtro-fecha" type="date" name="fecha" value="{{ $filtroFecha }}" class="campo sm:w-48">
    <label for="filtro-estado" class="sr-only">Estado</label>
    <select id="filtro-estado" name="estado" class="campo sm:w-48">
        <option value="">Todos los estados</option>
        @foreach ($estados as $valor => $texto)
            <option value="{{ $valor }}" @selected($filtroEstado === $valor)>{{ $texto }}</option>
        @endforeach
    </select>
</x-search-bar>

@if ($citas->isEmpty())
    <x-card>
        <x-empty-state
            title="No hay citas para mostrar"
            :message="($filtroFecha || $filtroEstado) ? 'Prueba con otra fecha o estado.' : 'Cuando se agenden citas, aparecerán aquí.'"
            :action="auth()->user()->can('create', App\Models\Cita::class) ? 'Agendar cita' : null"
            :href="route('citas.create')" />
    </x-card>
@else
    <x-table label="Lista de citas" :paginator="$citas">
        <x-slot:head>
            <th scope="col">Fecha y hora</th>
            @unless ($esCliente) <th scope="col">Cliente</th> @endunless
            <th scope="col" class="hidden sm:table-cell">Servicio</th>
            <th scope="col" class="hidden md:table-cell">Atiende</th>
            <th scope="col">Estado</th>
            <th scope="col"><span class="sr-only">Acciones</span></th>
        </x-slot:head>
        @foreach ($citas as $cita)
            <tr>
                <td class="whitespace-nowrap">
                    <span class="font-medium tabular-nums text-ink">{{ $cita->fecha?->format('d/m/Y') }}</span>
                    <span class="block text-xs tabular-nums text-ink-muted">{{ substr((string) $cita->hora, 0, 5) }} · #{{ $cita->codigo_cita }}</span>
                </td>
                @unless ($esCliente)
                    <td>{{ $cita->cliente?->usuario?->nombre }} {{ $cita->cliente?->usuario?->apellido }}</td>
                @endunless
                <td class="hidden sm:table-cell">{{ $cita->servicio?->nombre ?? '—' }}</td>
                <td class="hidden md:table-cell">{{ $cita->empleado ? $cita->empleado->nombre . ' ' . $cita->empleado->apellido : '—' }}</td>
                <td><x-status-badge :status="$cita->estado" /></td>
                <td class="text-right whitespace-nowrap">
                    @can('update', $cita)
                        <x-button :href="route('citas.edit', $cita->citas_id)" variant="secondary" size="sm">Editar</x-button>
                        @if ($esCliente && $cita->estado === 'pendiente')
                            <form method="POST" action="{{ route('citas.cancelar', $cita->citas_id) }}" class="inline"
                                  onsubmit="return confirm('¿Cancelar esta cita?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-fantasma text-danger">Cancelar</button>
                            </form>
                        @endif
                    @endcan
                </td>
            </tr>
        @endforeach
    </x-table>
@endif
@endsection
