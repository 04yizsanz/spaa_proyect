@extends('layouts.app')

@section('title', 'Registrar movimiento')

@section('content')
<x-page-header title="Registrar movimiento" subtitle="Entrada (compra, devolución) o salida (uso, venta, pérdida)." :back="route('movimientos.index')" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('movimientos.store') }}" novalidate>
        @csrf
        @include('movimientos._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('movimientos.index')" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar movimiento</x-button>
        </div>
    </form>
</x-card>
@endsection
