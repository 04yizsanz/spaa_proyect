<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario'); // ajusta si el parámetro de ruta se llama distinto

        return [
            'rol_id' => ['required', 'integer', 'exists:roles,rol_id'],
            'nombre' => ['required', 'string', 'max:80'],
            'apellido' => ['required', 'string', 'max:80'],
            'documento' => [
                'required', 'string', 'max:20',
                Rule::unique('usuarios', 'documento')->ignore($usuarioId, 'usuario_id'),
            ],
            'email' => [
                'required', 'string', 'email', 'max:150',
                Rule::unique('usuarios', 'email')->ignore($usuarioId, 'usuario_id'),
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            'estado' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'rol_id.required' => 'El rol es obligatorio.',
            'rol_id.exists' => 'El rol seleccionado no existe.',
            'documento.unique' => 'Ya existe un usuario con ese documento.',
            'email.unique' => 'Ya existe un usuario con ese correo.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}