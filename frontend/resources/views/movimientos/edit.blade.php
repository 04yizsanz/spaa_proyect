@extends('layouts.app')

@section('title', 'Editar movimiento')

@section('content')
<x-page-header title="Editar movimiento" :back="route('movimientos.show', $movimiento->movimiento_id)" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('movimientos.update', $movimiento->movimiento_id) }}" novalidate>
        @csrf
        @method('PUT')
        @include('movimientos._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('movimientos.show', $movimiento->movimiento_id)" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar cambios</x-button>
        </div>
    </form>
</x-card>
@endsection
