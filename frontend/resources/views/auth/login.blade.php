@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')

<div class="min-h-[70vh] flex items-center justify-center py-12">
    <div class="w-full max-w-md">

        {{-- Logo + título --}}
        <div class="text-center mb-8">
            <div
                class="mx-auto w-14 h-14 rounded-full flex items-center justify-center text-white text-2xl font-bold mb-4"
                style="background-color: var(--color-terracotta);"
                aria-hidden="true"
            >A</div>
            <h1 class="text-3xl font-bold mb-1" style="font-family: var(--font-serif); color: var(--color-charcoal);">
                Asha Spa
            </h1>
            <p class="text-sm" style="color: var(--color-charcoal-muted);">
                Ingresa tus credenciales para acceder al sistema
            </p>
        </div>

        {{-- Error general --}}
        @if (session('error'))
            <div class="mb-4 flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
                 style="background-color: var(--color-red-light); color: var(--color-red-soft); border: 1px solid var(--color-red-soft);"
                 role="alert">
                <span aria-hidden="true">✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <x-card padding="lg">
            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="flex flex-col gap-5">

                    {{-- Email --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-sm font-medium" style="color: var(--color-charcoal);">
                            Correo electrónico <span style="color: var(--color-red-soft);">*</span>
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            autofocus
                            placeholder="tu@correo.com"
                            class="block w-full"
                            style="
                                padding: 0.5rem 0.75rem;
                                border-radius: 0.5rem;
                                font-size: 0.875rem;
                                background-color: var(--color-warm-white);
                                color: var(--color-charcoal);
                                border: 1px solid {{ $errors->has('email') ? 'var(--color-red-soft)' : 'var(--color-beige-mid)' }};
                                outline: none; width: 100%;
                            "
                            @if($errors->has('email')) aria-invalid="true" aria-describedby="err-email" @endif
                        >
                        @error('email')
                            <p id="err-email" class="text-xs font-medium" style="color: var(--color-red-soft);" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="password" class="text-sm font-medium" style="color: var(--color-charcoal);">
                            Contraseña <span style="color: var(--color-red-soft);">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                autocomplete="current-password"
                                placeholder="••••••••"
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
                                @if($errors->has('password')) aria-invalid="true" aria-describedby="err-password" @endif
                            >
                            <button type="button" id="toggle-pwd"
                                class="absolute inset-y-0 right-0 flex items-center px-3 cursor-pointer"
                                style="color: var(--color-charcoal-muted);" aria-label="Mostrar contraseña">
                                <svg id="eye-on" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg id="eye-off" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.592m2.05-1.8A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.17 5.355M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p id="err-password" class="text-xs font-medium" style="color: var(--color-red-soft);" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Recordarme --}}
                    <div class="flex items-center gap-2">
                        <input id="remember" type="checkbox" name="remember"
                            style="accent-color: var(--color-terracotta); width:1rem; height:1rem; cursor:pointer;">
                        <label for="remember" class="text-sm cursor-pointer select-none" style="color: var(--color-charcoal-light);">
                            Mantener sesión iniciada
                        </label>
                    </div>

                    {{-- Botón --}}
                    <x-button type="submit" variant="primary" size="lg" class="w-full justify-center">
                        Iniciar sesión
                    </x-button>

                    {{-- Link a registro --}}
                    <p class="text-center text-sm" style="color: var(--color-charcoal-muted);">
                        ¿No tienes cuenta?
                        <a href="{{ route('register') }}" class="font-medium" style="color: var(--color-terracotta);">
                            Regístrate
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
(function(){
    const btn=document.getElementById('toggle-pwd');
    const inp=document.getElementById('password');
    const on=document.getElementById('eye-on');
    const off=document.getElementById('eye-off');
    if(!btn||!inp)return;
    btn.addEventListener('click',function(){
        const show=inp.type==='password';
        inp.type=show?'text':'password';
        on.classList.toggle('hidden',show);
        off.classList.toggle('hidden',!show);
        btn.setAttribute('aria-label',show?'Ocultar contraseña':'Mostrar contraseña');
    });
})();
</script>
@endpush
