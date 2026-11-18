{{--
    Componente: <x-page-header>
    Título de la pantalla, descripción y botones de acción a la derecha.

    Props: title, subtitle (opcional), back (URL opcional para "Volver")

    Uso:
        <x-page-header title="Proveedores" subtitle="Empresas que abastecen el spa.">
            <x-button :href="route('proveedores.create')">Nuevo proveedor</x-button>
        </x-page-header>
--}}
@props(['title', 'subtitle' => null, 'back' => null])

<div class="mb-6">
    @if ($back)
        <a href="{{ $back }}" class="mb-3 inline-flex items-center gap-1 text-sm text-ink-muted hover:text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </a>
    @endif
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-sm text-ink-soft">{{ $subtitle }}</p>
            @endif
        </div>
        @if (! $slot->isEmpty())
            <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
        @endif
    </div>
</div>
