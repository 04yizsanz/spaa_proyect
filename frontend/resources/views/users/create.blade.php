@extends('layouts.app')

@section('title', 'Nueva cuenta')

@section('content')
<x-page-header title="Nueva cuenta" subtitle="Crea el acceso de una persona del equipo." :back="route('usuarios.index')" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('usuarios.store') }}" novalidate>
        @csrf
        @include('users._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('usuarios.index')" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Crear cuenta</x-button>
        </div>
    </form>
</x-card>
@endsection
