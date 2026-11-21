{{--
    Componente: <x-card>

    Props:
    - padding: 'none' | 'sm' | 'md' (por defecto) | 'lg'
    Slot opcional "header" para un título con línea divisoria.

    Uso:
        <x-card>Contenido</x-card>
        <x-card padding="none"> <table class="tabla">…</table> </x-card>
--}}
@props(['padding' => 'md'])

@php
    $relleno = match ($padding) {
        'none' => '',
        'sm'   => 'p-4',
        'lg'   => 'p-8',
        default => 'p-6',
    };
@endphp

<div {{ $attributes->merge(['class' => "rounded-2xl border border-line bg-surface shadow-sm $relleno"]) }}>
    @isset($header)
        <div class="mb-5 border-b border-line pb-4">{{ $header }}</div>
    @endisset
    {{ $slot }}
</div>
