{{--
    Componente: <x-delete-button>
    Botón "Eliminar" que pide confirmación antes de enviar el formulario
    (DELETE). Usa el <dialog> nativo del navegador: sin librerías.

    Props:
    - action : URL del formulario (route('...destroy', $id))
    - name   : nombre del registro, para el mensaje de confirmación

    Uso: <x-delete-button :action="route('proveedores.destroy', $p->proveedor_id)" :name="$p->nombre" />
--}}
@props(['action', 'name' => 'este registro', 'label' => 'Eliminar'])

@php $idDialogo = 'eliminar-' . md5($action); @endphp

<button type="button" class="btn btn-sm btn-peligro" onclick="document.getElementById('{{ $idDialogo }}').showModal()">
    {{ $label }}
</button>

<dialog id="{{ $idDialogo }}" class="m-auto w-full max-w-md rounded-2xl border border-line bg-surface p-0 text-ink shadow-xl backdrop:bg-ink/40"
        aria-labelledby="{{ $idDialogo }}-titulo">
    <form method="POST" action="{{ $action }}" class="p-6">
        @csrf
        @method('DELETE')
        <h2 id="{{ $idDialogo }}-titulo" class="mb-2 text-lg">¿Eliminar {{ $name }}?</h2>
        <p class="mb-6 text-sm text-ink-soft">Esta acción no se puede deshacer desde la aplicación.</p>
        <div class="flex justify-end gap-2">
            <button type="button" class="btn btn-secundario" onclick="this.closest('dialog').close()">Cancelar</button>
            <button type="submit" class="btn btn-peligro">Sí, eliminar</button>
        </div>
    </form>
</dialog>
