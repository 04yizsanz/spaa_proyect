{{--
    Componente: <x-empty-state>
    Mensaje amable cuando una lista no tiene registros.

    Props: title, message (opcional), action + href (botón opcional)

    Uso:
        <x-empty-state title="Registra tu primer proveedor"
                       message="Los proveedores abastecen el inventario."
                       action="Nuevo proveedor" :href="route('proveedores.create')" />
--}}
@props([
    'title'   => 'No hay información disponible',
    'message' => null,
    'action'  => null,
    'href'    => null,
])

<div class="flex flex-col items-center justify-center px-6 py-14 text-center">
    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-primary-soft text-primary" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4m16 0h-3.6a1 1 0 00-.9.55l-.5 1a1 1 0 01-.9.55h-4.2a1 1 0 01-.9-.55l-.5-1a1 1 0 00-.9-.55H4"/>
        </svg>
    </div>
    <h3 class="mb-1 font-sans text-base font-semibold">{{ $title }}</h3>
    @if ($message)
        <p class="mb-5 max-w-sm text-sm text-ink-muted">{{ $message }}</p>
    @endif
    @if ($action && $href)
        <x-button :href="$href" size="sm">{{ $action }}</x-button>
    @endif
</div>
