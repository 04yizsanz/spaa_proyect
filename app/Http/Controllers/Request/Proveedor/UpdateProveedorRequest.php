<?php
namespace App\Http\Requests\Proveedor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'contacto' => ['sometimes', 'nullable', 'string', 'max:50'],
            'email' => ['sometimes', 'nullable', 'email', 'max:100'],
            'registro_tributario' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('proveedor', 'registro_tributario')->ignore($this->route('proveedor')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'contacto.string' => 'El contacto debe ser texto.',
            'contacto.max' => 'El contacto no puede superar los 50 caracteres.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.max' => 'El correo no puede superar los 100 caracteres.',
            'registro_tributario.required' => 'El registro tributario es obligatorio.',
            'registro_tributario.string' => 'El registro tributario debe ser texto.',
            'registro_tributario.max' => 'El registro tributario no puede superar los 20 caracteres.',
            'registro_tributario.unique' => 'El registro tributario ya está registrado.',
        ];
    }
}
