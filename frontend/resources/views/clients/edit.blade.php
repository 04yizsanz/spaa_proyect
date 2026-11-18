@extends('layouts.app')

@section('title', 'Editar cliente')

@section('content')
<x-page-header :title="'Editar ' . $usuario->nombre . ' ' . $usuario->apellido" :back="route('clientes.show', $cliente->cliente_id)" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('clientes.update', $cliente->cliente_id) }}" novalidate>
        @csrf
        @method('PUT')
        @include('clients._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('clientes.show', $cliente->cliente_id)" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar cambios</x-button>
        </div>
    </form>
</x-card>
@endsection
