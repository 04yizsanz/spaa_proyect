@extends('layouts.app')

@section('title', 'Editar cita')

@section('content')
<x-page-header :title="'Editar cita #' . $cita->codigo_cita" :back="route('citas.index')">
    <x-status-badge :status="$cita->estado" />
</x-page-header>

<form method="POST" action="{{ route('citas.update', $cita->citas_id) }}" aria-label="Formulario para editar la cita" novalidate>
    @csrf
    @method('PUT')
    @include('appointments._form')
</form>
@endsection
