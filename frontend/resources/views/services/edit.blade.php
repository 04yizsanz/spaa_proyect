@extends('layouts.app')

@section('title', 'Editar servicio')

@section('content')
<x-page-header :title="'Editar ' . $servicio->nombre" :back="route('servicios.show', $servicio->servicio_id)" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('servicios.update', $servicio->servicio_id) }}" novalidate>
        @csrf
        @method('PUT')
        @include('services._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('servicios.show', $servicio->servicio_id)" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar cambios</x-button>
        </div>
    </form>
</x-card>
@endsection
