<?php

namespace App\Http\Requests\Rol;

use Illuminate\Foundation\Http\FormRequest;

/**
 * La ruta está protegida por EnsureAdministrator (permiso 'sistema.administrar').
 */
class StoreRolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // La columna roles.nombre es VARCHAR(50) y única.
            'nombre' => ['required', 'string', 'max:50', 'unique:roles,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un rol con ese nombre.',
            'nombre.max' => 'El nombre del rol no puede superar los 50 caracteres.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
