@extends('layouts.app')

@section('title', 'Editar cuenta')

@section('content')
<x-page-header :title="'Cuenta de ' . $usuario->nombre . ' ' . $usuario->apellido" :back="route('usuarios.index')" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('usuarios.update', $usuario->usuario_id) }}" novalidate>
        @csrf
        @method('PUT')
        @include('users._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('usuarios.index')" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar cambios</x-button>
        </div>
    </form>
</x-card>
@endsection
