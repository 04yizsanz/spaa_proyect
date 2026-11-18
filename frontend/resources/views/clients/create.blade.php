@extends('layouts.app')

@section('title', 'Nuevo cliente')

@section('content')
<x-page-header title="Nuevo cliente" subtitle="Crea la cuenta del cliente desde el mostrador." :back="route('clientes.index')" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('clientes.store') }}" novalidate>
        @csrf
        @include('clients._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('clientes.index')" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Registrar cliente</x-button>
        </div>
    </form>
</x-card>
@endsection
