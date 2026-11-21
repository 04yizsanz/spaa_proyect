{{--
    Componente: <x-money>
    Muestra un valor en pesos colombianos: $ 45.000
    Uso: <x-money :value="$servicio->precio" />
--}}
@props(['value' => 0])
<span {{ $attributes->merge(['class' => 'tabular-nums']) }}>$ {{ number_format((float) $value, 0, ',', '.') }}</span>
