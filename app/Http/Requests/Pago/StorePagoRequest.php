<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_factura' => 'required|exists:facturas,codigo_factura',
            'monto'          => 'required|numeric|min:0',
            'metodo_pago'    => 'required|string|in:efectivo,tarjeta,transferencia',
            'fecha_pago'     => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_factura.required' => 'La factura asociada es obligatoria.',
            'codigo_factura.exists'   => 'La factura indicada no existe.',
            'monto.min'               => 'El monto no puede ser negativo.',
            'metodo_pago.in'          => 'El método de pago debe ser efectivo, tarjeta o transferencia.',
        ];
    }
}