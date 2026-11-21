{{--
    Componente: <x-form-field>
    Etiqueta + campo + ayuda + error de validación en un solo bloque.

    Props:
    - name, label
    - type        : text | email | password | number | date | time | datetime-local | select | textarea
    - required    : marca el campo como obligatorio
    - value       : valor inicial (se usa old() si hubo un error de validación)
    - help        : texto de ayuda bajo el campo
    - options     : ['valor' => 'texto'] para los select
    - placeholder

    Uso:
        <x-form-field name="nombre" label="Nombre" :required="true" />
        <x-form-field name="metodo" label="Método" type="select"
                      :options="['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta']" />
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
    $valorActual = old($name, $value ?? '');
    $tieneError  = $errors->has($name);
    $idCampo     = 'field-' . $name;
    $idAyuda     = $help ? 'help-' . $name : null;
    $idError     = $tieneError ? 'error-' . $name : null;
    $describe    = collect([$idAyuda, $idError])->filter()->implode(' ');
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'flex flex-col gap-1.5']) }}>
    @if ($label)
        <label for="{{ $idCampo }}" class="etiqueta">
            {{ $label }}
            @if ($required)<span class="ml-0.5 text-danger" title="Campo obligatorio">*</span>@endif
        </label>
    @endif

    @if ($type === 'select')
        <select id="{{ $idCampo }}" name="{{ $name }}" class="campo"
                @required($required) @if($tieneError) aria-invalid="true" @endif
                @if($describe) aria-describedby="{{ $describe }}" @endif
                {{ $attributes->except('class') }}>
            @if ($placeholder)
                <option value="" @selected($valorActual === '' || $valorActual === null)>{{ $placeholder }}</option>
            @endif
            @foreach ($options as $valor => $texto)
                <option value="{{ $valor }}" @selected((string) $valorActual === (string) $valor)>{{ $texto }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea id="{{ $idCampo }}" name="{{ $name }}" class="campo min-h-24 resize-y" placeholder="{{ $placeholder }}"
                  @required($required) @if($tieneError) aria-invalid="true" @endif
                  @if($describe) aria-describedby="{{ $describe }}" @endif
                  {{ $attributes->except('class') }}>{{ $valorActual }}</textarea>
    @else
        <input type="{{ $type }}" id="{{ $idCampo }}" name="{{ $name }}" class="campo"
               @if($type !== 'password') value="{{ $valorActual }}" @endif
               placeholder="{{ $placeholder }}"
               @required($required) @if($tieneError) aria-invalid="true" @endif
               @if($describe) aria-describedby="{{ $describe }}" @endif
               {{ $attributes->except('class') }}>
    @endif

    @if ($help)
        <p id="{{ $idAyuda }}" class="text-xs text-ink-muted">{{ $help }}</p>
    @endif

    @error($name)
        <p id="{{ $idError }}" class="text-xs font-medium text-danger" role="alert">{{ $message }}</p>
    @enderror
</div>
