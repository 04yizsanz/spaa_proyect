<?php

namespace App\Http\Requests\Factura;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'sometimes|integer|exists:clientes,cliente_id',
            'fecha_hora' => 'sometimes|date',
            'subtotal' => 'sometimes|numeric|min:0',
            'impuestos' => 'sometimes|numeric|min:0',
            'total' => 'sometimes|numeric|min:0',
            'pdf_url' => 'sometimes|nullable|string|max:255',
        ];
    }
}