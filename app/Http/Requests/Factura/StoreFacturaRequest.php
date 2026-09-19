<?php

namespace App\Http\Requests\Factura;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|integer|exists:clientes,cliente_id',
            'fecha_hora' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'impuestos' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'pdf_url' => 'sometimes|nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'El cliente es obligatorio.',
            'cliente_id.exists' => 'El cliente indicado no existe.',
            'total.min' => 'El total no puede ser negativo.',
        ];
    }
}