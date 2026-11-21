@extends('layouts.app')

@section('title', 'Editar factura')

@section('content')
<x-page-header :title="'Editar factura N.º ' . str_pad($factura->factura_id, 5, '0', STR_PAD_LEFT)" :back="route('facturas.show', $factura->factura_id)" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('facturas.update', $factura->factura_id) }}" novalidate>
        @csrf
        @method('PUT')
        @include('facturas._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('facturas.show', $factura->factura_id)" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar cambios</x-button>
        </div>
    </form>
</x-card>
@endsection
