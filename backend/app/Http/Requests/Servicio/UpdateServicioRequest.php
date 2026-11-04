<?php

namespace App\Http\Requests\Servicio;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'duracion_min' => [
                'sometimes',
                'integer',
                'gt:0',
            ],

            'precio' => [
                'sometimes',
                'numeric',
                'gte:0',
            ],

            'descripcion' => [
                'nullable',
                'string',
            ],

            'activo' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',

            'duracion_min.integer' => 'La duración debe ser un número entero.',
            'duracion_min.gt' => 'La duración debe ser mayor que cero.',

            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.gte' => 'El precio debe ser mayor o igual a cero.',

            'descripcion.string' => 'La descripción debe ser una cadena de texto.',

            'activo.boolean' => 'El campo activo debe ser verdadero o falso.',
        ];
    }
}