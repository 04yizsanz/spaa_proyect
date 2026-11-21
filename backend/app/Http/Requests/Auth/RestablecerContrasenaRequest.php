<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Paso 2: la persona abre el enlace del correo y escribe su nueva contraseña.
 * La contraseña debe cumplir las mismas reglas que en el registro.
 */
class RestablecerContrasenaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'     => 'El enlace no es válido. Solicita uno nuevo.',
            'email.required'     => 'Escribe tu correo electrónico.',
            'email.email'        => 'Ingresa un correo electrónico válido.',
            'password.required'  => 'Escribe tu nueva contraseña.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
