<?php

namespace App\Http\Requests\FacturaServicio;

use Illuminate\Foundation\Http\FormRequest;

/**
 * En una línea de factura solo se modifica la cantidad; el precio unitario
 * histórico se conserva y los totales se recalculan en el servidor.
 */
class UpdateFacturaServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cantidad'        => 'required|integer|min:1',
            'precio_unitario' => 'prohibited',
        ];
    }

    public function messages(): array
    {
        return [
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.min' => 'La cantidad debe ser al menos 1.',
            'precio_unitario.prohibited' => 'El precio unitario lo toma el servidor del servicio facturado.',
        ];
    }
}
