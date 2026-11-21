{{--
    Componente: <x-status-badge>
    Estado de un registro con color Y texto (accesible, WCAG).

    Acepta estados de citas (pendiente, confirmada, completada, cancelada),
    de pagos (aprobado, rechazado), de inventario (entrada, salida)
    y booleanos (activo/inactivo, true/false, 1/0).

    Uso: <x-status-badge :status="$cita->estado" />
--}}
@props(['status' => 'pendiente'])

@php
    $clave = is_bool($status) ? ($status ? 'activo' : 'inactivo') : strtolower((string) $status);

    [$texto, $clases] = match ($clave) {
        'confirmada'             => ['Confirmada', 'bg-success-soft text-success'],
        'aprobado'               => ['Aprobado',   'bg-success-soft text-success'],
        'completada'             => ['Completada', 'bg-info-soft text-info'],
        'cancelada'              => ['Cancelada',  'bg-danger-soft text-danger'],
        'rechazado'              => ['Rechazado',  'bg-danger-soft text-danger'],
        'activo', 'true', '1'    => ['Activo',     'bg-success-soft text-success'],
        'inactivo', 'false', '0', '' => ['Inactivo', 'bg-subtle text-ink-muted'],
        'disponible'             => ['Disponible', 'bg-success-soft text-success'],
        'no disponible'          => ['No disponible', 'bg-subtle text-ink-muted'],
        'entrada'                => ['Entrada',    'bg-success-soft text-success'],
        'salida'                 => ['Salida',     'bg-warning-soft text-warning'],
        default                  => ['Pendiente',  'bg-warning-soft text-warning'],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold $clases"]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ $texto }}
</span>
