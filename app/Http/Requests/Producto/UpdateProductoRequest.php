<?php

namespace App\Http\Requests\Producto;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'cantidad' => ['sometimes', 'required', 'integer', 'min:0'],
            'precio' => ['sometimes', 'required', 'numeric', 'min:0'],
            'fecha_registro' => ['sometimes', 'required', 'date'],
            'proveedor_id' => ['sometimes', 'required', 'integer', 'exists:proveedor,proveedor_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad no puede ser negativa.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
            'fecha_registro.required' => 'La fecha de registro es obligatoria.',
            'fecha_registro.date' => 'La fecha de registro debe ser una fecha válida.',
            'proveedor_id.required' => 'El proveedor es obligatorio.',
            'proveedor_id.integer' => 'El proveedor debe ser un número válido.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
        ];
    }
}