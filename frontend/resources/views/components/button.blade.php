{{--
    Componente: <x-button>

    Props:
    - variant : 'primary' (por defecto) | 'secondary' | 'danger' | 'ghost'
    - type    : 'button' | 'submit' | 'reset' (por defecto 'button')
    - href    : si se pasa, se renderiza un <a> (solo para navegación)
    - size    : 'sm' | 'md' (por defecto)

    Uso:
        <x-button type="submit">Guardar</x-button>
        <x-button :href="route('citas.index')" variant="secondary">Cancelar</x-button>

    Los enlaces son solo para navegar. Las acciones que modifican datos
    van en un <form> con type="submit".
--}}
@props([
    'variant' => 'primary',
    'type'    => 'button',
    'href'    => null,
    'size'    => 'md',
])

@php
    $clases = 'btn ' . match ($variant) {
        'secondary' => 'btn-secundario',
        'danger'    => 'btn-peligro',
        'ghost'     => 'btn-fantasma',
        default     => 'btn-primario',
    } . ($size === 'sm' ? ' btn-sm' : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $clases]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $clases]) }}>{{ $slot }}</button>
@endif
