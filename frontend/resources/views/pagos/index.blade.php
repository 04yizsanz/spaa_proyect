@extends('layouts.app')

@section('title', 'Pagos')

@section('content')
<x-page-header title="Pagos" :subtitle="auth()->user()->esCliente() ? 'Pagos de tus citas.' : 'Pagos recibidos por las citas.'">
    @can('create', App\Models\Pago::class)
        <x-button :href="route('pagos.create')">Registrar pago</x-button>
    @endcan
</x-page-header>

<x-search-bar :action="route('pagos.index')" :placeholder="null">
    <label for="filtro-estado" class="sr-only">Estado</label>
    <select id="filtro-estado" name="estado" class="campo sm:w-48">
        <option value="">Todos los estados</option>
        @foreach (['pendiente' => 'Pendientes', 'aprobado' => 'Aprobados', 'rechazado' => 'Rechazados'] as $valor => $texto)
            <option value="{{ $valor }}" @selected(request('estado') === $valor)>{{ $texto }}</option>
        @endforeach
    </select>
    <label for="filtro-metodo" class="sr-only">Método</label>
    <select id="filtro-metodo" name="metodo" class="campo sm:w-48">
        <option value="">Todos los métodos</option>
        @foreach ($metodos as $valor => $texto)
            <option value="{{ $valor }}" @selected(request('metodo') === $valor)>{{ $texto }}</option>
        @endforeach
    </select>
</x-search-bar>

@if ($pagos->isEmpty())
    <x-card><x-empty-state title="No hay pagos para mostrar" message="Los pagos de las citas aparecerán aquí." /></x-card>
@else
    <x-table label="Lista de pagos" :paginator="$pagos">
        <x-slot:head>
            <th scope="col">Fecha</th>
            <th scope="col">Cita</th>
            <th scope="col" class="hidden md:table-cell">Cliente</th>
            <th scope="col" class="hidden sm:table-cell">Método</th>
            <th scope="col" class="text-right">Monto</th>
            <th scope="col">Estado</th>
            <th scope="col"><span class="sr-only">Acciones</span></th>
        </x-slot:head>
        @foreach ($pagos as $pago)
            <tr>
                <td class="whitespace-nowrap tabular-nums">{{ $pago->fecha_hora?->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="font-medium text-ink">#{{ $pago->codigo_cita }}</span>
                    <span class="block text-xs text-ink-muted">{{ $pago->cita?->servicio?->nombre }}</span>
                </td>
                <td class="hidden md:table-cell">{{ $pago->cita?->cliente?->usuario?->nombre }} {{ $pago->cita?->cliente?->usuario?->apellido }}</td>
                <td class="hidden sm:table-cell">{{ $metodos[$pago->metodo] ?? $pago->metodo }}</td>
                <td class="text-right font-medium text-ink"><x-money :value="$pago->monto" /></td>
                <td><x-status-badge :status="$pago->estado" /></td>
                <td class="text-right"><a href="{{ route('pagos.show', $pago->pago_id) }}" class="enlace text-sm">Ver</a></td>
            </tr>
        @endforeach
    </x-table>
@endif
@endsection
