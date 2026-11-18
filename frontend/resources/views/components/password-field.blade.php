{{--
    Componente: <x-password-field>
    Campo de contraseña con botón para mostrar u ocultar lo escrito.

    Props: name, label, required, autocomplete, help

    Uso: <x-password-field name="password" label="Contraseña" autocomplete="current-password" />
--}}
@props([
    'name'         => 'password',
    'label'        => 'Contraseña',
    'required'     => true,
    'autocomplete' => 'current-password',
    'help'         => null,
])

@php
    $id         = 'field-' . $name;
    $tieneError = $errors->has($name);
@endphp

<div class="flex flex-col gap-1.5">
    <label for="{{ $id }}" class="etiqueta">
        {{ $label }}@if($required)<span class="ml-0.5 text-danger" title="Campo obligatorio">*</span>@endif
    </label>
    <div class="relative">
        <input id="{{ $id }}" type="password" name="{{ $name }}" class="campo pr-10" autocomplete="{{ $autocomplete }}"
               @required($required) @if($tieneError) aria-invalid="true" aria-describedby="error-{{ $name }}" @endif
               {{ $attributes }}>
        <button type="button" class="absolute inset-y-0 right-0 flex items-center px-3 text-ink-muted hover:text-ink"
                aria-label="Mostrar contraseña" aria-controls="{{ $id }}"
                onclick="const c=document.getElementById('{{ $id }}');const v=c.type==='password';c.type=v?'text':'password';this.setAttribute('aria-label',v?'Ocultar contraseña':'Mostrar contraseña');">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        </button>
    </div>
    @if ($help) <p class="text-xs text-ink-muted">{{ $help }}</p> @endif
    @error($name) <p id="error-{{ $name }}" class="text-xs font-medium text-danger" role="alert">{{ $message }}</p> @enderror
</div>
