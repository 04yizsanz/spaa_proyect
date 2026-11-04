{{--
    Componente: <x-status-badge>

    Muestra el estado de una cita de forma visual.
    Usa color Y texto para no depender solo del color (accesibilidad WCAG).

    Props:
    - $status: 'pendiente' | 'confirmada' | 'completada' | 'cancelada'
               También acepta 'activo'/'inactivo' para servicios.

    Uso:
        <x-status-badge :status="$cita->estado" />
        <x-status-badge status="pendiente" />
--}}

@props([
    'status' => 'pendiente',
])

@php
    $config = match($status) {
        'confirmada'  => [
            'label' => 'Confirmada',
            'bg'    => 'var(--color-sage-light)',
            'color' => 'var(--color-sage-dark)',
            'border'=> 'var(--color-sage)',
            'icon'  => '●',
        ],
        'completada'  => [
            'label' => 'Completada',
            'bg'    => '#EEF2FF',
            'color' => '#4338CA',
            'border'=> '#A5B4FC',
            'icon'  => '✓',
        ],
        'cancelada'   => [
            'label' => 'Cancelada',
            'bg'    => 'var(--color-red-light)',
            'color' => 'var(--color-red-soft)',
            'border'=> 'var(--color-red-soft)',
            'icon'  => '✕',
        ],
        'activo', 'true', '1' => [
            'label' => 'Activo',
            'bg'    => 'var(--color-sage-light)',
            'color' => 'var(--color-sage-dark)',
            'border'=> 'var(--color-sage)',
            'icon'  => '●',
        ],
        'inactivo', 'false', '0' => [
            'label' => 'Inactivo',
            'bg'    => 'var(--color-beige)',
            'color' => 'var(--color-charcoal-muted)',
            'border'=> 'var(--color-beige-mid)',
            'icon'  => '○',
        ],
        default => [  /* pendiente */
            'label' => 'Pendiente',
            'bg'    => 'var(--color-amber-light)',
            'color' => '#92650A',
            'border'=> 'var(--color-amber-soft)',
            'icon'  => '◐',
        ],
    };
@endphp

<span
    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
    style="
        background-color: {{ $config['bg'] }};
        color: {{ $config['color'] }};
        border: 1px solid {{ $config['border'] }};
    "
    {{-- El aria-label garantiza que el lector de pantalla anuncie el estado completo --}}
    aria-label="Estado: {{ $config['label'] }}"
>
    <span aria-hidden="true">{{ $config['icon'] }}</span>
    {{ $config['label'] }}
</span>
