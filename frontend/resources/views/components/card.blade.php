{{--
    Componente: <x-card>

    Props:
    - $padding: 'sm' | 'md' (por defecto) | 'lg' | 'none'
    - $class  : clases adicionales opcionales

    Uso:
        <x-card>
            Contenido de la tarjeta
        </x-card>

        <x-card padding="lg">
            <x-slot name="header">
                <h2>Título</h2>
            </x-slot>
            Contenido...
        </x-card>
--}}

@props([
    'padding' => 'md',
])

@php
    $paddingClass = match($padding) {
        'sm'   => 'p-4',
        'lg'   => 'p-8',
        'none' => '',
        default => 'p-6',
    };
@endphp

<div
    class="rounded-2xl {{ $paddingClass }} {{ $attributes->get('class') }}"
    style="background-color: var(--color-warm-white); border: 1px solid var(--color-beige); box-shadow: 0 1px 4px 0 rgba(44,40,37,0.06);"
    {{ $attributes->except('class') }}
>
    @if (isset($header))
        <div
            class="mb-5 pb-4"
            style="border-bottom: 1px solid var(--color-beige);"
        >
            {{ $header }}
        </div>
    @endif

    {{ $slot }}
</div>
