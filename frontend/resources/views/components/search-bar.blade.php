{{--
    Componente: <x-search-bar>
    Barra de búsqueda y filtros por GET (la URL refleja la búsqueda activa).

    Props:
    - action      : URL de la lista
    - placeholder : texto del buscador (si es null, no se muestra el buscador)
    Slot por defecto: filtros adicionales (selects).

    Uso:
        <x-search-bar :action="route('productos.index')" placeholder="Buscar producto">
            <select name="proveedor" class="campo sm:w-56">…</select>
        </x-search-bar>
--}}
@props(['action', 'placeholder' => 'Buscar'])

<form method="GET" action="{{ $action }}" role="search" class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center">
    @if ($placeholder)
        <div class="relative flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-muted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
            <label for="buscar" class="sr-only">{{ $placeholder }}</label>
            <input id="buscar" type="search" name="buscar" value="{{ request('buscar') }}" placeholder="{{ $placeholder }}" class="campo pl-9">
        </div>
    @endif
    {{ $slot }}
    <div class="flex gap-2">
        <button type="submit" class="btn btn-secundario">Filtrar</button>
        @if (collect(request()->query())->except('page')->filter()->isNotEmpty())
            <a href="{{ $action }}" class="btn btn-fantasma">Limpiar</a>
        @endif
    </div>
</form>
