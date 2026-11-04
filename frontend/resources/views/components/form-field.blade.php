{{--
    Componente: <x-form-field>

    Agrupa etiqueta, campo y mensaje de validación en un bloque consistente.

    Props:
    - $name     : nombre del campo (obligatorio para asociar label y error)
    - $label    : texto de la etiqueta
    - $type     : tipo de input (text, email, date, time, number, select, textarea)
    - $required : boolean, marca el campo como obligatorio
    - $value    : valor por defecto (usa old() si no se pasa)
    - $help     : texto de ayuda opcional bajo el campo
    - $options  : array ['value' => 'label'] para campos select
    - $placeholder: texto de placeholder

    Uso:
        <x-form-field name="fecha" label="Fecha" type="date" :required="true" />

        <x-form-field
            name="estado"
            label="Estado"
            type="select"
            :options="['pendiente' => 'Pendiente', 'confirmada' => 'Confirmada']"
        />

        <x-form-field
            name="observaciones"
            label="Observaciones"
            type="textarea"
            help="Máximo 300 caracteres."
        />
--}}

@props([
    'name'        => '',
    'label'       => '',
    'type'        => 'text',
    'required'    => false,
    'value'       => null,
    'help'        => null,
    'options'     => [],
    'placeholder' => '',
])

@php
    // old() recupera el valor que el usuario ingresó antes de un error de validación
    $currentValue = old($name, $value ?? '');
    $hasError     = $errors->has($name);
    $fieldId      = 'field-' . $name;
    $helpId       = $help ? 'help-' . $name : null;
    $errorId      = $hasError ? 'error-' . $name : null;

    // Estilo base del campo
    $fieldStyle = '
        width: 100%;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        background-color: var(--color-warm-white);
        color: var(--color-charcoal);
        transition: border-color 0.15s, box-shadow 0.15s;
        outline: none;
    ';

    $borderStyle = $hasError
        ? 'border: 1px solid var(--color-red-soft);'
        : 'border: 1px solid var(--color-beige-mid);';

    $describedBy = collect([$helpId, $errorId])->filter()->implode(' ');
@endphp

<div class="flex flex-col gap-1.5">

    {{-- Etiqueta --}}
    @if ($label)
        <label
            for="{{ $fieldId }}"
            class="text-sm font-medium"
            style="color: var(--color-charcoal);"
        >
            {{ $label }}
            @if ($required)
                <span
                    class="ml-0.5"
                    style="color: var(--color-red-soft);"
                    aria-label="(obligatorio)"
                    title="Campo obligatorio"
                >*</span>
            @endif
        </label>
    @endif

    {{-- Campo --}}
    @if ($type === 'select')
        <select
            id="{{ $fieldId }}"
            name="{{ $name }}"
            class="block"
            style="{{ $fieldStyle }} {{ $borderStyle }}"
            @if($required) required @endif
            @if($hasError) aria-invalid="true" @endif
            @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->except(['class', 'style']) }}
        >
            @if ($placeholder)
                <option value="" disabled {{ $currentValue === '' ? 'selected' : '' }}>
                    {{ $placeholder }}
                </option>
            @endif
            @foreach ($options as $optVal => $optLabel)
                <option value="{{ $optVal }}" {{ (string)$currentValue === (string)$optVal ? 'selected' : '' }}>
                    {{ $optLabel }}
                </option>
            @endforeach
        </select>

    @elseif ($type === 'textarea')
        <textarea
            id="{{ $fieldId }}"
            name="{{ $name }}"
            class="block resize-y"
            style="{{ $fieldStyle }} {{ $borderStyle }} min-height: 100px;"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($hasError) aria-invalid="true" @endif
            @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->except(['class', 'style']) }}
        >{{ $currentValue }}</textarea>

    @else
        <input
            type="{{ $type }}"
            id="{{ $fieldId }}"
            name="{{ $name }}"
            value="{{ $currentValue }}"
            class="block"
            style="{{ $fieldStyle }} {{ $borderStyle }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($hasError) aria-invalid="true" @endif
            @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->except(['class', 'style']) }}
        >
    @endif

    {{-- Texto de ayuda --}}
    @if ($help)
        <p
            id="{{ $helpId }}"
            class="text-xs"
            style="color: var(--color-charcoal-muted);"
        >
            {{ $help }}
        </p>
    @endif

    {{-- Error de validación de Laravel --}}
    @error($name)
        <p
            id="{{ $errorId }}"
            class="text-xs font-medium"
            style="color: var(--color-red-soft);"
            role="alert"
            aria-live="polite"
        >
            {{ $message }}
        </p>
    @enderror

</div>
