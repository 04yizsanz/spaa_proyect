{{--
    Componente: <x-toggle>
    Casilla de sí/no con apariencia de interruptor. Envía "1" si está marcada;
    el controlador lee el valor con $request->boolean('nombre').

    Uso: <x-toggle name="activo" label="Servicio activo" :checked="$servicio->activo" />
--}}
@props(['name', 'label', 'checked' => false, 'help' => null])

<label class="flex cursor-pointer items-start gap-3">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" class="peer sr-only" @checked(old($name, $checked))>
    <span class="relative mt-0.5 h-6 w-11 shrink-0 rounded-full bg-line-strong transition-colors peer-checked:bg-primary peer-focus-visible:ring-2 peer-focus-visible:ring-primary peer-focus-visible:ring-offset-2
                 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-surface after:shadow after:transition-transform peer-checked:after:translate-x-5" aria-hidden="true"></span>
    <span>
        <span class="etiqueta">{{ $label }}</span>
        @if ($help) <span class="block text-xs text-ink-muted">{{ $help }}</span> @endif
    </span>
</label>
