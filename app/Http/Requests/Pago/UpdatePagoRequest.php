<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_factura' => 'sometimes|exists:facturas,codigo_factura',
            'monto'          => 'sometimes|numeric|min:0',
            'metodo_pago'    => 'sometimes|string|in:efectivo,tarjeta,transferencia',
            'fecha_pago'     => 'sometimes|date',
        ];
    }
}