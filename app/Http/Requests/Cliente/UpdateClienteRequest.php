<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clienteId = $this->route('cliente'); // ajusta si el parámetro de ruta se llama distinto

        return [
            'usuario_id' => [
                'required', 'integer', 'exists:usuarios,usuario_id',
                Rule::unique('clientes', 'usuario_id')->ignore($clienteId, 'cliente_id'),
            ],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'preferencias' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'usuario_id.required' => 'El usuario es obligatorio.',
            'usuario_id.exists' => 'El usuario seleccionado no existe.',
            'usuario_id.unique' => 'Este usuario ya tiene un cliente asociado.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        ];
    }
}