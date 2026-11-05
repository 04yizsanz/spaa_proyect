<?php

namespace App\Http\Requests\Rol;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // apiResource('roles') genera el parámetro {role}; la PK de la tabla es rol_id.
        $rolId = $this->route('role');

        return [
            'nombre' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('roles', 'nombre')->ignore($rolId, 'rol_id'),
            ],
            'descripcion' => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],
            'estado' => [
                'sometimes',
                'boolean'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un rol con ese nombre.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}