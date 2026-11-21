<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Paso 1: la persona escribe su correo para recibir el enlace.
 */
class SolicitarRecuperacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Escribe el correo con el que te registraste.',
            'email.email'    => 'Ingresa un correo electrónico válido.',
        ];
    }
}
