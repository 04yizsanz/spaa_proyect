@extends('layouts.app')

@section('title', 'Nuevo empleado')

@section('content')
<x-page-header title="Nuevo empleado" :back="route('empleados.index')" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('empleados.store') }}" novalidate>
        @csrf
        @include('staff._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('empleados.index')" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar empleado</x-button>
        </div>
    </form>
</x-card>
@endsection
