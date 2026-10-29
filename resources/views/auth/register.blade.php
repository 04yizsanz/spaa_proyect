@extends('layouts.app')

@section('title', 'Crear cuenta')

@section('content')

<div class="min-h-[70vh] flex items-center justify-center py-12">
    <div class="w-full max-w-lg">

        {{-- Encabezado --}}
        <div class="text-center mb-8">
            <div
                class="mx-auto w-14 h-14 rounded-full flex items-center justify-center text-white text-2xl font-bold mb-4"
                style="background-color: var(--color-terracotta);"
                aria-hidden="true"
            >
                A
            </div>
            <h1
                class="text-3xl font-bold mb-1"
                style="font-family: var(--font-serif); color: var(--color-charcoal);"
            >
                Crear cuenta
            </h1>
            <p class="text-sm" style="color: var(--color-charcoal-muted);">
                Completa los datos para registrarte en el sistema
            </p>
        </div>

        {{-- Tarjeta del formulario --}}
        <x-card padding="lg">

            <form
                method="POST"
                action="{{ route('register') }}"
                novalidate
                aria-label="Formulario de registro"
            >
                @csrf

                <div class="flex flex-col gap-5">

                    {{-- Nombre y Apellido en fila --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form-field
                            name="nombre"
                            label="Nombre"
                            type="text"
                            placeholder="Ej: María"
                            :required="true"
                            autocomplete="given-name"
                            autofocus
                        />
                        <x-form-field
                            name="apellido"
                            label="Apellido"
                            type="text"
                            placeholder="Ej: López"
                            :required="true"
                            autocomplete="family-name"
                        />
                    </div>

                    {{-- Documento --}}
                    <x-form-field
                        name="documento"
                        label="Número de documento"
                        type="text"
                        placeholder="Ej: 1234567890"
                        :required="true"
                        autocomplete="off"
                    />

                    {{-- Correo --}}
                    <x-form-field
                        name="email"
                        label="Correo electrónico"
                        type="email"
                        placeholder="tu@correo.com"
                        :required="true"
                        autocomplete="email"
                    />

                    {{-- Teléfono --}}
                    <x-form-field
                        name="telefono"
                        label="Teléfono"
                        type="text"
                        placeholder="Ej: 3001234567"
                        :required="false"
                        autocomplete="tel"
                        help="Opcional."
                    />

                    {{-- Contraseña --}}
                    <div class="flex flex-col gap-1.5">
                        <label
                            for="field-password"
                            class="text-sm font-medium"
                            style="color: var(--color-charcoal);"
                        >
                            Contraseña
                            <span class="ml-0.5" style="color: var(--color-red-soft);" aria-label="(obligatorio)" title="Campo obligatorio">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="field-password"
                                type="password"
                                name="password"
                                autocomplete="new-password"
                                required
                                placeholder="Mínimo 8 caracteres"
                                class="block w-full"
                                style="
                                    padding: 0.5rem 2.5rem 0.5rem 0.75rem;
                                    border-radius: 0.5rem;
                                    font-size: 0.875rem;
                                    background-color: var(--color-warm-white);
                                    color: var(--color-charcoal);
                                    border: 1px solid {{ $errors->has('password') ? 'var(--color-red-soft)' : 'var(--color-beige-mid)' }};
                                    outline: none; width: 100%;
                                "
                                @if($errors->has('password')) aria-invalid="true" aria-describedby="error-password" @endif
                            >
                            <button
                                type="button"
                                id="toggle-password"
                                class="absolute inset-y-0 right-0 flex items-center px-3 cursor-pointer"
                                style="color: var(--color-charcoal-muted);"
                                aria-label="Mostrar contraseña"
                            >
                                <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true" class="hidden">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.592m2.05-1.8A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.17 5.355M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p id="error-password" class="text-xs font-medium" style="color: var(--color-red-soft);" role="alert" aria-live="polite">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirmar contraseña --}}
                    <div class="flex flex-col gap-1.5">
                        <label
                            for="field-password_confirmation"
                            class="text-sm font-medium"
                            style="color: var(--color-charcoal);"
                        >
                            Confirmar contraseña
                            <span class="ml-0.5" style="color: var(--color-red-soft);" aria-label="(obligatorio)" title="Campo obligatorio">*</span>
                        </label>
                        <input
                            id="field-password_confirmation"
                            type="password"
                            name="password_confirmation"
                            autocomplete="new-password"
                            required
                            placeholder="Repite la contraseña"
                            class="block w-full"
                            style="
                                padding: 0.5rem 0.75rem;
                                border-radius: 0.5rem;
                                font-size: 0.875rem;
                                background-color: var(--color-warm-white);
                                color: var(--color-charcoal);
                                border: 1px solid {{ $errors->has('password_confirmation') ? 'var(--color-red-soft)' : 'var(--color-beige-mid)' }};
                                outline: none; width: 100%;
                            "
                        >
                        @error('password_confirmation')
                            <p class="text-xs font-medium" style="color: var(--color-red-soft);" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botón registrar --}}
                    <x-button type="submit" variant="primary" size="lg" class="w-full justify-center mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Crear cuenta
                    </x-button>

                    {{-- Enlace a login --}}
                    <p class="text-center text-sm" style="color: var(--color-charcoal-muted);">
                        ¿Ya tienes cuenta?
                        <a
                            href="{{ route('login') }}"
                            class="font-medium"
                            style="color: var(--color-terracotta);"
                        >
                            Inicia sesión
                        </a>
                    </p>

                </div>
            </form>

        </x-card>

    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        const btn    = document.getElementById('toggle-password');
        const input  = document.getElementById('field-password');
        const eyeOn  = document.getElementById('icon-eye');
        const eyeOff = document.getElementById('icon-eye-off');
        if (!btn || !input) return;
        btn.addEventListener('click', function () {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            eyeOn.classList.toggle('hidden', isPassword);
            eyeOff.classList.toggle('hidden', !isPassword);
            btn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    })();
</script>
@endpush
