@extends('layouts.app')

@section('title', 'Nuevo servicio')

@section('content')
<x-page-header title="Nuevo servicio" :back="route('servicios.index')" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('servicios.store') }}" novalidate>
        @csrf
        @include('services._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('servicios.index')" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar servicio</x-button>
        </div>
    </form>
</x-card>
@endsection
