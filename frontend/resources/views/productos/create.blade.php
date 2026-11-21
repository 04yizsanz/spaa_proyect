@extends('layouts.app')

@section('title', 'Nuevo producto')

@section('content')
<x-page-header title="Nuevo producto" :back="route('productos.index')" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('productos.store') }}" novalidate>
        @csrf
        @include('productos._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('productos.index')" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar producto</x-button>
        </div>
    </form>
</x-card>
@endsection
