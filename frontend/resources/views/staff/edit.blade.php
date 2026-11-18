@extends('layouts.app')

@section('title', 'Editar empleado')

@section('content')
<x-page-header :title="'Editar ' . $empleado->nombre . ' ' . $empleado->apellido" :back="route('empleados.show', $empleado->empleado_id)" />

<x-card class="max-w-3xl">
    <form method="POST" action="{{ route('empleados.update', $empleado->empleado_id) }}" novalidate>
        @csrf
        @method('PUT')
        @include('staff._form')
        <div class="mt-8 flex justify-end gap-2 border-t border-line pt-5">
            <x-button :href="route('empleados.show', $empleado->empleado_id)" variant="secondary">Cancelar</x-button>
            <x-button type="submit">Guardar cambios</x-button>
        </div>
    </form>
</x-card>
@endsection
