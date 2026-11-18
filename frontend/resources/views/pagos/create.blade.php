@extends('layouts.app')

@section('title', 'Registrar pago')

@section('content')
<x-page-header title="Registrar pago" :back="route('pagos.index')" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('pagos.store') }}" novalidate>
        @csrf
        @include('pagos._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('pagos.index')" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Registrar pago</x-button>
        </div>
    </form>
</x-card>
@endsection
