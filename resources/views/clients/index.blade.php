@extends('layouts.app')

@section('title', 'Clientes')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl" style="font-family: var(--font-serif);">Clientes</h1>
        <p class="text-sm mt-1" style="color: var(--color-charcoal-light);">
            Personas registradas en el sistema.
        </p>
    </div>
</div>

@if ($modoDemo)
    <div
        class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm mb-6"
        style="background-color: var(--color-amber-light); color: #92650A; border: 1px solid var(--color-amber-soft);"
        role="status"
    >
        <span aria-hidden="true">⚠</span>
        <span><strong>Modo demostración:</strong> La base de datos no está conectada.</span>
    </div>
@endif

<x-card padding="none">
    @if ($clientes->isEmpty())
        <x-empty-state
            title="Aún no hay clientes registrados"
            message="Los clientes aparecerán aquí una vez que se registren en el sistema."
            icon="👤"
        />
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm" aria-label="Lista de clientes">
                <thead>
                    <tr style="border-bottom: 2px solid var(--color-beige); background-color: var(--color-cream);">
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--color-charcoal-muted);">
                            Nombre
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider hidden sm:table-cell"
                            style="color: var(--color-charcoal-muted);">
                            Documento
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider hidden md:table-cell"
                            style="color: var(--color-charcoal-muted);">
                            Correo
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider hidden lg:table-cell"
                            style="color: var(--color-charcoal-muted);">
                            Teléfono
                        </th>
                        <th scope="col" class="text-left px-6 py-3 text-xs font-semibold uppercase tracking-wider hidden lg:table-cell"
                            style="color: var(--color-charcoal-muted);">
                            Nacimiento
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                        <tr style="border-bottom: 1px solid var(--color-beige);">
                            <td class="px-6 py-4" style="color: var(--color-charcoal);">
                                @if ($cliente->usuario)
                                    <div class="font-medium">
                                        {{ $cliente->usuario->nombre }} {{ $cliente->usuario->apellido }}
                                    </div>
                                    <div class="text-xs" style="color: var(--color-charcoal-muted);">
                                        Cliente #{{ $cliente->cliente_id }}
                                    </div>
                                @else
                                    <span style="color: var(--color-charcoal-muted);">Sin usuario asociado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell" style="color: var(--color-charcoal-light);">
                                {{ $cliente->usuario?->documento ?? '—' }}
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell" style="color: var(--color-charcoal-light);">
                                {{ $cliente->usuario?->email ?? '—' }}
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell" style="color: var(--color-charcoal-light);">
                                {{ $cliente->usuario?->telefono ?? '—' }}
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell" style="color: var(--color-charcoal-light);">
                                {{ $cliente->fecha_nacimiento?->format('d/m/Y') ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($clientes->hasPages())
            <div class="px-6 py-4" style="border-top: 1px solid var(--color-beige);">
                {{ $clientes->links() }}
            </div>
        @endif
    @endif
</x-card>

@endsection
