@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
<x-page-header :title="'Editar ' . $producto->nombre" :back="route('productos.show', $producto->producto_id)" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('productos.update', $producto->producto_id) }}" novalidate>
        @csrf
        @method('PUT')
        @include('productos._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('productos.show', $producto->producto_id)" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar cambios</x-button>
        </div>
    </form>
</x-card>
@endsection
