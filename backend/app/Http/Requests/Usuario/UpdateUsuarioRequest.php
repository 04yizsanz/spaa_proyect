<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\Rol;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Gate 'administrar-sistema': cuenta y rol activos con el permiso 'sistema.administrar'.
        return (bool) $this->user()?->can('administrar-sistema');
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario'); // ajusta si el parámetro de ruta se llama distinto
        $roleRules = ['required', 'integer', 'exists:roles,rol_id'];
        $adminRoleId = Rol::where('nombre', 'Administrador')->value('rol_id');

        if ($adminRoleId !== null && (int) $this->input('rol_id') === (int) $adminRoleId) {
            $roleRules[] = Rule::unique('usuarios', 'rol_id')
                ->ignore($usuarioId, 'usuario_id');
        }

        return [
            'rol_id' => $roleRules,
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
            'password' => ['nullable', 'string', Password::defaults()],
            'estado' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'rol_id.required' => 'El rol es obligatorio.',
            'rol_id.exists' => 'El rol seleccionado no existe.',
            'rol_id.unique' => 'Ya existe un usuario con el rol de Administrador.',
            'documento.unique' => 'Ya existe un usuario con ese documento.',
            'email.unique' => 'Ya existe un usuario con ese correo.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}