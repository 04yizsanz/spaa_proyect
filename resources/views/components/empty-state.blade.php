{{--
    Componente: <x-empty-state>

    Muestra un mensaje amable cuando no hay registros para mostrar.

    Props:
    - $title  : mensaje principal
    - $message: descripción breve (opcional)
    - $action : texto del botón de acción (opcional)
    - $href   : ruta del botón de acción (solo se muestra si existe)
    - $icon   : carácter o emoji decorativo (opcional)

    Uso:
        <x-empty-state
            title="Aún no hay citas para hoy"
            message="Cuando agendes una cita aparecerá aquí."
            action="Agendar cita"
            href="{{ route('citas.create') }}"
        />
--}}

@props([
    'title'   => 'No hay información disponible',
    'message' => null,
    'action'  => null,
    'href'    => null,
    'icon'    => '✦',
])

<div class="flex flex-col items-center justify-center py-14 px-6 text-center">
    {{-- Ícono decorativo --}}
    <div
        class="w-14 h-14 rounded-full flex items-center justify-center text-2xl mb-5"
        style="background-color: var(--color-beige); color: var(--color-charcoal-muted);"
        aria-hidden="true"
    >
        {{ $icon }}
    </div>

    {{-- Título --}}
    <h3
        class="text-base font-semibold mb-1"
        style="color: var(--color-charcoal);"
    >
        {{ $title }}
    </h3>

    {{-- Descripción --}}
    @if ($message)
        <p
            class="text-sm max-w-xs mb-5"
            style="color: var(--color-charcoal-muted);"
        >
            {{ $message }}
        </p>
    @else
        <div class="mb-5"></div>
    @endif

    {{-- Acción: solo se muestra si se pasa un $href válido --}}
    @if ($action && $href)
        <x-button :href="$href" variant="primary" size="sm">
            {{ $action }}
        </x-button>
    @endif
</div>
