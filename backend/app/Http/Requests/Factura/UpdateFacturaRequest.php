<?php

namespace App\Http\Requests\Factura;

use Illuminate\Foundation\Http\FormRequest;

/**
 * subtotal y total los calcula el servidor (FacturaService); no se aceptan
 * desde el cliente. La autorización la aplica FacturaPolicy en el controlador.
 */
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
            'impuestos' => 'sometimes|numeric|min:0',
            'pdf_url' => 'sometimes|nullable|string|max:255',
            'subtotal' => 'prohibited',
            'total' => 'prohibited',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'El cliente indicado no existe.',
            'impuestos.min' => 'Los impuestos no pueden ser negativos.',
            'subtotal.prohibited' => 'El subtotal lo calcula el servidor a partir del detalle de la factura.',
            'total.prohibited' => 'El total lo calcula el servidor (subtotal + impuestos).',
        ];
    }
}
