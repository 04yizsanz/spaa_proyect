@extends('layouts.app')

@section('title', 'Nueva cita')

@section('content')
<x-page-header title="Agendar cita" subtitle="Elige el servicio, quién te atiende y el horario." :back="route('citas.index')" />

<form method="POST" action="{{ route('citas.store') }}" aria-label="Formulario para agendar una nueva cita" novalidate>
    @csrf
    @include('appointments._form')
</form>
@endsection
