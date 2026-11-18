@extends('layouts.app')

@section('title', 'Nuevo proveedor')

@section('content')
<x-page-header title="Nuevo proveedor" :back="route('proveedores.index')" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('proveedores.store') }}" novalidate>
        @csrf
        @include('proveedores._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('proveedores.index')" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar proveedor</x-button>
        </div>
    </form>
</x-card>
@endsection
