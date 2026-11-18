@extends('layouts.app')

@section('title', 'Facturas')

@section('content')
<x-page-header title="Facturas" :subtitle="auth()->user()->esCliente() ? 'Tus facturas.' : 'Facturas emitidas a los clientes.'">
    @can('create', App\Models\Factura::class)
        <x-button :href="route('facturas.create')">Nueva factura</x-button>
    @endcan
</x-page-header>

@if (! empty($clientes))
    <x-search-bar :action="route('facturas.index')" :placeholder="null">
        <label for="filtro-cliente" class="sr-only">Cliente</label>
        <select id="filtro-cliente" name="cliente" class="campo sm:w-72">
            <option value="">Todos los clientes</option>
            @foreach ($clientes as $id => $nombre)
                <option value="{{ $id }}" @selected(request('cliente') == $id)>{{ $nombre }}</option>
            @endforeach
        </select>
    </x-search-bar>
@endif

@if ($facturas->isEmpty())
    <x-card><x-empty-state title="No hay facturas para mostrar" message="Las facturas emitidas aparecerán aquí." /></x-card>
@else
    <x-table label="Lista de facturas" :paginator="$facturas">
        <x-slot:head>
            <th scope="col">Factura</th>
            <th scope="col">Fecha</th>
            <th scope="col" class="hidden md:table-cell">Cliente</th>
            <th scope="col" class="hidden text-right sm:table-cell">Subtotal</th>
            <th scope="col" class="text-right">Total</th>
            <th scope="col"><span class="sr-only">Acciones</span></th>
        </x-slot:head>
        @foreach ($facturas as $factura)
            <tr>
                <td class="font-medium text-ink">N.º {{ str_pad($factura->factura_id, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="whitespace-nowrap tabular-nums">{{ $factura->fecha_hora?->format('d/m/Y') }}</td>
                <td class="hidden md:table-cell">{{ $factura->cliente?->usuario?->nombre }} {{ $factura->cliente?->usuario?->apellido }}</td>
                <td class="hidden text-right sm:table-cell"><x-money :value="$factura->subtotal" /></td>
                <td class="text-right font-medium text-ink"><x-money :value="$factura->total" /></td>
                <td class="text-right"><a href="{{ route('facturas.show', $factura->factura_id) }}" class="enlace text-sm">Ver</a></td>
            </tr>
        @endforeach
    </x-table>
@endif
@endsection
