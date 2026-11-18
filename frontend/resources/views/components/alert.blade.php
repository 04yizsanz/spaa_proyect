{{--
    Componente: <x-alert>
    Mensaje destacado. Usa color Y texto, nunca solo color.

    Props: type = 'success' | 'error' | 'warning' | 'info'

    Uso: <x-alert type="warning">El pago ya fue aprobado.</x-alert>
--}}
@props(['type' => 'info'])

@php
    [$clases, $icono] = match ($type) {
        'success' => ['bg-success-soft text-success border-success/30', '✓'],
        'error'   => ['bg-danger-soft text-danger border-danger/30', '✕'],
        'warning' => ['bg-warning-soft text-warning border-warning/30', '!'],
        default   => ['bg-info-soft text-info border-info/30', 'i'],
    };
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-xl border px-4 py-3 text-sm font-medium $clases"]) }} role="{{ $type === 'error' ? 'alert' : 'status' }}">
    <span class="mt-px flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-current/10 text-xs font-bold" aria-hidden="true">{{ $icono }}</span>
    <div>{{ $slot }}</div>
</div>
