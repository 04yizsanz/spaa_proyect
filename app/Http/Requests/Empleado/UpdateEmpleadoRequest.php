<?php

namespace App\Http\Requests\Empleado;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'string', 'max:50'],
            'apellido' => ['sometimes', 'string', 'max:50'],
            'documento' => ['sometimes', 'string', 'max:20', 'unique:empleados,documento,' . $this->route('id') . ',empleado_id'],
            'correo' => ['nullable', 'email', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'rol' => ['sometimes', 'in:estilista,Estetisista,recepcionista,admin'],
            'salario' => ['sometimes', 'numeric', 'gt:0'],
            'fecha_contratacion' => ['sometimes', 'date', 'before_or_equal:today'],
            'disponibilidad' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede superar los 50 caracteres.',

            'apellido.string' => 'El apellido debe ser una cadena de texto.',
            'apellido.max' => 'El apellido no puede superar los 50 caracteres.',

            'documento.string' => 'El documento debe ser una cadena de texto.',
            'documento.max' => 'El documento no puede superar los 20 caracteres.',
            'documento.unique' => 'Ya existe un empleado registrado con ese documento.',

            'correo.email' => 'El correo electrónico debe tener un formato válido.',
            'correo.max' => 'El correo electrónico no puede superar los 100 caracteres.',

            'telefono.string' => 'El teléfono debe ser una cadena de texto.',
            'telefono.max' => 'El teléfono no puede superar los 20 caracteres.',

            'rol.in' => 'El rol seleccionado no es válido.',

            'salario.numeric' => 'El salario debe ser un valor numérico.',
            'salario.gt' => 'El salario debe ser mayor que cero.',

            'fecha_contratacion.date' => 'La fecha de contratación debe ser una fecha válida.',
            'fecha_contratacion.before_or_equal' => 'La fecha de contratación no puede ser posterior a la fecha actual.',

            'disponibilidad.boolean' => 'La disponibilidad debe ser un valor verdadero o falso.',
        ];
    }
}