@extends('layouts.app')

@section('title', 'Editar proveedor')

@section('content')
<x-page-header :title="'Editar ' . $proveedor->nombre" :back="route('proveedores.show', $proveedor->proveedor_id)" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('proveedores.update', $proveedor->proveedor_id) }}" novalidate>
        @csrf
        @method('PUT')
        @include('proveedores._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('proveedores.show', $proveedor->proveedor_id)" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar cambios</x-button>
        </div>
    </form>
</x-card>
@endsection
