<?php
namespace App\Http\Requests\MovimientoInventario;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', 'in:entrada,salida'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'fecha_hora' => ['required', 'date_format:Y-m-d H:i:s'],
            'motivo' => ['nullable', 'string', 'max:100'],
            'producto_id' => ['required', 'integer', 'exists:producto,producto_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'El tipo de movimiento es obligatorio.',
            'tipo.in' => 'El tipo debe ser entrada o salida.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser mínimo 1.',
            'fecha_hora.required' => 'La fecha y hora son obligatorias.',
            'fecha_hora.date_format' => 'La fecha y hora deben tener el formato Y-m-d H:i:s.',
            'motivo.string' => 'El motivo debe ser texto.',
            'motivo.max' => 'El motivo no puede superar los 100 caracteres.',
            'producto_id.required' => 'El producto es obligatorio.',
            'producto_id.integer' => 'El producto debe ser un número entero.',
            'producto_id.exists' => 'El producto no existe.',
        ];
    }
}
