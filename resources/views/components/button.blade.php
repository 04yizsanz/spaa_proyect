{{--
    Componente: <x-button>
    
    Props:
    - $variant : 'primary' (por defecto) | 'secondary' | 'danger'
    - $type    : 'button' | 'submit' | 'reset'  (por defecto 'button')
    - $href    : si se pasa, renderiza un <a> en vez de <button>
    - $disabled: boolean, desactiva el botón
    - $size    : 'sm' | 'md' (por defecto) | 'lg'

    Uso:
        <x-button type="submit" variant="primary">Guardar cita</x-button>
        <x-button href="{{ route('citas.index') }}" variant="secondary">Cancelar</x-button>
        <x-button variant="danger" type="button">Eliminar</x-button>

    IMPORTANTE: no uses <x-button href="..."> para acciones que modifican datos.
    Los enlaces son solo para navegación. Usa type="submit" en formularios.
--}}

@props([
    'variant'  => 'primary',
    'type'     => 'button',
    'href'     => null,
    'disabled' => false,
    'size'     => 'md',
])

@php
    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
        default => 'px-4 py-2 text-sm',
    };

    $baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-colors duration-150 focus-visible:outline-none cursor-pointer ' . $sizeClasses;

    $variantStyles = match($variant) {
        'secondary' => 'background-color: transparent; color: var(--color-charcoal-light); border: 1px solid var(--color-beige-mid);',
        'danger'    => 'background-color: var(--color-red-light); color: var(--color-red-soft); border: 1px solid var(--color-red-soft);',
        default     => 'background-color: var(--color-terracotta); color: white; border: 1px solid var(--color-terracotta);',
    };

    $hoverClass = match($variant) {
        'secondary' => 'hover:bg-gray-50',
        'danger'    => '',
        default     => '',
    };

    $disabledStyle = $disabled ? 'opacity: 0.5; cursor: not-allowed; pointer-events: none;' : '';
@endphp

@if ($href)
    {{-- Enlace de navegación --}}
    <a
        href="{{ $href }}"
        class="{{ $baseClasses }} {{ $hoverClass }}"
        style="{{ $variantStyles }} {{ $disabledStyle }}"
        {{ $disabled ? 'aria-disabled=true tabindex=-1' : '' }}
        {{ $attributes }}
    >
        {{ $slot }}
    </a>
@else
    {{-- Botón de acción --}}
    <button
        type="{{ $type }}"
        class="{{ $baseClasses }} {{ $hoverClass }}"
        style="{{ $variantStyles }} {{ $disabledStyle }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes }}
    >
        {{ $slot }}
    </button>
@endif
