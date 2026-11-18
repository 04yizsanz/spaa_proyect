{{--
    Componente: <x-table>
    Tabla dentro de una tarjeta, con desplazamiento horizontal en móvil
    y paginación automática si se pasa un paginador.

    Props:
    - label     : descripción para lectores de pantalla (obligatoria)
    - paginator : resultado de ->paginate() (opcional)
    Slot "head" con los <th>; slot por defecto con las filas <tr>.

    Uso:
        <x-table label="Lista de proveedores" :paginator="$proveedores">
            <x-slot:head><th>Nombre</th><th>Correo</th></x-slot:head>
            @foreach ($proveedores as $p) <tr><td>…</td></tr> @endforeach
        </x-table>
--}}
@props(['label', 'paginator' => null])

<x-card padding="none" {{ $attributes }}>
    <div class="overflow-x-auto">
        <table class="tabla" aria-label="{{ $label }}">
            <thead><tr>{{ $head }}</tr></thead>
            <tbody>{{ $slot }}</tbody>
        </table>
    </div>
    @if ($paginator && $paginator->hasPages())
        <div class="border-t border-line px-5 py-4">{{ $paginator->links() }}</div>
    @endif
</x-card>
