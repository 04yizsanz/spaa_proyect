<?php

namespace App\Http\Requests\FacturaServicio;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacturaServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'factura_id' => 'required|integer|exists:facturas,factura_id',
            'servicio_id' => 'required|integer|exists:servicios,servicio_id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'factura_id.required' => 'La factura es obligatoria.',
            'servicio_id.required' => 'El servicio es obligatorio.',
            'cantidad.min' => 'La cantidad debe ser al menos 1.',
            'precio_unitario.min' => 'El precio no puede ser negativo.',
        ];
    }
}