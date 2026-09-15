<?php

namespace App\Http\Requests\Servicio;

use Illuminate\Foundation\Http\FormRequest;

class StoreServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],

            'duracion_min' => [
                'required',
                'integer',
                'gt:0',
            ],

            'precio' => [
                'required',
                'numeric',
                'gte:0',
            ],

            'descripcion' => [
                'nullable',
                'string',
            ],

            'activo' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del servicio es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',

            'duracion_min.required' => 'La duración del servicio es obligatoria.',
            'duracion_min.integer' => 'La duración debe ser un número entero.',
            'duracion_min.gt' => 'La duración debe ser mayor que cero.',

            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.gte' => 'El precio debe ser mayor o igual a cero.',

            'descripcion.string' => 'La descripción debe ser una cadena de texto.',

            'activo.boolean' => 'El campo activo debe ser verdadero o falso.',
        ];
    }
}