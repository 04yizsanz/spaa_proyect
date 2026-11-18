@extends('layouts.app')

@section('title', 'Editar pago')

@section('content')
<x-page-header :title="'Editar pago #' . $pago->pago_id" :back="route('pagos.show', $pago->pago_id)" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('pagos.update', $pago->pago_id) }}" novalidate>
        @csrf
        @method('PUT')
        @include('pagos._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('pagos.show', $pago->pago_id)" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar cambios</x-button>
        </div>
    </form>
</x-card>
@endsection
