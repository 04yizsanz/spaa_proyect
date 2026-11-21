@extends('layouts.app')

@section('title', 'Nueva factura')

@section('content')
<x-page-header title="Nueva factura" subtitle="Primero se crea la factura; después le agregas los servicios." :back="route('facturas.index')" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('facturas.store') }}" novalidate>
        @csrf
        @include('facturas._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('facturas.index')" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Crear y agregar servicios</x-button>
        </div>
    </form>
</x-card>
@endsection
