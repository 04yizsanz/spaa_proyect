{{--
    Componente: <x-detail>
    Fila "etiqueta: valor" para las pantallas de detalle (show).
    Debe ir dentro de un <dl class="divide-y divide-line">.

    Uso: <x-detail label="Correo">{{ $proveedor->email ?? '—' }}</x-detail>
--}}
@props(['label'])

<div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3 sm:gap-4">
    <dt class="text-sm font-medium text-ink-muted">{{ $label }}</dt>
    <dd class="text-sm text-ink sm:col-span-2">{{ $slot }}</dd>
</div>
